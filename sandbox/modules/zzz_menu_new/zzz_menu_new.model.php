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

            $config = $oModuleModel->getModuleConfig('zzz_menu_new');

            // 기본값
            if (!$config->use_menu_new)  $config->use_menu_new = 'Y';
            if (!$config->use_comment)  $config->use_comment = 'N';
            if (!$config->duration_new) $config->duration_new = 24;
            if (!$config->up_new)       $config->up_new = 'N';
            if (!$config->text_new)     $config->text_new = 'N';

            return $config;
        }
        
        /**
         * @brief new 이미지 태그
         **/
        function getNewImageTag() {
            $config = $this->getConfig();
            
            if (!empty($config->new_image)) {
                $new_image = sprintf('<img src="%s" alt="new" title="new" style="margin-left:2px;" class="addon_menu_new"/>', $addon_info->new_image);
            }else{
                $path = sprintf('%s%s',getUrl(), 'modules/document/tpl/icons/');
                $new_image = sprintf('<img src="%snew.gif" alt="new" title="new" style="margin-left:2px;" class="addon_menu_new"/>', $path);
            }
            
            return $new_image;
        }
    }
?>