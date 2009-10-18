<?php
    /**
     * @class  join_extendController
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief  join_extend모듈의 controller class
     **/

    class join_extendController extends join_extend {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 동의
         **/
        function procJoin_extendAgree() {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            
            // 중복 확인
            $result = $oJoinExtendModel->isDuplicate();
            if ($result)    return $this->stop('jumin_exist');

            // 성별 확인
            $result = $oJoinExtendModel->isSex();
            if (!$result) {
                return $this->stop(sprintf(Context::getLang('sex_restrictions'), $config->use_sex_restrictions=='M'?Context::getLang('man'):Context::getLang('woman')));
            }
            
            // 나이제한 확인
            $result = $oJoinExtendModel->isAge();
            if (!$result) {
                return $this->stop(sprintf(Context::getLang('msg_age_restrictions'), $config->age_restrictions));
            }

            // 주민번호 확인
            $result = $oJoinExtendModel->isValid();
            if (!$result)   return $this->stop('invaild_jumin');

            // session 추가
            $oJoinExtendModel->createSession();

            // xml_rpc return
            header("Content-Type: text/xml; charset=UTF-8");
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            print("<response>\r\n<error>0</error>\r\n<message>success</message>\r\n</response>");

            Context::close();
            exit();
        }

        /**
         * @brief 주민번호 입력
         **/
        function procJoin_extendJuminInsert($member_srl) {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            if ($config->use_jumin != "Y")  return true;
            if ($config->save_jumin != "Y") return true;

            if (!$member_srl) return false;
            $args->jumin = $_SESSION['join_extend_jumin']['jumin'];
            $args->member_srl = $member_srl;

            $output = executeQuery('join_extend.insertJumin', $args);
            if (!$output->toBool())  return false;
            
            return true;
        }
        
        /**
         * @brief 추천인 포인트 지급
         **/
        function procJoin_extendRecommender($member_srl) {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            if (empty($config->recoid_var_name))    return true;
            
            // 포인트 컨트롤러
            $oPointController = &getController('point');
            
            // 추천인
            $recoid = Context::get($config->recoid_var_name);
            if (empty($recoid)) return true;
            
            $oMemberModel = &getModel('member');
            $recoid_info = $oMemberModel->getMemberInfoByUserID($recoid);
            if (!$recoid_info)  return false;
            
            // 추천인 포인트 지급
            if (intVal($config->recoid_point)) {
                $oPointController->setPoint($recoid_info->member_srl, intVal($config->recoid_point), 'add');
            }
            
            // 본인 포인트 지급
            if (intVal($config->joinid_point)) {
                $oPointController->setPoint($member_srl, intVal($config->joinid_point), 'add');
            }
            
            return true;
        }

        /**
         * @brief 추천인 포인트 지급
         **/
        function triggerInsertMember(&$obj) {
            $member_srl = $obj->member_srl;
			$oMemberController = &getController('member');
			
			$res = $this->procJoin_extendJuminInsert($member_srl);
			
			// 주민번호 입력에 실패하면 회원가입을 취소
			if (!$res){
				$oMemberController->deleteMember($member_srl);
				return new Object(-1, 'insert_fail_jumin');
			}
			
			// 추천인 포인트 지급
			$res = $this->procJoin_extendRecommender($member_srl);
			
			// 포인트 지급에 실패하면 회원가입을 취소
			if (!$res){
				$oMemberController->deleteMember($member_srl);
				return new Object(-1, 'point_fail');
			}
			
			unset($_SESSION['join_extend_authed_act']);
			unset($_SESSION['join_extend_jumin']);
			
			return new Object();
        }
    }
?>
