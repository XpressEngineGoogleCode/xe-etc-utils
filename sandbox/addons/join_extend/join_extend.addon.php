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

            // 세션 체크
			$oMJExtendModel = &getModel('join_extend');
			$res = $oMJExtendModel->checkSession();
			if ($res)   $this->error = $res;
			
		// 회원 정보 수정 시
		}else if (Context::get('act') == 'procMemberModifyInfo') {
            // 세션 체크
			$oMJExtendModel = &getModel('join_extend');
			$oMJExtendModel->checkSession();
			if ($res)   $this->error = $res;
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
	
	// 회원가입 폼, 추천인 아이디 사용시 설명 문구 변경
	}else if ($called_position == 'before_display_content' && Context::getResponseMethod() == 'HTML' && in_array(Context::get('act'), array("dispMemberSignUpForm", "dispMemberModifyInfo"))) {
	    $oMJExtendModel = &getModel('join_extend');
	    $config = $oMJExtendModel->getConfig();
	    if (empty($config->recoid_var_name))    return;
	    
	    // 추천인 포인트
	    $output = str_replace('$recoid_point', intVal($config->recoid_point), $output);
	    
	    // 추천 포인트
	    $output = str_replace('$joinid_point', intVal($config->joinid_point), $output);
	    
	    // 포인트 단위
	    $oModuleModel = &getModel('module');
        $point_config = $oModuleModel->getModuleConfig('point');
	    $output = str_replace('$point_name', $point_config->point_name, $output);
	}
?>