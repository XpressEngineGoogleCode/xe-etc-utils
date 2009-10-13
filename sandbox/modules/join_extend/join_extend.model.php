<?php
    /**
     * @class  join_extendModel
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief  join_extend 모듈의 model class
     **/

    class join_extendModel extends join_extend {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 설정을 받아옴
         **/
        function getConfig() {
            $oModuleModel = &getModel('module');
            return $oModuleModel->getModuleConfig('join_extend');
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

            if ($now - $birthYear < intVal($config->age_restrictions))  return false;

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
    }
?>
