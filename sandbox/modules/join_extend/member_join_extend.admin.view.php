<?php
	/**
	 * @class  member_join_extendAdminView
	 * @author 난다날아 (sinsy200@gmail.com)
	 * @brief  member_join_extend모듈의 admin view class
	 **/

	class member_join_extendAdminView extends member_join_extend {

		/**
		 * @brief 초기화
		 **/
		function init() {
		}

		/**
		 * @brief 모듈 설정 화면
		 **/
		function dispMember_join_extendAdminIndex() {
			$oMJExtendModel = &getModel('member_join_extend');
            $config = $oMJExtendModel->getConfig();
            Context::set('config',$config);

			// 스킨 목록을 구해옴
			$oModuleModel = &getModel('module');
			$skin_list = $oModuleModel->getSkins($this->module_path);
			Context::set('skin_list',$skin_list);

			// 템플릿 지정
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('index');
		}
	}
?>
