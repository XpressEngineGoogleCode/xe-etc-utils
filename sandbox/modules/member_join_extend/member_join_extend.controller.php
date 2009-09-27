<?php
	/**
	 * @class  member_join_extendController
	 * @author 난다날아 (sinsy200@gmail.com)
	 * @brief  member_join_extend모듈의 controller class
	 **/

	class member_join_extendController extends member_join_extend {

		/**
		 * @brief 초기화
		 **/
		function init() {
		}

		/**
		 * @brief 동의
		 **/
		function procMember_join_extendAgree() {
			$oMJExtedModel = &getModel('member_join_extend');

			// 중복 확인
			$result = $oMJExtedModel->isDuplicate();
			if ($result)	return $this->stop('jumin_exist');

			// 나이제한 확인
			$result = $oMJExtedModel->isAge();
			if (!$result)	return $this->stop('age_restrictions');

			// 주민번호 확인
			$result = $oMJExtedModel->isValid();
			if (!$result)	return $this->stop('invaild_jumin');

			// session 추가 
			$_SESSION['member_join_extend_authed'] = true;
			$_SESSION['member_join_extend_jumin']['name'] = Context::get('name');
			$_SESSION['member_join_extend_jumin']['jumin'] = md5(Context::get('jumin1') . '-' . Context::get('jumin2'));

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
		function procJuminInsert($member_srl) {
			if (!$member_srl) return false;
			$args->jumin = $_SESSION['member_join_extend_jumin']['jumin'];
			$args->member_srl = $member_srl;

			$output = executeQuery('member_join_extend.insertJumin', $args);
			if (!$output->toBool())  return false;
			
			return true;
		}
	}
?>
