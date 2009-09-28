<?php
	/**
	 * @class  join_extendController
	 * @author 난다날아 (sinsy200@gmail.com)
	 * @brief  join_extend모듈의 controller class
	 **/

	class join_extendController extends join_extend {

		/**
		 * @brief 초기화
		 **/
		function init() {
		}

		/**
		 * @brief 동의
		 **/
		function procJoin_extendAgree() {
			$oJoinExtedModel = &getModel('join_extend');

			// 중복 확인
			$result = $oJoinExtedModel->isDuplicate();
			if ($result)	return $this->stop('jumin_exist');

			// 나이제한 확인
			$result = $oJoinExtedModel->isAge();
			if (!$result)	return $this->stop('age_restrictions');

			// 주민번호 확인
			$result = $oJoinExtedModel->isValid();
			if (!$result)	return $this->stop('invaild_jumin');

			// session 추가 
			$_SESSION['join_extend_authed'] = true;
			$_SESSION['join_extend_jumin']['name'] = Context::get('name');
			$_SESSION['join_extend_jumin']['jumin'] = md5(Context::get('jumin1') . '-' . Context::get('jumin2'));

			// xml_rpc return
			header("Content-Type: text/xml; charset=UTF-8");
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			print("<response>\r\n<error>0</error>\r\n<message>success</message>\r\n</response>");

			Context::close();
			exit();
		}

		/**
		 * @brief 주민번호 입력
		 **/
		function procJoin_extendJuminInsert($member_srl) {
			if (!$member_srl) return false;
			$args->jumin = $_SESSION['join_extend_jumin']['jumin'];
			$args->member_srl = $member_srl;

			$output = executeQuery('join_extend.insertJumin', $args);
			if (!$output->toBool())  return false;
			
			return true;
		}
	}
?>
