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
            $this->procUpdateNew($obj);

            return new Object();
        }

        /**
         * @brief 댓글 작성 후 트리거
         **/
        function triggerInsertComment(&$obj) {
            $oMNModel = &getModel('zzz_menu_new');
            $config = $oMNModel->getConfig();

            if ($config->use_comment == 'Y')   $this->procUpdateNew($obj);

            return new Object();
        }

        /**
         * @brief 글 삭제 후 트리거
         **/
        function triggerDeleteDocument(&$obj) {
            $this->procUpdateNew($obj);

            return new Object();
        }

        /**
         * @brief 댓글 삭제 후 트리거
         **/
        function triggerDeleteComment(&$obj) {
            $oMNModel = &getModel('zzz_menu_new');
            $config = $oMNModel->getConfig();

            if ($config->use_comment == 'Y')   $this->procUpdateNew($obj);

            return new Object();
        }

        /**
         * @brief 캐시를 이용한 new 갱신 (애드온에서 매 접속시 사용)
         **/
        function procUpdateNewUseCache(){
            // 설정
            $oMNModel = &getModel('zzz_menu_new');
            $config = $oMNModel->getConfig();
            $time_check = time() - intVal($config->duration_new) * 60 * 60;
            if ($config->use_menu_new != 'Y')   return;
            
            // 캐시 디렉토리
            $menu_cache_path = _XE_PATH_.'files/cache/menu';
            $menu_new_cache_path = _XE_PATH_.'files/cache/menu_new';
            
            
            // 해당 사이트의 모든 메뉴 목록
            $site_info = Context::get('site_module_info');
            $oMenuAdminModel = &getAdminModel('menu');
            $menus = $oMenuAdminModel->getMenus($site_info->site_srl);
            if (!count($menus)) return;
            
            foreach($menus as $val) {
                // 메뉴 캐시를 로드
                @include $menu_cache_path . '/' . $val->menu_srl . '.php';
                if (!$menu) continue;

                // 메뉴 아이템을 돌며 처리
                $this->_procUpdateNewUseCache($menu->list, $time_check, $site_info, $config);
            }
        }
        
        /**
         * @brief 캐시를 이용한 new 갱신 (재귀적으로 실행)
         **/
        function _procUpdateNewUseCache($menu_list, $time_check, $site_info, $config){
            if (!count($menu_list)) return;
            
            // 캐시 디렉토리
            $menu_new_cache_path = _XE_PATH_.'files/cache/menu_new';
            
            foreach($menu_list as $menu_item){
                // 하위 메뉴가 있으면 먼저 처리
                if (count($menu_item['list']))  $this->_procUpdateNewUseCache($menu_item['list'], $time_check, $site_info, $config);
                
                // 해당 메뉴의 캐시 로드
                $cache = sprintf("$menu_new_cache_path/menu.item.%d.php", $menu_item[node_srl]);

                @include $cache;
                if (!$regdate)  continue;

                // 해당 메뉴의 마지막 글/댓글 작성 시간이 새글표시 시간을 지났으면 new 제거
                if ($regdate < $time_check){
                    // 해당 메뉴의 모듈 정보
                    $oModuleModel = &getModel('module');
                    $module_info = $oModuleModel->getModuleInfoByMid($menu_item[url], $site_info->site_srl);
                    if (!$module_info->module_srl)  continue;
                
                    $this->procDelNew($module_info, $site_info, $regdate, $config, false);
                }
            }
            
        }
        
        /**
         * @brief new 갱신
         **/
        function procUpdateNew(&$obj, $all_site = false) {
            // 설정
            $oMNModel = &getModel('zzz_menu_new');
            $config = $oMNModel->getConfig();
            $time_check = time() - intVal($config->duration_new) * 60 * 60;
            $new_image = $oMNModel->getNewImageTag();
            
            // module 정보
            $site_info = Context::get('site_module_info');
            $oModuleModel = &getModel('module');
            $module_info = $oModuleModel->getModuleInfoByModuleSrl($obj->module_srl);
            if (!$module_info)  return;
            $mid = $module_info->mid;
            
            // 새글표시 미사용
            if ($config->use_menu_new != 'Y') {
                if (!$all_site) return;
                
                $this->procDelNew($module_info, $site_info, 0, $config, $new_image, $all_site);
                return;
            }
            
            // 마지막 글의 시간을 구한다.
            $args->list_count = 1;
            $args->order_type = 'asc';
            $args->module_srl = $module_info->module_srl;
            $output = executeQuery('document.getDocumentList', $args);
			if (!$output->toBool()) return;

            if (count($output->data)){ foreach($output->data as $doc){
                $last_date = ztime($doc->regdate);
            }}
			
            // 댓글 옵션이 켜져 있으면 마지막 댓글의 시간을 구한다.
            if ($config->use_comment == 'Y') {
                $output = executeQuery('comment.getNewestCommentList', $args);
                if (!$output->toBool()) return;

                $last_comment_date = ztime($output->data->regdate);

                // 마지막 댓글의 시간이 마지막 글의 시간보다 최신이면 last_date를 갱신
                if ($last_comment_date > $last_date)    $last_date = $last_comment_date;
            }
            
            if ($time_check <= $last_date)   $this->procAddNew($module_info, $site_info, $last_date, $config, $new_image, $all_site);
            else                             $this->procDelNew($module_info, $site_info, $last_date, $config, $all_site);
        }
         
        /**
         * @brief new 이미지 추가
         **/
        function procAddNew($module_info, $site_info, $last_date, $config, $new_image, $all_site){
            // 캐시 디렉토리
            $menu_cache_path = _XE_PATH_.'files/cache/menu';
            $menu_new_cache_path = _XE_PATH_.'files/cache/menu_new';
            
            // 현재 사이트에 연결된 메뉴 목록 구함
            $menu_srls = Array();
            $oMenuAdminModel = &getAdminModel('menu');
            $menu_info = $oMenuAdminModel->getMenus($site_info->site_srl);
            if (count($menu_info)) {
                foreach($menu_info as $menu) {
                    $menu_srls[] = $menu->menu_srl;
                }
            }

            // 메뉴 캐시 디렉토리의 각 파일을 돌며 new 이미지 추가
            $file_list = FileHandler::readDir($menu_cache_path);
            if (!count($file_list)) return new Object();
            
            foreach($file_list as $file) {
                // 현재 사이트에 해당하는 메뉴만 처리
                $token = explode('.', $file);
                if (!in_array($token[0], $menu_srls) && $all_site == false)   continue;

                if (strpos($file, 'xml'))   $this->procXMLAddNew($menu_cache_path.'/'.$file, $module_info->mid, $last_date, $config, $new_image);
                else                        $this->procPHPAddNew($menu_cache_path.'/'.$file, $module_info->mid, $last_date, $config, $new_image);
            }
            
            // new 표시 캐시
            $cache_file = $menu_new_cache_path . '/module.' . $module_info->module_srl . '.php';
            $buff = sprintf('<? $regdate=%d; ?>', $last_date);
            FileHandler::writeFile($cache_file, $buff);
        }

        /**
         * @brief XML 파일에 new 이미지 추가
         **/
        function procXMLAddNew($file, $mid, $last_date, $config, $new_image) {
            //
        }
        
        /**
         * @brief PHP 파일에 new 이미지 추가
         **/
        function procPHPAddNew($file, $mid, $last_date, $config, $new_image) {
            $cache = FileHandler::readFile($file);
            
            $menu_new_cache_path = _XE_PATH_.'files/cache/menu_new';

            // mid 검색
            $pattern = '/"node_srl"=>"([0-9]+)","parent_srl"=>"[0-9]+","text"=>\((true|\(\$is_admin==true\|\|\(is_array\(\$group_srls\)&&count\(array_intersect\(\$group_srls, array\([^\(]+\)\)\)\)\))\?\$_menu_names\[[0-9]+\]\[\$lang_type\]( \.\'<img[^>]*class="addon_menu_new"[^>]*>\')?:""\),"href"=>\((true|\(\$is_admin==true\|\|\(is_array\(\$group_srls\)&&count\(array_intersect\(\$group_srls, array\([^\(]+\)\)\)\)\))\?"[^,]+":""\),"url"=>\((true|\(\$is_admin==true\|\|\(is_array\(\$group_srls\)&&count\(array_intersect\(\$group_srls, array\([^\(]+\)\)\)\)\))\?"'.$mid.'":""\)/';
            $res = preg_match_all($pattern, $cache, $matches);
            if (!$res)  return;

            for ($i = 0; $i < $res; $i++){
                $node_srl = $matches[1][$i];

                $cache = $this->_procPHPAddNew($node_srl, $cache, $new_image, $config->text_new, $config->up_new);
                
                // new 표시 캐시
                $cache_file = $menu_new_cache_path . '/menu.item.' . $node_srl . '.php';
                $buff = sprintf('<? $regdate=%d; ?>', $last_date);
                FileHandler::writeFile($cache_file, $buff);
            }

            FileHandler::writeFile($file, $cache);
        }

        /**
         * @brief PHP 파일에 new 이미지 추가
         **/
        function _procPHPAddNew($node_srl, $cache, $new_image, $text_new, $up_new) {
            if ($node_srl == 0) return $cache;
            
            $menu_new_cache_path = _XE_PATH_.'files/cache/menu_new';

            $pattern = '/"node_srl"=>"'.$node_srl.'","parent_srl"=>"([0-9]+)"/U';
            $res = preg_match($pattern, $cache, $matches);
            $parent_srl = $matches[1];

            // new 이미지 추가
            $pattern = '/("link"=>\((true|\(\$is_admin==true\|\|\(is_array\(\$group_srls\)&&count\(array_intersect\(\$group_srls, array\([^\(]+\)\)\)\)\))\? \( array\([^\(.]*\)&&in_array\(Context::get\("mid"\),array\([^\(.]*\)\) \?(\$_menu_names\['.$node_srl.'\]\[\$lang_type\]|"<img src=\\\\"\.\/files\/attach\/menu_button\/[0-9]+\/'.$node_srl.'\.[^>]+>")):(\$_menu_names\['.$node_srl.'\]\[\$lang_type\]|"<img src=\\\\"\.\/files\/attach\/menu_button\/[0-9]+\/'.$node_srl.'\.[^>]+>")\):""\)/U';
            $cache = preg_replace($pattern, "$1 .'$new_image':$4 .'$new_image'):\"\")", $cache);

            if ($text_new == 'Y') {
                $pattern = '/("node_srl"=>"'.$node_srl.'","parent_srl"=>"[0-9]+","text"=>\((true|\(\$is_admin==true\|\|\(is_array\(\$group_srls\)&&count\(array_intersect\(\$group_srls, array\([^\(]+\)\)\)\)\))\?\$_menu_names\['.$node_srl.'\]\[\$lang_type\]):/';
                $cache = preg_replace($pattern, "$1 .'$new_image':", $cache);
            }

            // 올림 표시에 대한 new 표시 캐시
            if ($parent_srl) {
                $cache_file = $menu_new_cache_path . '/menu.sub.' .$parent_srl. '.' .$node_srl. '.php';
                $buff = sprintf('<? $regdate=%d; ?>', time());
                FileHandler::writeFile($cache_file, $buff);
            }
            
            // 올림 표시이면 부모 메뉴에 재귀적 호출
            if ($up_new == 'Y' && $parent_srl)  return $this->_procPHPAddNew($parent_srl, $cache, $new_image, $text_new, $up_new);
            else                                return $cache;
        }

        /**
         * @brief new 이미지 제거
         **/
        function procDelNew($module_info, $site_info, $last_date, $config, $all_site){
            // 캐시 디렉토리
            $menu_cache_path = _XE_PATH_.'files/cache/menu';
            $menu_new_cache_path = _XE_PATH_.'files/cache/menu_new';

            // 캐시 갱신
            $cache_file = $menu_new_cache_path . '/module.' . $module_info->module_srl . '.php';
            $buff = sprintf('<? $regdate=%d; ?>', $last_date);
            FileHandler::writeFile($cache_file, $buff);

            // 현재 사이트에 연결된 메뉴 목록 구함
            $menu_srls = Array();
            $oMenuAdminModel = &getAdminModel('menu');
            $menu_info = $oMenuAdminModel->getMenus($site_info->site_srl);
            if (count($menu_info)) {
                foreach($menu_info as $menu) {
                    $menu_srls[] = $menu->menu_srl;
                }
            }
            
            // 메뉴 캐시 디렉토리의 각 파일을 돌며 new 이미지 제거
            $file_list = FileHandler::readDir($menu_cache_path);
            if (!count($file_list)) return new Object();
            
            foreach($file_list as $file) {
                // 현재 사이트에 해당하는 메뉴만 처리
                $token = explode('.', $file);
                if (!in_array($token[0], $menu_srls) && $all_site == false)   continue;

                if (strpos($file, 'xml'))   $this->procXMLDelNew($menu_cache_path.'/'.$file, $module_info->mid, $module_info->module_srl, $config);
                else                        $this->procPHPDelNew($menu_cache_path.'/'.$file, $module_info->mid, $module_info->module_srl, $config);
            }
        }
        
        /**
         * @brief XML 파일에 new 이미지 제거
         **/
        function procXMLDelNew($file, $mid, $module_srl, $config) {
            //
        }
        
        /**
         * @brief PHP 파일에 new 이미지 제거
         **/
        function procPHPDelNew($file, $mid, $module_srl, $config) {
            $cache = FileHandler::readFile($file);
            
            $menu_new_cache_path = _XE_PATH_.'files/cache/menu_new';

            // mid 검색
            $pattern = '/"node_srl"=>"([0-9]+)","parent_srl"=>"[0-9]+","text"=>\((true|\(\$is_admin==true\|\|\(is_array\(\$group_srls\)&&count\(array_intersect\(\$group_srls, array\([^\(]+\)\)\)\)\))\?\$_menu_names\[[0-9]+\]\[\$lang_type\]( \.\'<img[^>]*class="addon_menu_new"[^>]*>\')?:""\),"href"=>\((true|\(\$is_admin==true\|\|\(is_array\(\$group_srls\)&&count\(array_intersect\(\$group_srls, array\([^\(]+\)\)\)\)\))\?"[^,]+":""\),"url"=>\((true|\(\$is_admin==true\|\|\(is_array\(\$group_srls\)&&count\(array_intersect\(\$group_srls, array\([^\(]+\)\)\)\)\))\?"'.$mid.'":""\)/';
            $res = preg_match_all($pattern, $cache, $matches);
            if (!$res)  return;

            for ($i = 0; $i < $res; $i++){
                $node_srl = $matches[1][$i];

                // 캐시 갱신
                $menu_item_cache_file = $menu_new_cache_path . '/menu.item.' . $node_srl . '.php';
                $module_cache_file = $menu_new_cache_path . '/module.' . $module_srl . '.php';
                FileHandler::copyFile($module_cache_file, $menu_item_cache_file);
                
                $cache = $this->_procPHPDelNew($node_srl, $cache, $config->up_new, $config);
            }

            FileHandler::writeFile($file, $cache);
        }

        /**
         * @brief PHP 파일에 new 이미지 제거
         **/
        function _procPHPDelNew($node_srl, $cache, $up_new, $config) {
            if ($node_srl == 0) return $cache;

            // 설정
            $time_check = time() - intVal($config->duration_new) * 60 * 60;
            $menu_new_cache_path = _XE_PATH_.'files/cache/menu_new';
            
            // 올림 표시 사용시 이 메뉴 혹은 하위 메뉴의 새글/댓글이 존재할 경우 new 이미지 없애지 않는다
            if ($up_new == 'Y'){
                @include $menu_new_cache_path . '/menu.item.' . $node_srl . '.php';

                if ($regdate > $time_check) return $cache;
                unset($regdate);
                $file_list = FileHandler::readDir($menu_new_cache_path);
                if (count($file_list)) {
                    foreach($file_list as $file) {
                        if (strpos($file, 'menu.sub.' .$node_srl. '.') === false)   continue;
                        
                        @include $menu_new_cache_path . '/' . $file;
                        if ($regdate > $time_check) return $cache;
                    }
                }
            }
            
            $pattern = '/"node_srl"=>"'.$node_srl.'","parent_srl"=>"([0-9]+)"/U';
            $res = preg_match($pattern, $cache, $matches);
            $parent_srl = $matches[1];

            // new 이미지 제거
            $pattern = '/("link"=>\((true|\(\$is_admin==true\|\|\(is_array\(\$group_srls\)&&count\(array_intersect\(\$group_srls, array\([^\(]+\)\)\)\)\))\? \( array\([^\(.]*\)&&in_array\(Context::get\("mid"\),array\([^\(.]*\)\) \?(\$_menu_names\['.$node_srl.'\]\[\$lang_type\]|"<img src=\\\\"\.\/files\/attach\/menu_button\/[0-9]+\/'.$node_srl.'\.[^>]+>"))( \.\'<img[^>]*class="addon_menu_new"[^>]*>\')?:(\$_menu_names\['.$node_srl.'\]\[\$lang_type\]|"<img src=\\\\"\.\/files\/attach\/menu_button\/[0-9]+\/'.$node_srl.'\.[^>]+>")( \.\'<img[^>]*class="addon_menu_new"[^>]*>\')?\):""\)/U';
            $cache = preg_replace($pattern, "$1:$5):\"\")", $cache);

            $pattern = '/("node_srl"=>"'.$node_srl.'","parent_srl"=>"[0-9]+","text"=>\((true|\(\$is_admin==true\|\|\(is_array\(\$group_srls\)&&count\(array_intersect\(\$group_srls, array\([^\(]+\)\)\)\)\))\?\$_menu_names\['.$node_srl.'\]\[\$lang_type\])( \.\'<img[^>]*class="addon_menu_new"[^>]*>\')?:/';
            $cache = preg_replace($pattern, "$1:", $cache);

            // 캐시 갱신
            $menu_item_cache_file = $menu_new_cache_path . '/menu.item.' . $node_srl . '.php';
            $menu_sub_cache_file = $menu_new_cache_path . '/menu.sub.' .$parent_srl. '.' .$node_srl. '.php';
            FileHandler::copyFile($menu_item_cache_file, $menu_sub_cache_file);
                
            // 올림 표시이면 부모 메뉴에 재귀적 호출
            if ($up_new == 'Y' && $parent_srl)  return $this->_procPHPDelNew($parent_srl, $cache, $up_new, $config);
            else                                return $cache;
        }
    }

?>