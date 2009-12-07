<?php
    if(!defined("__ZBXE__")) exit();

    /**
     * @file remove_id_search.addon.php
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief 아이디 클릭 시 나타나는 메뉴에서 '작성글보기'를 삭제합니다.
     *
     **/

    if($called_position == 'after_module_proc' && $this->act == 'getMemberMenu') {

		// 메뉴에서 작성 글 보기를 찾는다.
		if (count($this->variables[menus])) {
			foreach($this->variables[menus] as $no => $val) {
				if (strpos($val->url, 'search_target=user_id') !== false) {
					// 작성 글 보기 메뉴를 없앤다.
					unset($this->variables[menus][$no]);
					break;
				}
			}
		}

    }
?>
