<?php
	/**
	 * @class  join_extendAdminView
	 * @author 난다날아 (sinsy200@gmail.com)
	 * @brief  join_extend모듈의 admin view class
	 **/

	class join_extendAdminView extends join_extend {

		/**
		 * @brief 초기화
		 **/
		function init() {
		}

		/**
		 * @brief 모듈 설정 화면
		 **/
		function dispJoin_extendAdminIndex() {
			$oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            Context::set('config',$config);

			// 스킨 목록을 구해옴
			$oModuleModel = &getModel('module');
			$skin_list = $oModuleModel->getSkins($this->module_path);
			Context::set('skin_list',$skin_list);

			// 템플릿 지정
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('index');
		}
		
		/**
         * @brief 특정 스킨에 속한 컬러셋 목록을 return
         **/
        function getJoin_extendAdminColorset() {
            $skin = Context::get('skin');

            if(!$skin) $tpl = "";
            else {
                $oModuleModel = &getModel('module');
                $skin_info = $oModuleModel->loadSkinInfo($this->module_path, $skin);
                Context::set('skin_info', $skin_info);

                $oModuleModel = &getModel('module');
                $config = $oModuleModel->getModuleConfig('join_extend');
                if(!$config->colorset) $config->colorset = "white";
                Context::set('config', $config);

                $oTemplate = &TemplateHandler::getInstance();
                $tpl = $oTemplate->compile($this->module_path.'tpl', 'colorset_list');
            }

            $this->add('tpl', $tpl);
        }
	}
?>