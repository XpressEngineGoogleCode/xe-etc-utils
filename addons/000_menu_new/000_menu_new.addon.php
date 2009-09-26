<?php
    if(!defined("__ZBXE__")) exit();

    /**
     * @file menu_new.addon.php
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief 해당 메뉴에 새글이 있을 경우 new 표시를 합니다.
     *
     **/

	// HTML 출력 직전
	if($called_position != 'before_display_content' || Context::getResponseMethod() != 'HTML' ) return;

	// 메뉴 변수 명
	$menu_var_name = $addon_info->menu_var_name;
	if (empty($menu_var_name))	$menu_var_name = 'main_menu';

	// 메뉴 얻어오기
	$target_menu = Context::get($menu_var_name);
	if (empty($target_menu))	return;

	// 새글 표시 시간
	$time_interval = intVal($addon_info->menu_var_name) * 60 * 60;
	if (!$time_interval)	$time_interval = 24 * 60 * 60;

	// new 이미지
	if (!empty($addon_info->new_image)) {
		$new_image = sprintf('<img src="%snew.gif" alt="new" title="new" style="margin-left:2px;" class="addon_menu_new"/>', $addon_info->new_image);
    }else{
		$path = sprintf('%s%s',getUrl(), 'modules/document/tpl/icons/');
		$new_image = sprintf('<img src="%snew.gif" alt="new" title="new" style="margin-left:2px;" class="addon_menu_new"/>', $path);
	}

	// module model 얻기
	$oModuleModel = &getModel('module');

	// 메뉴 돌며 해당 메뉴 마지막 글 정보 얻기
	$time_check = date("YmdHis", time()-$time_interval);
	$args->list_count = 1;
	$args->order_type = 'asc';
	if ($target_menu->list && count($target_menu->list)){
		foreach($target_menu->list as $no1 => $menu1){
			// 해당 메뉴에 연결된 모듈 정보
			$target_module = $oModuleModel->getModuleInfoByMid($menu1[url]);
			if (!$target_module)	continue;

			// 2차 메뉴 있을 때
			$mark1 = false;
			if ($menu1['list'] && count($menu1['list'])) {
				foreach($menu1['list'] as $no2 => $menu2){
					// 해당 메뉴에 연결된 모듈 정보
					$target_module2 = $oModuleModel->getModuleInfoByMid($menu2[url]);
					if (!$target_module2)	continue;

					// 3차 메뉴 있을 때
					$mark2 = false;
					if ($menu2['list'] && count($menu2['list'])) {
						foreach($menu2['list'] as $no3 => $menu3) {
							// 해당 메뉴에 연결된 모듈 정보
							$target_module3 = $oModuleModel->getModuleInfoByMid($menu3[url]);
							if (!$target_module3)	continue;

							// 해당 모듈의 마지막 글 가져오기
							$args->module_srl = $target_module3->module_srl;
							$db_output = executeQuery('document.getDocumentList', $args);
							if (!$db_output->toBool())	continue;

							if ($db_output->data){ foreach($db_output->data as $doc){
								if($doc->regdate > $time_check) {
									if ($addon_info->up_new && !$mark2) {
										$target_menu->list[$no1][link] .= $new_image;
										$target_menu->list[$no1]['list'][$no2][link] .= $new_image;
										if ($addon_info->text_new) {
											$target_menu->list[$no1][text] .= $new_image;
											$target_menu->list[$no1]['list'][$no2][text] .= $new_image;
										}
										$mark2 = true;
									}
									if ($addon_info->text_new)	$target_menu->list[$no1]['list'][$no2]['list'][$no3][text] .= $new_image;
									$target_menu->list[$no1]['list'][$no2]['list'][$no3][link] .= $new_image;
								}
							}}
						}
					}
					// 3차 메뉴 없을 때
					else{
						// 해당 모듈의 마지막 글 가져오기
						$args->module_srl = $target_module2->module_srl;
						$db_output = executeQuery('document.getDocumentList', $args);
						if (!$db_output->toBool())	continue;

						if ($db_output->data){ foreach($db_output->data as $doc){
							if($doc->regdate > $time_check) {
								if ($addon_info->up_new && !$mark1) {
									$target_menu->list[$no1][link] .= $new_image;
									if ($addon_info->text_new)	$target_menu->list[$no1][text] .= $new_image;
									$mark1 = true;
								}
								if ($addon_info->text_new)	$target_menu->list[$no1]['list'][$no2][text] .= $new_image;
								$target_menu->list[$no1]['list'][$no2][link] .= $new_image;
							}
						}}
					}
				}
			}
			// 2차 메뉴 없을 때
			else{
				// 해당 모듈의 마지막 글 가져오기
				$args->module_srl = $target_module->module_srl;
				$db_output = executeQuery('document.getDocumentList', $args);
				if (!$db_output->toBool())	continue;

				if ($db_output->data){ foreach($db_output->data as $doc){
					if($doc->regdate > $time_check) {
						$target_menu->list[$no1][link] .= $new_image;
						if ($addon_info->text_new) $target_menu->list[$no1][text] .= $new_image;
					}
				}}

			}
		}
	}

	Context::set($menu_var_name, $target_menu);

	// 레이아웃을 다시 컴파일
	$output = $oTemplate->compile($layout_path, $layout_file, $edited_layout_file);

	// 트리커 다시 호출!
	ModuleHandler::triggerCall('display', 'before', $output);
?>
