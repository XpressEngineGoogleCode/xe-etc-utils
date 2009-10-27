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
         * @brief 회원 DB 추가 후 트리거
         **/
        function triggerInsertMember(&$obj) {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            if ($config->use_join_extend != 'Y')    return new Object();
            
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
         * @brief 모듈핸들러 실행 후 트리거 (애드온의 after_module_proc에 대응)
         **/
        function triggerModuleHandlerProc(&$oModule) {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            if ($config->use_join_extend != 'Y')    return new Object();
            
            if(Context::get('act') == "dispMemberSignUpForm"){
    			if(!$_SESSION['join_extend_authed']){
    
    				// 모듈 옵션
    				$oMJExtendModel = &getModel('join_extend');
    				$config = $oMJExtendModel->getConfig();
    				Context::set('config', $config);
    
    				Context::addHtmlHeader(sprintf('<script type="text/javascript"> var msg_junior_join ="%s"; var msg_check_agree ="%s"; var msg_empty_name = "%s"; var msg_empty_jumin1 = "%s"; var msg_empty_jumin2 = "%s"; var use_jumin = "%s"; var about_user_name = "%s"; </script>',
    					trim($config->msg_junior_join),Context::getLang('msg_check_agree'), 
    					sprintf(Context::getLang('msg_empty'), Context::getLang('name')),
    					sprintf(Context::getLang('msg_empty'), Context::getLang('jumin1')),
    					sprintf(Context::getLang('msg_empty'), Context::getLang('jumin2')),
    					$config->use_jumin,
    					Context::getLang('about_user_name')
    				));
    
    				// change module template
    				Context::addJsFile('./modules/join_extend/tpl/js/member_join_extend.js',false);
    				if ($config->skin)
    					$addon_tpl_path = sprintf('./modules/join_extend/skins/%s/', $config->skin);
    				else
    					$addon_tpl_path = './modules/join_extend/skins/default/';
    
    				$addon_tpl_file = 'member_join_extend.html';
    				
    				$oModule->setTemplatePath($addon_tpl_path);
    				$oModule->setTemplateFile($addon_tpl_file);

    				unset($_SESSION['join_extend_jumin']);
    			}else{
    				// 모듈 옵션
    				$oMJExtendModel = &getModel('join_extend');
    				$config = $oMJExtendModel->getConfig();
    
                    // 추천인 아이디
                    if (!empty($config->recoid_var_name) && Context::get('recoid')) {
                        Context::addHtmlHeader(sprintf('<script type="text/javascript"> var recoid_var_name2 ="%s"; var recoid = "%s"; </script>', 
                                                        $config->recoid_var_name,
    					                                Context::get('recoid')));
                    }
                    
                    // 주민번호를 입력받고 성별 정보가 있으면 자동으로 선택한다.
    				if ($config->use_jumin == "Y" && !empty($config->sex_var_name)) {
    				    Context::addHtmlHeader(sprintf('<script type="text/javascript"> var sex_var_name ="%s"; var sex = "%s"; </script>', 
    				                                    $config->sex_var_name,
    					                                $_SESSION['join_extend_jumin']['sex']));
    				}
    				
    				// 주민번호를 입력받고 나이 정보가 있으면 자동으로 입력한다.
    				if ($config->use_jumin == "Y" && !empty($config->age_var_name)){
    				    Context::addHtmlHeader(sprintf('<script type="text/javascript"> var age_var_name ="%s"; var age = "%s"; </script>', 
    				                                    $config->age_var_name,
    					                                $_SESSION['join_extend_jumin']['age']));
    				}
    				
    				// 주민번호를 입력받으면 이름을 고정시킨다.
    				if ($config->use_jumin == "Y") {
    					Context::addHtmlHeader(sprintf('<script type="text/javascript"> var user_name ="%s"; var birthday = "%s"; var birthday2 = "%s"; </script>', 
    					                                $_SESSION['join_extend_jumin']['name'],
    					                                $_SESSION['join_extend_jumin']['birthday'],
    					                                $_SESSION['join_extend_jumin']['birthday2']));
    					Context::addJsFile('./modules/join_extend/tpl/js/fix_name.js',false);
    				}
    
        			unset($_SESSION['join_extend_authed']);
                    $_SESSION['join_extend_authed_act'] = true;
                }
    
    		// 회원 정보 수정 화면 주민번호 사용시 이름 변경 금지!
    		}else if (Context::get('act') == 'dispMemberModifyInfo'){
    				// 모듈 옵션
    				$oMJExtendModel = &getModel('join_extend');
    				$config = $oMJExtendModel->getConfig();
    				$member_info = Context::get('member_info');
    				
    				if (!empty($config->recoid_var_name)) {
    				    Context::addHtmlHeader(sprintf('<script type="text/javascript"> var recoid_var_name ="%s"; </script>', $config->recoid_var_name));
    				}
    
    				if (!empty($config->age_var_name)) {
    				    Context::addHtmlHeader(sprintf('<script type="text/javascript"> var age_var_name ="%s"; </script>', $config->age_var_name));
    				    $_SESSION['join_extend_jumin']['age'] = $member_info->{$config->age_var_name};
    				}				
    				
    				if ($config->use_jumin == "Y" && !empty($config->sex_var_name) && !empty($member_info->{$config->sex_var_name})) {
    				    Context::addHtmlHeader(sprintf('<script type="text/javascript"> var sex_var_name ="%s"; </script>', $config->sex_var_name));
    				    $_SESSION['join_extend_jumin']['sex'] = $member_info->{$config->sex_var_name};
    				}
    				
    				if ($config->use_jumin == "Y") {
    					Context::addHtmlHeader(sprintf('<script type="text/javascript"> var user_name ="%s";  </script>', $member_info->user_name));
    					Context::addJsFile('./modules/join_extend/tpl/js/fix_name.js',false);
    					$_SESSION['join_extend_jumin']['name'] = $member_info->user_name;
    				}
    				
    		}
		
            return new Object();
        }
        
        /**
         * @brief 모듈 핸들러 초기화 후 트리거 (애드온의 before_module_proc에 대응)
         **/
        function triggerModuleHandlerInit(&$module_info){
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            if ($config->use_join_extend != 'Y')    return new Object();
            
			// 실제 가입시 체크
    		if(Context::get('act')=='procMemberInsert'){
    			// session 체크
    			if(!$_SESSION['join_extend_authed_act']){
    			    $this->xmlMessage('msg_not_permitted');
    			}
    
                // 세션 체크
    			$oMJExtendModel = &getModel('join_extend');
    			$res = $oMJExtendModel->checkSession();

    			if ($res)   $this->xmlMessage($res);
    			
    		// 회원 정보 수정 시
    		}else if (Context::get('act') == 'procMemberModifyInfo') {
                // 세션 체크
    			$oMJExtendModel = &getModel('join_extend');
    			$res = $oMJExtendModel->checkSession();
    			if ($res)   $this->xmlMessage($res);
    		}
    		
			return new Object();
			
        }
        
        /**
         * @brief 출력 전 트리거 (애드온의 before_display_content에 대응)
         **/
        function triggerDisplay(&$output){
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            if ($config->use_join_extend != 'Y')    return new Object();
            
            if (Context::getResponseMethod() == 'HTML' && in_array(Context::get('act'), array("dispMemberSignUpForm", "dispMemberModifyInfo"))) {
        	    $oMJExtendModel = &getModel('join_extend');
        	    $config = $oMJExtendModel->getConfig();
        	    if (empty($config->recoid_var_name))    return new Object();
        	    
        	    // 추천인 포인트
        	    $output = str_replace('$recoid_point', intVal($config->recoid_point), $output);
        	    
        	    // 추천 포인트
        	    $output = str_replace('$joinid_point', intVal($config->joinid_point), $output);
        	    
        	    // 포인트 단위
        	    $oModuleModel = &getModel('module');
                $point_config = $oModuleModel->getModuleConfig('point');
        	    $output = str_replace('$point_name', $point_config->point_name, $output);
        	}
        	
        	return new Object();
        }
        
        /**
         * @brief XML RPC 메시지 출력
         **/
        function xmlMessage($msg){
            header("Content-Type: text/xml; charset=UTF-8");
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			printf("<response>\r\n<error>-1</error>\r\n<message>%s</message>\r\n</response>", Context::getLang($msg));

			Context::close();
			exit();
        }
    }
?>
