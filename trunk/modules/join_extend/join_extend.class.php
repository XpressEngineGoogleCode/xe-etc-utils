<?php
	/**
	 * @class  join_extend
	 * @author 난다날아 (sinsy200@gmail.com)
	 * @brief  join_extend 모듈의 상위 class
	 **/

	class join_extend extends ModuleObject {

		/**
		 * @brief 설치시 추가 작업이 필요할시 구현
		 **/
		function moduleInstall() {
			
			// member table에 주민등록번호를 받을 jumin column을 추가
			$oDB = &DB::getInstance();
			$oDB->addColumn('member','jumin','varchar',32,'',true);
			
			return new Object();
		}

		/**
		 * @brief 설치가 이상이 없는지 체크하는 method
		 **/
		function checkUpdate() {
			$oDB = &DB::getInstance();
			$oModuleModel = &getModel('module');

			// jumin colmn이 있나?
			if(!$oDB->isColumnExists('member', 'jumin')) return true;

			return false;
		}

		/**
		 * @brief 업데이트 실행
		 **/
		function moduleUpdate() {
			$oDB = &DB::getInstance();
			$oModuleModel = &getModel('module');
			$oModuleController = &getController('module');
	
			// jumin colimn을 추가
			if(!$oDB->isColumnExists('member', 'jumin')) {
				$oDB->addColumn('member','jumin','varchar',32,'',true);
			}

			return new Object(0, 'success_updated');
		}

		/**
		 * @brief 캐시 파일 재생성
		 **/
		function recompileCache() {
		}
	}
?>