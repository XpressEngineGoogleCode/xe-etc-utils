<?php
    if(!defined("__ZBXE__")) exit();

    /**
     * @file write_limit.addon.php
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief 하루에 작성할 수 있는 글/댓글을 제한합니다.
     *
     **/

	// 로그인 정보 가져오기
	$logged_info = Context::get('logged_info');

	// 관리자면 통과!!
	if ($logged_info->is_admin == 'Y')	return;

	// 오늘 작성글 개수를 가져온다.
	// 비회원은 ip를 기준으로...
	$args->today = date("Ymd");
	if (!logged_info)	$args->ipaddress = 	$_SERVER['REMOTE_ADDR'];
	else				$args->member_srl = $logged_info->member_srl;

	// 글 작성 화면
	if($called_position == 'before_module_init' && ($this->act == 'dispBoardWrite' || $this->act == 'procBoardInsertDocument') ) {
		// 제한이 걸려있지 않으면 통과!
		if (!$addon_info->document_limit)	return;
		
		$output = executeQuery('addons.write_limit.document_count', $args);

		if (!$output->toBool()) {
			if ($this->act = 'dispBoardWrite'){
				$this->error = "SQL Error";
				return;
			}else{
				// xml_rpc return
				header("Content-Type: text/xml; charset=UTF-8");
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");
				print("<response>\r\n<error>-1</error>\r\n<message>SQL Error</message>\r\n</response>");
	
				Context::close();
				exit();
			}
		}

		// 설정된 개수 이상의 작성이면 중단!
		if ($output->data->count >= $addon_info->document_limit) {
			if ($this->act = 'dispBoardWrite'){
				$this->error = "하루 작성량({$addon_info->document_limit}개)초과입니다.";
				return;
			}else{
				// xml_rpc return
				header("Content-Type: text/xml; charset=UTF-8");
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");
				printf("<response>\r\n<error>-1</error>\r\n<message>하루 작성량(%d개)초과입니다.</message>\r\n</response>", $addon_info->document_limit);
	
				Context::close();
				exit();
			}
		}

	// 댓글 작성 때
    }else if($called_position == 'before_module_init' && $this->act == 'procBoardInsertComment') {

		// 제한이 걸려있지 않으면 통과!
		if (!$addon_info->comment_limit)	return;
		
		// 오늘 작성 댓글 개수를 가져온다.
		$output = executeQuery('addons.write_limit.comment_count', $args);
		if (!$output->toBool()) {
			// xml_rpc return
			header("Content-Type: text/xml; charset=UTF-8");
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			print("<response>\r\n<error>-1</error>\r\n<message>SQL Error</message>\r\n</response>");

			Context::close();
			exit();
		}

		// 설정된 개수 이상의 작성이면 중단!
		if ($output->data->count >= $addon_info->comment_limit) {
			// xml_rpc return
			header("Content-Type: text/xml; charset=UTF-8");
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			printf("<response>\r\n<error>-1</error>\r\n<message>하루 댓글 작성량(%d개)초과입니다.</message>\r\n</response>", $addon_info->comment_limit);

			Context::close();
			exit();
		}

    }
?>
