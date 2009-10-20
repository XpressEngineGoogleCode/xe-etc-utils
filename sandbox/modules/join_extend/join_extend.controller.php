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
        
        /**
         * @brief 메인 홈페이지의 애드온 설정을 모든 가상 사이트에 동기화 시키기 위한 애드온
         **/
        function triggerModuleHandlerProc($oModule) {
            $site_module_info = Context::get('site_module_info');

            // 메인홈페이지에서의 회원가입 확장 애드온 사용 토글에 대한 동작인지 확인
            if ($oModule->act != 'procAddonAdminToggleActivate' || Context::get('addon') != 'join_extend' || $site_module_info->site_srl != 0)    return new Object();
            
            // 활성화 상태 확인
            $oAddonModel = &getAdminModel('addon');
            $oAddonAdminController = &getAdminController('addon');
            $is_active = $oAddonModel->isActivatedAddon('join_extend', 0);
            
            // 모든 가상 사이트 목록을 가져온다
            $output = executeQuery('join_extend.getSiteList');
            if (!$output->toBool()) return new Object();
            
            // 각 가상 사이트를 돌면서 애드온 활성화 상태를 전파
            if (count($output->data)) {
                foreach($output->data as $val) {
                    if (!$val->site_srl) continue;
                    
                    // 해당 가상 사이트의 회원가입 확장 애드온 상태 검사후 다르면 토글
                    if ($oAddonModel->isActivatedAddon('join_extend', $val->site_srl) != $is_active) {
                        if ($is_active) $oAddonAdminController->doActivate('join_extend', $val->site_srl);
                        else            $oAddonAdminController->doDeactivate('join_extend', $val->site_srl);
                        $oAddonAdminController->makeCacheFile($val->site_srl);
                    }
                }
            }
            
            return new Object();
        }
        
        /**
         * @brief 각 카페에서 개별적으로 애드온 설정을 하지 못하도록 하기 위한 트리거
         **/
        function triggerModuleHandlerInit($module_info){
            $site_module_info = Context::get('site_module_info');
            
            // 가상 사이트에서의 화원가입 확장 애드온을 사용 토글에 대한 동작인지 확인
            if (Context::get('act') == 'procAddonAdminToggleActivate' && Context::get('addon') == 'join_extend' && $site_module_info->site_srl != 0) {
            
                // XE에서 xml 반환 시켜주지 않는다...
                //return new Object(-1, 'msg_not_permitted');
                
                // 직접 xml 반환
                header("Content-Type: text/xml; charset=UTF-8");
    			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    			header("Cache-Control: no-store, no-cache, must-revalidate");
    			header("Cache-Control: post-check=0, pre-check=0", false);
    			header("Pragma: no-cache");
    			printf("<response>\r\n<error>-1</error>\r\n<message>%s</message>\r\n</response>", Context::getLang('msg_not_permitted'));
    
    			Context::close();
    			exit();
			}
			
			// 가상 사이트에서의 애드온 설정
			if (Context::get('act') == 'dispAddonAdminSetup' && Context::get('selected_addon') == 'join_extend' && $site_module_info->site_srl != 0) {
			    return new Object(-1, 'msg_not_permitted');
			}
			
			return new Object();
			
        }
    }
?>
