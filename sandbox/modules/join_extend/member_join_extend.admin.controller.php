<?php
	/**
	 * @class  member_join_extendAdminController
	 * @author 난다날아 (sinsy200@gmail.com)
	 * @brief  member_join_extend모듈의 admin controller class
	 **/

	class member_join_extendAdminController extends member_join_extend {

		/**
		 * @brief 모듈 설정 저장
		 **/
		function procMember_join_extendAdminInsertConfig() {
			$config = Context::getRequestVars();

			// module Controller 객체 생성하여 입력
			$oModuleController = &getController('module');
			$output = $oModuleController->insertModuleConfig('member_join_extend',$config);
			return $output;
		}
	}
?>
