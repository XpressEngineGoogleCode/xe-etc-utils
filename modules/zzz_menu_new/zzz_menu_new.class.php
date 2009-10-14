<?php
    /**
     * @class  zzz_menu_new
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief  zzz_menu_new 모듈의 high class
     **/

    class zzz_menu_new extends ModuleObject {
        
        /**
         * @brief 설치시 추가 작업이 필요할시 구현
         **/
        function moduleInstall() {
            // 캐시 디렉토리 생성
            FileHandler::makeDir('./files/cache/menu_new');
            
            // 글/댓글 작성에 트리거
            $oModuleController = &getController('module');

            $oModuleController->insertTrigger('document.insertDocument', 'zzz_menu_new', 'controller', 'triggerInsertDocument', 'after');
            $oModuleController->insertTrigger('comment.insertComment', 'zzz_menu_new', 'controller', 'triggerInsertComment', 'after');
            
            $oModuleController->insertTrigger('document.deleteDocument', 'zzz_menu_new', 'controller', 'triggerDeleteDocument', 'after');
            $oModuleController->insertTrigger('comment.deleteComment', 'zzz_menu_new', 'controller', 'triggerDeleteComment', 'after');

            return new Object();
        }

        /**
         * @brief 설치가 이상이 없는지 체크하는 method
         **/
        function checkUpdate() {
            $oModuleModel = &getModel('module');
            
            // 트리거 체크
            if(!$oModuleModel->getTrigger('document.insertDocument', 'zzz_menu_new', 'controller', 'triggerInsertDocument', 'after'))   return true;
            if(!$oModuleModel->getTrigger('comment.insertComment', 'zzz_menu_new', 'controller', 'triggerInsertComment', 'after'))      return true;
            if(!$oModuleModel->getTrigger('document.deleteDocument', 'zzz_menu_new', 'controller', 'triggerDeleteDocument', 'after'))   return true;
            if(!$oModuleModel->getTrigger('comment.deleteComment', 'zzz_menu_new', 'controller', 'triggerDeleteComment', 'after'))      return true;
            
            return false;
        }

        /**
         * @brief 업데이트 실행
         **/
        function moduleUpdate() {
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');
            
            if(!$oModuleModel->getTrigger('document.insertDocument', 'zzz_menu_new', 'controller', 'triggerInsertDocument', 'after'))
                $oModuleController->insertTrigger('document.insertDocument', 'zzz_menu_new', 'controller', 'triggerInsertDocument', 'after');
                
            if(!$oModuleModel->getTrigger('comment.insertComment', 'zzz_menu_new', 'controller', 'triggerInsertComment', 'after'))
                $oModuleController->insertTrigger('comment.insertComment', 'zzz_menu_new', 'controller', 'triggerInsertComment', 'after');
                
            if(!$oModuleModel->getTrigger('document.deleteDocument', 'zzz_menu_new', 'controller', 'triggerDeleteDocument', 'after'))
                $oModuleController->insertTrigger('document.deleteDocument', 'zzz_menu_new', 'controller', 'triggerDeleteDocument', 'after');
                
            if(!$oModuleModel->getTrigger('comment.deleteComment', 'zzz_menu_new', 'controller', 'triggerDeleteComment', 'after'))
                $oModuleController->insertTrigger('comment.deleteComment', 'zzz_menu_new', 'controller', 'triggerDeleteComment', 'after');
            
            return new Object(0, 'success_updated');
        }

        /**
         * @brief 캐시 파일 재생성
         **/
        function recompileCache() {
            $oMenuNewAdminController = &getAdminController('zzz_menu_new');
            $oMenuNewAdminController->procZzz_menu_newAdminRemakeCacheAll();
        }
    }
?>
