<?php
	/**
	 * @class  join_extendAdminController
	 * @author 난다날아 (sinsy200@gmail.com)
	 * @brief  member_join_extend모듈의 admin controller class
	 **/

	class join_extendAdminController extends join_extend {

		/**
		 * @brief 모듈 설정 저장
		 **/
		function procJoin_extendAdminInsertConfig() {
			$config = Context::getRequestVars();

			// module Controller 객체 생성하여 입력
			$oModuleController = &getController('module');
			$output = $oModuleController->insertModuleConfig('join_extend',$config);
			return $output;
		}
	}
?>
