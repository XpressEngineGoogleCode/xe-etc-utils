<?php
    /**
     * @class  zzz_menu_newAdminView
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief  zzz_menu_new 모듈의 admin view class
     **/

    class zzz_menu_newAdminView extends zzz_menu_new {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 설정 페이지
         **/
        function dispZzz_menu_newAdminContent() {
            $oMenuNewModel = &getModel('zzz_menu_new');
            $config = $oMenuNewModel->getConfig();
            Context::set('config',$config);

            // 현재 사이트의 mid 목록
            $oModuleModel = &getModel('module');
            $site_info = Context::get('site_module_info');
            $args->site_srl = $site_info->site_srl;
            $mid_list = $oModuleModel->getMidList($args);
            Context::set('mid_list',$mid_list);
            
            // 템플릿 파일 지정
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('menu_new_config');
        }
    }
?>
