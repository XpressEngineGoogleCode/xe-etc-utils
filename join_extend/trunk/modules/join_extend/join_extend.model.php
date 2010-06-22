<?php
    /**
     * @class  join_extendModel
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief  join_extend 모듈의 model class
     **/

    class join_extendModel extends join_extend {
        
        /**
         * @brief 설정을 받아옴
         **/
        function getConfig($input_config = true) {
            if (!isset($GLOBALS['__join_extend__']['config_with_input_config']))
                $GLOBALS['__join_extend__']['config_with_input_config'] = $this->_getConfig();
            if (!isset($GLOBALS['__join_extend__']['config']))
                $GLOBALS['__join_extend__']['config'] = $this->_getConfig(false);
                
            if ($input_config)  return clone($GLOBALS['__join_extend__']['config_with_input_config']);
            else                return clone($GLOBALS['__join_extend__']['config']);
        }
        
        /**
         * @brief 설정을 받아옴
         **/
        function _getConfig($input_config = true, $editor_config = true) {
            $oModuleModel = &getModel('module');
            $_config = $oModuleModel->getModuleConfig('join_extend');
            if ($_config)   $config = clone($_config);

            // 기본값
            if (!$config->skin) $config->skin = 'default';
            if (!$config->notify_admin_collect_number)  $config->notify_admin_collect_number = 10;
            
            // 에디터 내용을 가져온다.
            if ($editor_config == true) {
                $config->agreement = $oModuleModel->getModuleConfig('join_extend_editor_agreement');
                $config->private_agreement = $oModuleModel->getModuleConfig('join_extend_editor_private_agreement');
                $config->private_gathering_agreement = $oModuleModel->getModuleConfig('join_extend_editor_private_gathering_agreement');
                $config->welcome = $oModuleModel->getModuleConfig('join_extend_editor_welcome');
                $config->welcome_email = $oModuleModel->getModuleConfig('join_extend_editor_welcome_email');
            }
            
            // 정보입력 설정을 적당히 가공한다.
            if ($input_config == true) {
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
            return clone($config);
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
            $_SESSION['join_extend_invitation'] = true;
            
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
            
            // 초대장 세션 체크
            if ($config->use_invitation == "Y" && Context::get('act') == 'procMemberInsert') {
                if (!isset($_SESSION['join_extend_invitation_srl']))  return 'session_problem';
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
                    if (!($val == "Y" || $val == "Y2")) continue;

                    // 회원가입시라면 생일 수정 금지일 때만 진행
                    if ($var_name != 'birthday' && Context::get('act') == 'procMemberInsert')   continue;
                    if ($var_name == 'birthday' && Context::get('act') == 'procMemberInsert' && $val == "Y")    continue;
                    
                    if (!isset($request_vars->{$var_name}))                     continue;
                    if (!isset($_SESSION['join_extend_no_mod'][$var_name]))     return new Object(-1, 'session_problem');
                    if (empty($_SESSION['join_extend_no_mod'][$var_name]))      continue;
                    if (is_array($_SESSION['join_extend_no_mod'][$var_name]))   $_SESSION['join_extend_no_mod'][$var_name] = implode('|@|', $_SESSION['join_extend_no_mod'][$var_name]);

                    Context::set($var_name, $_SESSION['join_extend_no_mod'][$var_name], true);
                }
            }

            // 추천인 ID 변경 대비
            if (Context::get('act') == 'procMemberModifyInfo' && !empty($config->recoid_var_name)) {
                if (!isset($_SESSION['join_extend_jumin']['recoid']))  return new Object(-1, 'session_problem');
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
        
        /**
         * @brief 에디터 내용 이전 되었는지 확인
         **/
        function isUpdateEditor() {
            $config = $this->_getConfig(false, false);
            
            if( isset($config->agreement) || 
                isset($config->private_agreement) || 
                isset($config->private_gathering_agreement) || 
                isset($config->welcome)) return false;
            
            return true;
        }
        
        /**
         * @brief 입력항목 필수 표시, 길이 제한 메시지
         **/
        function procRequiredLength() {
            $config = $this->getConfig();
            
            $str = '<script type="text/javascript"> var required = new Array();';
            if (is_array($config->input_config->required)) {
                foreach($config->input_config->required as $name => $val){
                    if ($val == "Y")    $str .= "required['$name'] = true;";
                }
            }
            $str .= '</script>';
            Context::addHtmlFooter($str);
            
            $lower_config = $config->input_config->lower_length;
            $upper_config = $config->input_config->upper_length;
            $type_config = $config->input_config->type;
            
            // 아이디, 이름, 닉네임 설명 바꾸기
            $this->changeDefaultMsg('about_user_id', 'my_about_user_id', 3, 20, $lower_config['user_id'], $upper_config['user_id']);
            $this->changeDefaultMsg('about_user_name', 'my_about_user_name', 2, 40, $lower_config['user_name'], $upper_config['user_name']);
            $this->changeDefaultMsg('about_nick_name', 'my_about_nick_name', 2, 40, $lower_config['nick_name'], $upper_config['nick_name']);
            
            // 확장 변수 설명 바꾸기
            $extend_form_list = Context::get('extend_form_list');
            $this->changeExtendMsg($extend_form_list, $lower_config, $upper_config);
            Context::set('extend_form_list', $extend_form_list);
            
            // 폼 필터에서 사용하기 위한 길이 제한 정보
            $str = '<script type="text/javascript"> var var_name = new Array(); var lower_length = new Array(); var upper_length = new Array();';
            $i = 0;
            $j = 0;
            if (is_array($type_config)) {
                foreach($type_config as $name => $val) {
                    $str .= "var_name[" .$j++ . "] = '$name';";
                    if (!$lower_config[$name] && !$upper_config[$name]) continue;
                    
                    // 기본 항목의 길이와 조합
                    switch ($name) {
                        case 'user_id':
                            if ($lower_config[$name] < 3)   $lower_config[$name] = 3;
                            if ($upper_config[$name] > 20)  $upper_config[$name] = 20;
                            break;
                        case 'user_name': case 'nick_name':
                            if ($lower_config[$name] < 2)   $lower_config[$name] = 2;
                            if ($upper_config[$name] > 40)  $upper_config[$name] = 40;
                            break;
                    }
                    
                    $str .= "lower_length['$name'] = parseInt('{$lower_config[$name]}'); upper_length['$name'] = parseInt('{$upper_config[$name]}');";
                    $i++;
                }
            }
            
            $str .= '</script>';
            Context::addHtmlFooter($str);
            
            // 이 js 파일이 가장 뒤에 실행될 수 있도록 html footer로 넣는다.
            Context::addHtmlFooter('<script type="text/javascript" src="./modules/join_extend/tpl/js/input_config.js"></script>');
            //Context::addJsFile('./modules/join_extend/tpl/js/input_config.js');
            Context::addHtmlFooter('<script type="text/javascript"> doMark(); </script>');
        }
        
        /**
         * @brief 기본항목 설명 바꾸기
         **/
        function changeDefaultMsg($og_msg, $my_msg, $default_lower_length, $default_upper_length, $lower_length, $upper_length) {
            if (!$lower_length) $lower_length = $default_lower_length;
            if (!$upper_length) $upper_length = $default_upper_length;
            
            $str = sprintf('%d~%d', $lower_length, $upper_length);
            Context::setLang($og_msg, sprintf(Context::getLang($my_msg), $str));
        }
        
        /**
         * @brief 확장변수 설명 바꾸기
         **/
        function changeExtendMsg(&$extend_form_list, $lower_config, $upper_config) {
            if (!is_array($extend_form_list))   return;
            
            foreach($extend_form_list as $no => $val) {
                if ($lower_config[$val->column_name] && $upper_config[$val->column_name])
                    $str = sprintf('%d~%d', $lower_config[$val->column_name], $upper_config[$val->column_name]);
                else if ($lower_config[$val->column_name])
                    $str = sprintf('%d~', $lower_config[$val->column_name]);
                else if ($upper_config[$val->column_name])
                    $str = sprintf('~%d', $upper_config[$val->column_name]);
                else    continue;
                
                $str = sprintf(' ' . Context::getLang('msg_length'), $str);
                $extend_form_list[$no]->description .= $str;
            }
        }
    }
?>
