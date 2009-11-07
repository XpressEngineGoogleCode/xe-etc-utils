<?php
    /**
     * @class  zzz_menu_newController
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief  zzz_menu_new 모듈의 controller class
     **/

    class zzz_menu_newController extends zzz_menu_new {

        /**
         * @brief 글 작성 후 트리거
         **/
        function triggerInsertDocument(&$obj) {
            $this->procUpdateCache($obj);
            
            return new Object();
        }

        /**
         * @brief 댓글 작성 후 트리거
         **/
        function triggerInsertComment(&$obj) {
            $this->procUpdateCache($obj);

            return new Object();
        }

        /**
         * @brief 글 삭제 후 트리거
         **/
        function triggerDeleteDocument(&$obj) {
            $this->procUpdateCache($obj);

            return new Object();
        }

        /**
         * @brief 댓글 삭제 후 트리거
         **/
        function triggerDeleteComment(&$obj) {
            $this->procUpdateCache($obj);

            return new Object();
        }
        
        /**
         * @brief 메뉴 캐시에 include 코드 추가
         **/
        function procMenuInclude($menu_srl) {
            $cache = sprintf("%s/%d.php", $this->menu_cache_path, $menu_srl);
            $content = FileHandler::readFile($cache);
            if (!$content)  return;
            
            // 이미 include가 추가 되었는지 확인
            $res = strpos($content, 'menu_include.php');
            if ($res)   return;
            
            // include문 추가
            $content .= '<? @include _XE_PATH_."modules/zzz_menu_new/menu_include.php"; ?>';
            FileHandler::writeFile($cache, $content);
        }
        
        /**
         * @brief new 적용
         **/
        function procNew(&$menu_list) {
            $oMenuNewModel = &getModel('zzz_menu_new');
            $config = $oMenuNewModel->getConfig();
            $site_info = Context::get('site_module_info');
            if ($config->use_menu_new != 'Y')   return;

            $this->_procNew($menu_list, $config, $site_info->site_srl);
        }
        
        /**
         * @brief new 적용 (재귀)
         **/
        function _procNew(&$menu_list, &$config, &$site_srl) {
            if (!count($menu_list)) return;
            
            $is_new = false;
            foreach($menu_list as $menu_srl => $menu_item) {
                $regdate = 0;
                
                // 하위 메뉴가 있으면 먼저 처리
                $is_sub_new = false;
                if (count($menu_item['list']))
                    $is_sub_new = $this->_procNew($menu_list[$menu_srl]['list'], $config, $site_srl);

                // mid 구하기
                $oMenuNewModel = &getModel('zzz_menu_new');
                $mid = $oMenuNewModel->getMid($menu_item['url']);
                
                // 해당 mid에 새글 표시 사용인지 확인
                $is_use = in_array($mid, $config->mid_list2);
                if ($config->select_module_mode == 'out')   $is_use = !$is_use;
                if (!count($config->mid_list2))    $is_use = true;
                
                if (!empty($mid) && $is_use) {
                    // 현재 메뉴의 마지막 글 시간
                    $cache = sprintf("%s/%d.%s_doc.php", $this->menu_new_cache_path, $site_srl, $mid);
                    @include $cache;
    
                    // 현재 메뉴의 마지막 댓글 시간
                    if ($config->use_comment == 'Y') {
                        $cache = sprintf("%s/%d.%s_com.php", $this->menu_new_cache_path, $site_srl, $mid);
                        @include $cache;
    
                        if ($regdate_com > $regdate)    $regdate = $regdate_com;
                    }
                }
                // 설정된 시간 이내 새글/댓글이 있으면 new 이미지 추가
                if (($config->up_new == 'Y' && $is_sub_new) || intVal($config->time_check) < intVal($regdate)) {
                    if (!empty($menu_item['link']))   $menu_list[$menu_srl]['link'] .= $config->new_image_tag;
                    if ($config->text_new == 'Y' && !empty($menu_item['text']))   $menu_list[$menu_srl]['text'] .= $config->new_image_tag;
                    $is_new = true;
                }
            }
            
            return $is_new;
        }
        
        /**
         * @brief 캐시 업데이트
         **/
        function procUpdateCache(&$obj, $site_srl = -1) {
            // 메뉴에 새글 표시 사용중인지 확인
            $oMenuNewModel = &getModel('zzz_menu_new');
            $config = $oMenuNewModel->getConfig();
            if ($config->use_menu_new != 'Y')   return new Object();
            
            // site_srl
            if ($site_srl == -1) {
                $site_info = Context::get('site_module_info');
                $site_srl = $site_info->site_srl;
            }
            
            // module_info
            $oModuleModel = &getModel('module');
            $module_info = $oModuleModel->getModuleInfoByModuleSrl($obj->module_srl);
            $mid = $module_info->mid;
            if (empty($mid))  return;
            
            // 마지막 글의 시간을 구한다.
            $args->list_count = 1;
            $args->order_type = 'asc';
            $args->module_srl = $module_info->module_srl;
            $output = executeQuery('document.getDocumentList', $args);
			if (!$output->toBool()) return;

            if (count($output->data)){ foreach($output->data as $doc){
                $regdate = ztime($doc->regdate);
            }}
			
			// document에 대한 캐시 생성
            $cache = sprintf("%s/%d.%s_doc.php", $this->menu_new_cache_path, $site_srl, $mid);
            $buff = sprintf('<? $regdate=%d; ?>', $regdate);
            FileHandler::writeFile($cache, $buff);

            // 플래닛
            if ($module_info->module == 'planet') {
                // 플래닛 mid
                $oPlanetModel = &getModel('planet');
                $planet_config = $oPlanetModel->getPlanetConfig();
                $planet_mid = $planet_config->mid;

                // 플래닛의 마지막 글을 구한다.
                $args->order = 'asc';
                $output = executeQuery('planet.getPlanetNewestContentList', $args);
                if (!$output->toBool()) return;

                if (count($output->data)){ foreach($output->data as $doc){
                    $regdate = ztime($doc->regdate);
                }}
                
                // planet에 대한 캐시 생성
                if (!empty($planet_mid)) {
                    $cache = sprintf("%s/%d.%s_doc.php", $this->menu_new_cache_path, $site_srl, $planet_mid);
                    $buff = sprintf('<? $regdate=%d; ?>', $regdate);
                    FileHandler::writeFile($cache, $buff);
                }
            }
            
            // 마지막 댓글의 시간을 구한다.
            $output = executeQuery('comment.getNewestCommentList', $args);
            if (!$output->toBool()) return;

            $regdate = ztime($output->data->regdate);
            
            // comment에 대한 캐시 생성
            $cache = sprintf("%s/%d.%s_com.php", $this->menu_new_cache_path, $site_srl, $mid);
            $buff = sprintf('<? $regdate_com=%d; ?>', $regdate);
            FileHandler::writeFile($cache, $buff);
        }
        
        /**
         * @brief 메뉴 캐시 생성시추가 작업
         **/
        function triggerModuleHandlerProc(&$oModule) {
            $target_act = array(
                                'procHomepageInsertMenuItem', 
                                'procHomepageDeleteMenuItem', 
                                'procHomepageMenuItemMove', 
                                'procMenuAdminInsertItem', 
                                'procMenuAdminDeleteItem', 
                                'procMenuAdminMoveItem', 
                                'procMenuAdminMakeXmlFile'
                                );
                                
            if (in_array($oModule->act, $target_act)) {
                $menu_srl = Context::get('menu_srl');
                if (!$menu_srl)  return new Object();
        
                $oMenuNewAdminController = &getAdminController('zzz_menu_new');
                $oMenuNewAdminController->procZzz_menu_newAdminRemakeCache($menu_srl);
            }
            
            return new Object();
            
        }
        
        /**
         * @brief CafeXE 메뉴 설정 화면 추가 작업
         **/
        function triggerDisplay(&$output) {
            if (Context::getResponseMethod() == 'HTML' && Context::get('act') == 'dispHomepageTopMenu') {
                // 설정 가져오기
                $oMenuNewModel = &getModel('zzz_menu_new');
                $config = $oMenuNewModel->getConfig();
                Context::set('config', $config);
                
                // 현재 사이트의 mid 목록
                $oModuleModel = &getModel('module');
                $site_info = Context::get('site_module_info');
                $args->site_srl = $site_info->site_srl;
                $mid_list = $oModuleModel->getMidList($args);
                Context::set('mid_list',$mid_list);
                
                // 설정 화면 컴파일
                $oTemplate = new TemplateHandler();
                $menu_new = $oTemplate->compile('./modules/zzz_menu_new/tpl', 'menu_new_config.html');
                
                // HTML 추가
                $output = str_replace('</iframe>', "</iframe>$menu_new", $output);
            }
            
            return new Object();
        }
    }

?>