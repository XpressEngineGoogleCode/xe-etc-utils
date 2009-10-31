<?php
    /**
     * @class  join_extendModel
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief  join_extend 모듈의 model class
     **/

    class join_extendModel extends join_extend {

        var $config;
        var $config_with_input_config;
        
        /**
         * @brief 초기화
         **/
        function join_extendModel() {
            $this->config_with_input_config = $this->_getConfig();
            $this->config = $this->_getConfig(false);
        }

        /**
         * @brief 설정을 받아옴
         **/
        function getConfig($input_config = true) {
            if ($input_config)  return $this->config_with_input_config;
            else                return $this->config;
        }
        
        /**
         * @brief 설정을 받아옴
         **/
        function _getConfig($input_config = true) {
            $oModuleModel = &getModel('module');
            $config = $oModuleModel->getModuleConfig('join_extend');

            // 기본값
            if (!$config->skin) $config->skin = 'default';
            
            // 정보입력 설정을 적당히 가공한다.
            if ($input_config) {
                $array_config = get_object_vars($config);
                if (is_array($array_config)) {
                    foreach($array_config as $name => $val) {
                        // 필수항목
                        $res = preg_match("/^(.+)_required$/", $name, $matches);
                        if ($res)   $required[$matches[1]] = $val;
                        
                        // 수정금지
                        $res = preg_match("/^(.+)_no_mod$/", $name, $matches);
                        if ($res)   $no_mod[$matches[1]] = $val;
                        
                        // 최소길이
                        $res = preg_match("/^(.+)_lower_length$/", $name, $matches);
                        if ($res)   $lower_length[$matches[1]] = $val;
                        
                        // 최대 길이
                        $res = preg_match("/^(.+)_upper_length$/", $name, $matches);
                        if ($res)   $upper_length[$matches[1]] = $val;
                        
                        // 종류
                        $res = preg_match("/^(.+)_type$/", $name, $matches);
                        if ($res)   $type[$matches[1]] = $val;
                    }
                }
                $config->input_config->required = $required;
                $config->input_config->no_mod = $no_mod;
                $config->input_config->lower_length = $lower_length;
                $config->input_config->upper_length = $upper_length;
                $config->input_config->type = $type;
            }
            return $config;
        }

        /**
         * @brief 올바른 주민번호인지 확인
         **/
        function isValid() {
            $config = $this->getConfig();
            if ($config->use_jumin != 'Y')  return true;

            // 일단 정규식과 주민번호 규칙을 이용하여 검사
            $name = Context::get('name');
            $resno1 = Context::get('jumin1');
            $resno2 = Context::get('jumin2');

            $resno = $resno1 . $resno2; 

            // 형태 검사: 총 13자리의 숫자, 7번째는 1..4의 값을 가짐 
            if (!ereg('^[[:digit:]]{6}[1-4][[:digit:]]{6}$', $resno)) 
                return false; 
            
            // 날짜 유효성 검사 
            $birthYear = ('2' >= $resno[6]) ? '19' : '20'; 
            $birthYear .= substr($resno, 0, 2); 
            $birthMonth = substr($resno, 2, 2); 
            $birthDate = substr($resno, 4, 2); 
            if (!checkdate($birthMonth, $birthDate, $birthYear)) 
                return false; 
            
            // Checksum 코드의 유효성 검사 
            for ($i = 0; $i < 13; $i++) $buf[$i] = (int) $resno[$i]; 
            $multipliers = array(2,3,4,5,6,7,8,9,2,3,4,5); 
            for ($i = $sum = 0; $i < 12; $i++) $sum += ($buf[$i] *= $multipliers[$i]); 
            if ((11 - ($sum % 11)) % 10 != $buf[12]) 
                return false; 
            
            // 실명인증 등 외부 모듈과 연동은 아래 파일에 boolean값을 return 하도록 작성하시면 됩니다.
            // 이름은 $name, 주민번호 앞자리는 $resno1, 뒷자리는 $resno2 입니다.
            $out_result = (@include("outmodule.php"));
            if (!$out_result)   return false;

            // 모든 검사를 통과하면 유효한 주민등록번호임 
            return true; 
        }
        
        /**
         * @brief 성별 검사
         **/
        function isSex()
        {
            $config = $this->getConfig();
            if ($config->use_sex_restrictions != "M" && $config->use_sex_restrictions != "W")   return true;
            if ($config->use_jumin != "Y")  return true;
            
            $sex_code = substr(Context::get('jumin2'), 0, 1);

            if ($sex_code == '1' || $sex_code == '3')   $sex = "M";
            else                                        $sex = "W";

            if ($config->use_sex_restrictions != $sex)  return false;

            return true;
        }
        
        /**
         * @brief 나이제한 검사
         **/
        function isAge()
        {
            $config = $this->getConfig();

            if ($config->use_age_restrictions != "Y")   return true;
            if ($config->use_jumin != "Y")  return true;

            $birthYear = (2 >= intVal(substr(Context::get('jumin2'), 0, 1))) ? 1900 : 2000; 
            $birthYear += intVal(substr(Context::get('jumin1'), 0, 2));

            $now = intVal(date('Y'));
            $age = $now - $birthYear;
            $low = intVal($config->age_restrictions);
            $up = intVal($config->age_upper_restrictions);
            if (!$up)   $up = 999;
            if ($age < $low || $age > $up)  return false;
            
            return true;
        }

        /**
         * @brief 중복인지 검사
         **/
        function isDuplicate()
        {
            $config = $this->getConfig();
            if ($config->use_jumin != "Y")  return false;

            $resno1 = Context::get('jumin1');
            $resno2 = Context::get('jumin2');
            
            $args->jumin = md5($resno1 . '-' . $resno2);

            $output = executeQuery('join_extend.isDuplicate', $args);
            if (!$output->toBool())  return true;
            
            if ($output->data->count)   return true;
            
            return false;
        }

        /**
         * @brief 세션 만들기
         **/
        function createSession()
        {
            $_SESSION['join_extend_authed'] = true;
            
            $config = $this->getConfig();
            if ($config->use_jumin != "Y")  return;
                        
            $jumin1 = Context::get('jumin1');
            $jumin2 = Context::get('jumin2');
            
            // 이름과 해시된 주민번호
            $_SESSION['join_extend_jumin']['name'] = Context::get('name');
            $_SESSION['join_extend_jumin']['jumin'] = md5($jumin1 . '-' . $jumin2);

            // 생년월일
            $birthYear = ('2' >= $jumin2[0]) ? '19' : '20';
            $birthYear .= substr($jumin1, 0, 2);
            $birthMonth = substr($jumin1, 2, 2);
            $birthDate = substr($jumin1, 4, 2);
            
            $_SESSION['join_extend_jumin']['birthday'] = $birthYear . $birthMonth . $birthDate;
            $_SESSION['join_extend_jumin']['birthday2'] = sprintf("%s-%s-%s", $birthYear, $birthMonth, $birthDate);
            
            // 성별 정보
            if (!empty($config->sex_var_name)) {
                if ($jumin2[0] == '1' || $jumin2[0] == '3') $sex = $config->man_value;
                else                                        $sex = $config->woman_value;
                $_SESSION['join_extend_jumin']['sex'] = $sex;
            }
            
            // 나이 정보
            if (!empty($config->age_var_name)) {
                $birthYear = (2 >= intVal(substr($jumin2, 0, 1))) ? 1900 : 2000; 
                $birthYear += intVal(substr($jumin1, 0, 2));
                $now = intVal(date('Y'));
                $age = $now - $birthYear + 1;
                $_SESSION['join_extend_jumin']['age'] = $age;
            }
        }
        
        /**
         * @brief 세션 체크
         **/
        function checkSession() {
            // 모듈 옵션
			$config = $this->getConfig();

			// 혹시나 있을 이름 변경에 대비
			if ($config->use_jumin == "Y") {
			    if (!isset($_SESSION['join_extend_jumin']['name']))  return 'session_problem';
				Context::set('user_name', $_SESSION['join_extend_jumin']['name'], true);
			}
			
			// 혹시나 있을 나이 변경에 대비
			if ($config->use_jumin == "Y" && !empty($config->age_var_name)) {
			    if (!isset($_SESSION['join_extend_jumin']['age']))  return 'session_problem';
				Context::set($config->age_var_name, $_SESSION['join_extend_jumin']['age'], true);
			}

			// 혹시나 있을 성별 변경에 대비
			if ($config->use_jumin == "Y" && !empty($config->sex_var_name)) {
			    if (!isset($_SESSION['join_extend_jumin']['sex']))  return 'session_problem';
				Context::set($config->sex_var_name, $_SESSION['join_extend_jumin']['sex'], true);
			}
			
			return false;
        }
        
        /**
         * @brief 입력항목체크
         **/
        function checkInput() {
            $config = $this->getConfig();
            $lang_filter = Context::getLang('filter');
            $request_vars = Context::getRequestVars();
            $array_request_vars = get_object_vars($request_vars);
            if (!count($array_request_vars))    return new Object();
            
            foreach($array_request_vars as $var_name => $val) {
                // 필수 체크
                if ($config->input_config->required[$var_name] == "Y" && empty($val)) {
                    return new Object(-1, sprintf($lang_filter->isnull, Context::getLang($var_name)));
                }
                
                // 길이 체크
                if ($config->input_config->type[$var_name] == 'text' && !empty($val)) {
                    if (!intVal($config->input_config->upper_length[$var_name])) $config->input_config->upper_length[$var_name] = 999;
                    if (intVal($config->input_config->lower_length[$var_name]) > mb_strlen($val, 'utf-8') || intVal($config->input_config->upper_length[$var_name]) < mb_strlen($val, 'utf-8')) {
                        if (!intVal($config->input_config->lower_length[$var_name]))            $length_info = "(~{$config->input_config->upper_length[$var_name]})";
                        else if (intVal($config->input_config->upper_length[$var_name]) == 999) $length_info = "({$config->input_config->lower_length[$var_name]}~)";
                        else                                                                    $length_info = "({$config->input_config->lower_length[$var_name]}~{$config->input_config->upper_length[$var_name]})";
                        
                        return new Object(-1, sprintf($lang_filter->outofrange, Context::getLang($var_name)). $length_info);
                    }
                }
            }
            
            return new Object();
        }
        
        /**
         * @brief 수정금지 입력항목 세션 체크
         **/
        function checkInputMod() {
            if (!count($_SESSION['join_extend_no_mod']))    return new Object();

            $config = $this->getConfig();

            $request_vars = Context::getRequestVars();
            if (count($config->input_config->no_mod)) {
                foreach($config->input_config->no_mod as $var_name => $val) {
                    if ($val != "Y") continue;

                    if (!isset($request_vars->{$var_name}))                     continue;
                    if (!isset($_SESSION['join_extend_no_mod'][$var_name]))     return new Object(-1, 'session_problem');
                    if (empty($_SESSION['join_extend_no_mod'][$var_name]))      continue;
                    if (is_array($_SESSION['join_extend_no_mod'][$var_name]))   $_SESSION['join_extend_no_mod'][$var_name] = implode('|@|', $_SESSION['join_extend_no_mod'][$var_name]);

				    Context::set($var_name, $_SESSION['join_extend_no_mod'][$var_name], true);
                }
            }

			// 추천인 ID 변경 대비
			if (!empty($config->recoid_var_name)) {
                if (!isset($_SESSION['join_extend_jumin']['recoid']))  return 'session_problem';
				Context::set($config->recoid_var_name, $_SESSION['join_extend_jumin']['recoid'], true);
			}
			
            return new Object();
        }
        
        /**
         * @brief 주민등록번호 테이블 이전 되었는지 확인
         **/
        function isUpdateTable() {
            $oDB = &DB::getInstance();
            
            if($oDB->isColumnExists("member","jumin")) return false;
            
            return true;
        }
    }
?>
