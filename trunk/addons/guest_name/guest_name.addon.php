<?php
    if(!defined("__ZBXE__")) exit();

    /**
     * @file guest_name.addon.php
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief 글/댓글 작성 시 비회원의 이름을 고정시킵니다.
     *
     **/

	if (Context::get('logged_info'))	return;

    if($called_position == 'before_display_content' && Context::getResponseMethod() == 'HTML' ) {
		// 비회원 고정 이름
		$guest_name = $addon_info->guest_name;
		if (empty($guest_name))	$guest_name = '손님';

		// <head></head> 사이에 등록
		$js = "<script type=\"text/javascript\">//<![CDATA[\nguest_name='{$guest_name}';\n//]]></script>";
		Context::addHtmlHeader($js);

		// guest_name.js 로드
		Context::addJsFile('./addons/guest_name/guest_name.js');



    }
?>
