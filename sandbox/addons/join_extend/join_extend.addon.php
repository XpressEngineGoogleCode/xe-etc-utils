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

				// 주민번호를 입력받으면 이름을 고정시킨다.
				if ($config->use_jumin == "Y") {
					Context::addHtmlHeader(sprintf('<script type="text/javascript"> var user_name ="%s";  </script>', $_SESSION['join_extend_jumin']['name']));
					Context::addJsFile('./modules/join_extend/tpl/js/fix_name.js',false);
				}

    			unset($_SESSION['join_extend_authed']);
                $_SESSION['join_extend_authed_act'] = true;
            }

		// 회원가입 완료 후 주민번호 입력
		}else if (in_array(Context::get('act'),array('procMemberInsert'))) {
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
			}
		// 회원가입 벗어나면 주민번호 정보를 필히 삭제
		}else if (strpos(Context::get('act'), 'procMember') === false && Context::getResponseMethod() == 'HTML'){
			unset($_SESSION['join_extend_authed_act']);
			unset($_SESSION['join_extend_jumin']);
		}
	}
?>