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
            
            // 회원가입 트리거 추가
            $oModuleController = &getController('module');
            $oModuleController->insertTrigger('member.insertMember', 'join_extend', 'controller', 'triggerInsertMember', 'after');
            
            // 애드온 없이 단독 작동하기 위한 트리거(2009-10-20)
            $oModuleController->insertTrigger('moduleHandler.init', 'join_extend', 'controller', 'triggerModuleHandlerInit', 'after');
            $oModuleController->insertTrigger('moduleHandler.proc', 'join_extend', 'controller', 'triggerModuleHandlerProc', 'after');
            $oModuleController->insertTrigger('display', 'join_extend', 'controller', 'triggerDisplay', 'before');
            
            // 회원탈퇴 트리거 추가(2009-10-31)
            $oModuleController->insertTrigger('member.deleteMember', 'join_extend', 'controller', 'triggerDeleteMember', 'before');
            
            return new Object();
        }

        /**
         * @brief 설치가 이상이 없는지 체크하는 method
         **/
        function checkUpdate() {
            $oDB = &DB::getInstance();
            $oModuleModel = &getModel('module');
            $oJoinExtendModel = &getModel('join_extend');

            // 트리거 체크
            if(!$oModuleModel->getTrigger('member.insertMember', 'join_extend', 'controller', 'triggerInsertMember', 'after'))   return true;
            if(!$oModuleModel->getTrigger('moduleHandler.init', 'join_extend', 'controller', 'triggerModuleHandlerInit', 'after'))   return true;
            if(!$oModuleModel->getTrigger('moduleHandler.proc', 'join_extend', 'controller', 'triggerModuleHandlerProc', 'after'))   return true;
            if(!$oModuleModel->getTrigger('display', 'join_extend', 'controller', 'triggerDisplay', 'before'))   return true;
            if(!$oModuleModel->getTrigger('member.deleteMember', 'join_extend', 'controller', 'triggerDeleteMember', 'before'))   return true;
            
            // 기존 member 테이블의 jumin 필드를 join_extend의 jumin 필드로 이동(2009-10-30)
            if($oDB->isColumnExists("member","jumin"))  return true;
            
            // 에디터 내용 이전 (2009-12-12)
            if(!$oJoinExtendModel->isUpdateEditor())    return true;
            
            // 초대장에 유효기간 추가 (2009-01-25)
            if(!$oDB->isColumnExists("join_extend_invitation", "validdate"))    return true;
            
            return false;
        }

        /**
         * @brief 업데이트 실행
         **/
        function moduleUpdate() {
            $oDB = &DB::getInstance();
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');
            $oJoinExtendModel = &getModel('join_extend');
            $oJoinExtendAdminController = &getAdminController('join_extend');

            // 트리거 추가
            if(!$oModuleModel->getTrigger('member.insertMember', 'join_extend', 'controller', 'triggerInsertMember', 'after'))
                $oModuleController->insertTrigger('member.insertMember', 'join_extend', 'controller', 'triggerInsertMember', 'after');
            if(!$oModuleModel->getTrigger('moduleHandler.init', 'join_extend', 'controller', 'triggerModuleHandlerInit', 'after'))
                $oModuleController->insertTrigger('moduleHandler.init', 'join_extend', 'controller', 'triggerModuleHandlerInit', 'after');
            if(!$oModuleModel->getTrigger('moduleHandler.proc', 'join_extend', 'controller', 'triggerModuleHandlerProc', 'after'))
                $oModuleController->insertTrigger('moduleHandler.proc', 'join_extend', 'controller', 'triggerModuleHandlerProc', 'after');
            if(!$oModuleModel->getTrigger('display', 'join_extend', 'controller', 'triggerDisplay', 'before'))
                $oModuleController->insertTrigger('display', 'join_extend', 'controller', 'triggerDisplay', 'before');
            if(!$oModuleModel->getTrigger('member.deleteMember', 'join_extend', 'controller', 'triggerDeleteMember', 'before'))
                $oModuleController->insertTrigger('member.deleteMember', 'join_extend', 'controller', 'triggerDeleteMember', 'before');
            
            // 기존 member 테이블의 jumin 필드를 join_extend의 jumin 필드로 이동(2009-10-30)
            if($oDB->isColumnExists("member","jumin")) return new Object(-1, 'run_update');
            
            // 에디터 내용 이전 (2009-12-12)
            if(!$oJoinExtendModel->isUpdateEditor()) return $oJoinExtendAdminController->updateEditor();
            
            // 초대장에 유효기간 추가 (2009-01-25)
            if(!$oDB->isColumnExists("join_extend_invitation", "validdate"))
                $oDB->addColumn("join_extend_invitation", "validdate", "date");
            
            return new Object(0, 'success_updated');
        }

        /**
         * @brief 캐시 파일 재생성
         **/
        function recompileCache() {
        }
    }
?>
