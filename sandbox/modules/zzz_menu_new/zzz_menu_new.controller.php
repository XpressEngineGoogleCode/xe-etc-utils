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

            $this->_procNew($menu_list, $config, $site_info->site_srl);
        }
        
        /**
         * @brief new 적용 (재귀)
         **/
        function _procNew(&$menu_list, &$config, &$site_srl) {
            if (!count($menu_list)) return;
            
            $is_new = false;
            foreach($menu_list as $menu_srl => $menu_item) {
                // 하위 메뉴가 있으면 먼저 처리
                $is_sub_new = false;
                if (count($menu_item['list']))
                    $is_sub_new = $this->_procNew($menu_list[$menu_srl]['list'], $config, $site_srl);

                // mid 구하기
                $oMenuNewModel = &getModel('zzz_menu_new');
                $mid = $oMenuNewModel->getMid($menu_item['url']);
                if (empty($mid))    continue;
                
                // 현재 메뉴의 마지막 글 시간
                $cache = sprintf("%s/%d.%s_doc.php", $this->menu_new_cache_path, $site_srl, $mid);
                @include $cache;

                // 현재 메뉴의 마지막 댓글 시간
                if ($config->use_comment == 'Y') {
                    $cache = sprintf("%s/%d.%s_com.php", $this->menu_new_cache_path, $site_srl, $mid);
                    @include $cache;

                    if ($regdate_com > $regdate)    $regdate = $regdate_com;
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
            
            // 마지막 댓글의 시간을 구한다.
            $output = executeQuery('comment.getNewestCommentList', $args);
            if (!$output->toBool()) return;

            $regdate = ztime($output->data->regdate);
            
            // comment에 대한 캐시 생성
            $cache = sprintf("%s/%d.%s_com.php", $this->menu_new_cache_path, $site_srl, $mid);
            $buff = sprintf('<? $regdate_com=%d; ?>', $regdate);
            FileHandler::writeFile($cache, $buff);
        }
    }

?>