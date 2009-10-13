<?php
    if(!defined("__ZBXE__")) exit();

    /**
     * @file join_extend.addon.php
     * @author sol (sol@ngleader.com), 난다날아 (sinsy200@gmail.com)
     * @brief 회원 가입 화면 출력시 (dispMemberSignUpForm) 14세 이상/미만 구분, 이용약관, 주민등록번호 입력 출력
     **/

    if($called_position == 'before_module_init'){

		// 실제 가입시 체크
		if(Context::get('act')=='procMemberInsert'){
			// session 체크
			if(!$_SESSION['join_extend_authed_act']){
				$this->error = "msg_not_permitted";
			}

			// 모듈 옵션
			$oMJExtendModel = &getModel('join_extend');
			$config = $oMJExtendModel->getConfig();

			// 혹시나 있을 이름 변경에 대비
			if ($config->use_jumin == "Y") {
				Context::set('user_name', $_SESSION['join_extend_jumin']['name']);
			}

		// 회원 정보 수정 시
		}else if (Context::get('act') == 'procMemberModifyInfo') {
			// 모듈 옵션
			$oMJExtendModel = &getModel('join_extend');
			$config = $oMJExtendModel->getConfig();

			// 혹시나 있을 이름 변경에 대비
			if ($config->use_jumin == "Y") {
				Context::set('user_name', $_SESSION['join_extend_jumin']['name']);
			}
		}

	} else if($called_position == 'after_module_proc') {

		if(Context::get('act') == "dispMemberSignUpForm"){
			if(!$_SESSION['join_extend_authed']){

				// 모듈 옵션
				$oMJExtendModel = &getModel('join_extend');
				$config = $oMJExtendModel->getConfig();
				Context::set('config', $config);

				// load addon lang 
				Context::loadLang(_XE_PATH_.'modules/join_extend/lang');
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
				
				$this->setTemplatePath($addon_tpl_path);
				$this->setTemplateFile($addon_tpl_file);

				unset($_SESSION['join_extend_jumin']);
			}else{
				// 모듈 옵션
				$oMJExtendModel = &getModel('join_extend');
				$config = $oMJExtendModel->getConfig();

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

		// 회원가입 완료 후 주민번호 입력
		}else if (Context::get('act') == 'procMemberInsert') {
			// 회원가입이 안 됐으면 생략
			if(is_a($output, 'Object') || is_subclass_of($output, 'Object')) return;

			$member_srl = $this->get('member_srl');

			$oMJExtendController = &getController('join_extend');
			$oMemberController = &getController('member');
			
			$res = $oMJExtendController->procJoin_extendJuminInsert($member_srl);
			
			// 주민번호 입력에 실패하면 회원가입을 취소
			if (!$res){
				Context::loadLang(_XE_PATH_.'modules/join_extend/lang');
				$oMemberController->deleteMember($logged_info->member_srl);
				$output = new Object(-1, 'insert_fail_jumin');
				return;
			}
			
			unset($_SESSION['join_extend_authed_act']);
			unset($_SESSION['join_extend_jumin']);

		// 회원 정보 수정 화면 주민번호 사용시 이름 변경 금지!
		}else if (Context::get('act') == 'dispMemberModifyInfo'){
				// 모듈 옵션
				$oMJExtendModel = &getModel('join_extend');
				$config = $oMJExtendModel->getConfig();
				$member_info = Context::get('member_info');
				
				if ($config->use_jumin == "Y" && !empty($config->sex_var_name) && !empty($member_info->{$config->sex_var_name})) {
				    Context::addHtmlHeader(sprintf('<script type="text/javascript"> var sex_var_name ="%s"; </script>', $config->sex_var_name));
				}
				
				if ($config->use_jumin == "Y") {
					Context::addHtmlHeader(sprintf('<script type="text/javascript"> var user_name ="%s";  </script>', $member_info->user_name));
					Context::addJsFile('./modules/join_extend/tpl/js/fix_name.js',false);
					$_SESSION['join_extend_jumin']['name'] = $member_info->user_name;
				}
		}
	}
?>