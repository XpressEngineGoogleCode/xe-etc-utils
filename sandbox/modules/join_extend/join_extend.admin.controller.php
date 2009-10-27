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
		    $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            
			$new_config = Context::getRequestVars();

            // 새 설정을 기존 설정과 합친다.
            $config_list = get_object_vars($new_config);
            if (count($config_list)) {
                foreach($config_list as $var_name => $val) {
                    $config->{$var_name} = $val;
                }
            }
            
			// module Controller 객체 생성하여 입력
			$oModuleController = &getController('module');
			$output = $oModuleController->insertModuleConfig('join_extend',$config);
			return $output;
		}
	}
?>
