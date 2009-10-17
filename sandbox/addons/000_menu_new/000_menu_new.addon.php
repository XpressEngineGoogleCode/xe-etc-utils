<?php
    if(!defined("__ZBXE__")) exit();

    /**
     * @file menu_new.addon.php
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief 해당 메뉴에 새글이 있을 경우 new 표시를 합니다.
     *
     **/

    // 메뉴 트리 재생성시
    if ($called_position == 'after_module_proc' && in_array($this->act, array(
            'procHomepageInsertMenuItem',
            'procHomepageDeleteMenuItem',
            'procHomepageMenuItemMove',
            'procMenuAdminInsertItem',
            'procMenuAdminDeleteItem',
            'procMenuAdminMoveItem',
            'procMenuAdminMakeXmlFile'
        ))) {
        $oMenuNewAdminController = &getAdminController('zzz_menu_new');
        $menu_srl = Context::get('menu_srl');
        if (!$menu_srl)  return;
        
        // 새글 표시도 재생성!
        $oMenuNewAdminController->procZzz_menu_newAdminRemakeCache($menu_srl);
    }

    // 카페 메뉴 설정 화면에 메뉴에 새글 표시 설정도 표시
    if ($called_position == 'before_display_content' && Context::getResponseMethod() == 'HTML' && Context::get('act') == 'dispHomepageTopMenu') {
        // 설정 가져오기
        $oMenuNewModel = &getModel('zzz_menu_new');
        $config = $oMenuNewModel->getConfig();
        Context::set('config', $config);
        
        // 설정 화면 컴파일
        $oTemplate = new TemplateHandler();
        $menu_new = $oTemplate->compile('./modules/zzz_menu_new/tpl', 'menu_new_config.html');
        $menu_new = preg_replace_callback('/<!--Meta:([a-z0-9\_\/\.\@]+)-->/is', array($this,'transMeta'), $menu_new);
        
        // HTML 추가
        $output = str_replace('</iframe>', "</iframe>$menu_new", $output);
    }
    
?>
