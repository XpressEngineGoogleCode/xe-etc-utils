<?php
    /**
     * @class  zzz_menu_newAdminController
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief  zzz_menu_new 모듈의 admin controller class
     **/

    class zzz_menu_newAdminController extends zzz_menu_new {

        /**
         * @brief 초기화
         **/
        function init() {
        }
        
        /**
         * @brief 설정 저장
         **/
        function procZzz_menu_newAdminSaveConfig() {
            $config = Context::getRequestVars();
            
            // module Controller 객체 생성하여 입력
            $oModuleController = &getController('module');
            //$output = $oModuleController->insertModuleConfig('zzz_menu_new',$config);
            $site_info = Context::get('site_module_info');
            $output = $oModuleController->insertModulePartConfig('zzz_menu_new', $site_info->site_srl, $config);
            if (!$output->toBool()) $output;
        }

        /**
         * @brief 캐시파일 재생성
         **/
        function procZzz_menu_newAdminRemakeCacheAll() {
            FileHandler::removeFilesInDir($this->menu_new_cache_path);
            
            // 메뉴 캐시를 돌며 각 메뉴의 모듈 새글 체크
            $file_list = FileHandler::readDir($this->menu_cache_path);
            if (!count($file_list)) return new Object(-1, 'error');
            
            foreach($file_list as $file) {
                if (strpos($file, 'xml'))   continue;
                
                $token = explode('.', $file);
                $menu_srl = $token[0];
                $this->procZzz_menu_newAdminRemakeCache($menu_srl);
            }
        }
        
        /**
         * @brief menu_srl에 해당하는 캐시 재생성
         **/
        function procZzz_menu_newAdminRemakeCache($menu_srl) {
            // menu info
            $oMenuAdminModel = &getAdminModel('menu');
            $menu_info = $oMenuAdminModel->getMenu($menu_srl);
            
            // 메뉴 include
            @include $menu_info->php_file;
            
            // 각 메뉴 아이템의 캐시 재생성
            $this->_procZzz_menu_newAdminRemakeCache($menu->list, $menu_info->site_srl);
            
            // 해당 메뉴 캐시에 include 문 추가
            $oMenuNewController = &getController('zzz_menu_new');
            $oMenuNewController->procMenuInclude($menu_srl);
        }
        
        /**
         * @brief 각 메뉴 아이템의 캐시 재생성 (하위 메뉴는 재귀적으로...)
         **/
        function _procZzz_menu_newAdminRemakeCache($menu_list, $site_srl) {
            if (!count($menu_list)) return;
            
            foreach($menu_list as $menu_item) {
                // 하위 메뉴가 있으면 하위 메뉴부터 처리
                if (count($menu_item['list']))    $this->_procZzz_menu_newAdminRemakeCache($menu_item['list'], $site_srl);
                
                // 해당 메뉴의 모듈 정보
                $oModuleModel = &getModel('module');
                $module_info = $oModuleModel->getModuleInfoByMid($menu_item[url], $site_srl);
                if (!$module_info->module_srl)  return;
                
                // 해당 모듈의 캐시 재생성
                $obj->module_srl = $module_info->module_srl;
                $oMenuNewController = &getController('zzz_menu_new');
                $oMenuNewController->procUpdateCache($obj, $site_srl);
            }
        }
    }
?>
