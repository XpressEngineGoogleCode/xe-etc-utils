<?php
    /**
     * @class  zzz_menu_newModel
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief  zzz_menu_new 모듈의 model 클래스
     **/

    class zzz_menu_newModel extends zzz_menu_new {

        /**
         * @brief 설정 정보를 구함
         **/
        function getConfig() {
            // 기본 설정을 구한다.
            $config = $this->_getConfig();

            // new 이미지 태그
            $config->new_image_tag = $this->getNewImageTag();

            return $config;
        }

        /**
         * 설정을 받아옴.
         */
        function _getConfig() {
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
            if (!$config->select_module_mode)     $config->select_module_mode = 'in';

            $config->time_check = time() - intVal($config->duration_new) * 60 * 60;

            // mid 목록
            if (empty($config->mid_list))   $config->mid_list2 = array();
            else                            $config->mid_list2 = explode('|@|', $config->mid_list);

            return $config;
        }

        /**
         * @brief new 이미지 태그
         **/
        function getNewImageTag() {
            $config = $this->_getConfig();

            if (!empty($config->new_image)) {
                $new_image = sprintf('<img src="%s" alt="new" title="new" style="margin-left:2px;" class="addon_menu_new"/>', $config->new_image);
            }else{
                $path = sprintf('%s%s',getUrl(), 'modules/document/tpl/icons/');
                $new_image = sprintf('<img src="%snew.gif" alt="new" title="new" style="margin-left:2px;" class="addon_menu_new"/>', $path);
            }

            return $new_image;
        }

        /**
         * @brief url에서 mid 추출
         **/
        function getMid($url) {
            // url 파싱
            $url_info = @parse_url($url);
            if (!$url_info) return false;

            // 내부 링크인지 확인
            if ($url_info['host'] && $url_info['host'] != $_SERVER[HTTP_HOST])   return false;

            // url 쿼리에 mid가 있으면 반환
            parse_str($url_info['query']);
            if ($mid)   return $mid;

            // rewrite 형식
            $pattern = '/^' .str_replace('/', '\/', getScriptPath()). '([a-zA-Z0-9_]+)\/?$/';
            preg_match($pattern, $url_info['path'], $matches);
            if ($matches[1])    return $matches[1];

            $pattern = '/\.\/([a-zA-Z0-9_]+)\/?$/';
            preg_match($pattern, $url_info['path'], $matches);
            if ($matches[1])    return $matches[1];

            return $url;
        }
    }
?>
