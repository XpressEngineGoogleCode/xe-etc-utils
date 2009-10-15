<?php
    if(!defined("__ZBXE__")) exit();

    /**
     * @file menu_new.addon.php
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief 해당 메뉴에 새글이 있을 경우 new 표시를 합니다.
     *
     **/

    // 메뉴 트리 재생성시
    if ($called_position == 'after_module_proc' && strpos($this->act, 'procMenuAdmin') !== false) {
        $oMenuNewAdminController = &getAdminController('zzz_menu_new');
        $menu_srl = Context::get('menu_srl');
        if (!$menu_srl)  return;
        
        // 새글 표시도 재생성!
        $oMenuNewAdminController->procZzz_menu_newAdminRemakeCache($menu_srl);
    }
?>
