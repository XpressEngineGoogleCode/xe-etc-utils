<?php
    /**
     * @class  zzz_menu_newModel
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief  zzz_menu_new 모듈의 model 클래스
     **/

    class zzz_menu_newModel extends zzz_menu_new {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 설정 정보를 구함
         **/
        function getConfig() {
            // 설정 정보를 받아옴 (module model 객체를 이용)
            $oModuleModel = &getModel('module');
            $site_info = Context::get('site_module_info');
            $config = $oModuleModel->getModulePartConfig('zzz_menu_new', $site_info->site_srl);

            // 기본값
            if (!$config->use_menu_new)  $config->use_menu_new = 'Y';
            if (!$config->use_comment)  $config->use_comment = 'N';
            if (!$config->duration_new) $config->duration_new = 24;
            if (!$config->up_new)       $config->up_new = 'N';
            if (!$config->text_new)     $config->text_new = 'N';
            
            $config->time_check = time() - intVal($config->duration_new) * 60 * 60;
            
            // new 이미지 태그
            if (!empty($config->new_image)) {
                $config->new_image_tag = sprintf('<img src="%s" alt="new" title="new" style="margin-left:2px;vertical-align: middle;" class="addon_menu_new"/>', $config->new_image);
            }else{
                $path = sprintf('%s%s',getUrl(), 'modules/document/tpl/icons/');
                $config->new_image_tag = sprintf('<img src="%snew.gif" alt="new" title="new" style="margin-left:2px;vertical-align: middle;" class="addon_menu_new"/>', $path);
            }

            return $config;
        }
        
        /**
         * @brief new 이미지 태그
         **/
        function getNewImageTag() {
            $config = $this->getConfig();
            
            if (!empty($config->new_image)) {
                $new_image = sprintf('<img src="%s" alt="new" title="new" style="margin-left:2px;vertical-align: middle;" class="addon_menu_new"/>', $config->new_image);
            }else{
                $path = sprintf('%s%s',getUrl(), 'modules/document/tpl/icons/');
                $new_image = sprintf('<img src="%snew.gif" alt="new" title="new" style="margin-left:2px;vertical-align: middle;" class="addon_menu_new"/>', $path);
            }
            
            return $new_image;
        }
        
        /**
         * @brief url에서 mid 추출
         **/
        function getMid($url) {
            return $url;
        }
    }
?>
