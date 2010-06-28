<?php
    /**
     * @class  join_extendAdminController
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief  member_join_extend모듈의 admin controller class
     **/

    class join_extendAdminController extends join_extend {

        /**
         * @brief 초기화
         **/
        function init() {
        }
        
        /**
         * @brief 모듈 설정 저장
         **/
        function procJoin_extendAdminInsertConfig() {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->_getConfig(false, false);
            $new_config = Context::getRequestVars();
            $config_type = $new_config->config_type;
            unset($new_config->config_type);
            
            // 입력항목 설정일 경우 기존 일력항목 설정 값은 초기화
            if (isset($new_config->user_name_type)) {
                $config_list = get_object_vars($config);
                if (count($config_list)) {
                    foreach($config_list as $var_name => $val) {
                        if (strpos($var_name, '_required') || strpos($var_name, '_no_mod') || strpos($var_name, '_lower_length') || strpos($var_name, '_upper_length') || strpos($var_name, '_type'))
                            unset($config->{$var_name});
                    }
                }
            }
            unset($config->input_config);
            
            // 에디터 내용 없앰
            unset($config->agreement);
            unset($config->private_agreement);
            unset($config->private_gathering_agreement);
            unset($config->welcome);
            unset($config->welcome_email);

            // 값이 없을 때
            if ($config_type == "after_config"){
                if (!$new_config->welcome_title) $new_config->welcome_title = '';
                if (!$new_config->welcome_email_title) $new_config->welcome_email_title = '';
                if (!$new_config->notify_admin_collect_number) $new_config->notify_admin_collect_number = '';
            }
            if ($config_type == "coupon_config"){
                if (!$new_config->coupon_var_name) $new_config->coupon_var_name = '';
            }
            if ($config_type == "extend_var_config"){
                if (!$new_config->sex_var_name) $new_config->sex_var_name = '';
                if (!$new_config->man_value) $new_config->man_value = '';
                if (!$new_config->woman_value) $new_config->woman_value = '';
                if (!$new_config->age_var_name) $new_config->age_var_name = '';
                if (!$new_config->recoid_var_name) $new_config->recoid_var_name = '';
                if (!$new_config->recoid_point) $new_config->recoid_point = '';
                if (!$new_config->joinid_point) $new_config->joinid_point = '';
            }
            if ($config_type == "index"){
                if (!$new_config->admin_id) $new_config->admin_id = '';
            }
            if ($config_type == "input_config"){
                $config_list = get_object_vars($new_config);
                if (count($config_list)){
                    foreach($config_list as $var_name => $val){
                        if (strpos($var_name, "_type")){
                            $name = substr($var_name, 0, -5);
                            if (!$new_config->{$name. "_lower_length"}) $new_config->{$name. "_lower_length"} = '';
                            if (!$new_config->{$name. "_upper_length"}) $new_config->{$name. "_upper_length"} = '';
                        }
                    }
                }
            }
            if ($config_type == "restrictions_config"){
                if (!$new_config->age_restrictions) $new_config->age_restrictions = '';
                if (!$new_config->age_upper_restrictions) $new_config->age_upper_restrictions = '';
                if (!$new_config->msg_junior_join) $new_config->msg_junior_join = '';
            }
            
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
        
        /**
         * @brief 에디터 설정 저장
         **/
        function procJoin_extendAdminInsertEditor() {
            $type = Context::get('type');
            
            switch ($type) {
                case 'agreement':
                    $content = Context::get('agreement');
                    break;
                case 'private_agreement':
                    $content = Context::get('private_agreement');
                    break;
                case 'private_gathering_agreement':
                    $content = Context::get('private_gathering_agreement');
                    break;
                case 'welcome':
                    $content = Context::get('welcome');
                    break;
                case 'welcome_email':
                    $content = Context::get('welcome_email');
                    break;
            }
            
            // module Controller 객체 생성하여 입력
            $oModuleController = &getController('module');
            $output = $oModuleController->insertModuleConfig('join_extend_editor_'.$type, $content);
            return $output;
        }
        
        /**
         * @brief 주민등록번호를 새 테이블로 이동
         **/
        function procJoin_extendAdminUpdateTable(){
            $oDB = &DB::getInstance();
            $count = Context::get('count');
            $start_idx = Context::get('start_idx');

            $args->order_type = 'asc';
            $args->list_count = $count;
            $args->page = $start_idx;
            $output = $oDB->executeQuery('join_extend.getOldJumin', $args);
            if (!$output->toBool())  return $output;
            
            if ($output->data && count($output->data)){
                foreach($output->data as $val){
                    $output2 = $oDB->executeQuery('join_extend.insertJuminToNewTable', $val);
                    if (!$output2->toBool())    return $output2;
                }
            }
            
            $percent = $output->page_navigation->cur_page / $output->page_navigation->total_page;

            $this->add('next_idx', intVal($start_idx)+1);
            $this->add('percent', $percent);
            if ($percent == 1) {
                $oDB->dropColumn('member', 'jumin');
                $this->setMessage('complete_update_table');
            }
        }
        
        /**
         * @brief 에디터 이전
         **/
        function updateEditor(){
            // 기존 설정을 가져온다.
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->_getConfig(false, false);
            
            // 에디터는 별도 저장
            $oModuleController = &getController('module');
            $output = $oModuleController->insertModuleConfig('join_extend_editor_agreement', $config->agreement);
            if (!$output->toBool()) return $output;
            $output = $oModuleController->insertModuleConfig('join_extend_editor_private_agreement', $config->private_agreement);
            if (!$output->toBool()) return $output;
            $output = $oModuleController->insertModuleConfig('join_extend_editor_private_gathering_agreement', $config->private_gathering_agreement);
            if (!$output->toBool()) return $output;
            $output = $oModuleController->insertModuleConfig('join_extend_editor_welcome', $config->welcome);
            if (!$output->toBool()) return $output;
            
            // 기존 설정에서 에디터 내용은 삭제
            unset($config->agreement);
            unset($config->private_agreement);
            unset($config->private_gathering_agreement);
            unset($config->welcome);
            $output = $oModuleController->insertModuleConfig('join_extend', $config);
            if (!$output->toBool()) return $output;
            
            return new Object(0, 'success_updated');
        }
        
        /**
         * @brief 초대장 생성
         **/
        function procJoin_extendAdminGenerateInvitation(){
            // 개수 확인
            $count = intVal(Context::get('count'));
            if ($count < 1 || $count > 100) {
                $this->SetError(1);
                $this->SetMessage('msg_invitation_incorrect_count');
                return;
            }
            
            // 유효기간 확인
            $validdate = Context::get('validdate');
            if ($validdate && $validdate < date("Ymd")) {
                $this->SetError(1);
                $this->SetMessage('msg_validdate_past');
                return;
            }
            
            // 초대장 생성
            $oDB = &DB::getInstance();
            $oDB->begin();
            for ($i = 0; $i < $count; $i++) {
                
                while(1) {
                    $args->invitation_code = strtoupper(md5(microtime()+$i));
                    $output = $oDB->executeQuery('join_extend.getInvitation', $args);
                    if (!$output->toBool()) {
                        $oDB->rollback();
                        return $output;
                    }
                    if (!$output->data) break;
                }
                
                $args->invitation_srl = getNextSequence();
                $args->own_member_srl = 0;
                $args->validdate = $validdate;
                $output = $oDB->executeQuery('join_extend.insertInvitation', $args);
                if (!$output->toBool()) {
                    $oDB->rollback();
                    return $output;
                }
            }
            $oDB->commit();
        }
        
        /**
         * @brief 초대장 삭제
         **/
        function procJoin_extendAdminDeleteInvitation(){
            $args->invitation_srls = Context::get('invitation_srls');
            if (!$args->invitation_srls) return new Object(-1, 'invitaion_srls is missing');
            
            $output = executeQuery('join_extend.deleteInvitation', $args);
            if (!$output->toBool()) return $output;
            
            $this->setMessage('success_deleted');
        }
        
        /**
         * @brief 쿠폰 생성
         **/
        function procJoin_extendAdminGenerateCoupon(){
            // 개수 확인
            $count = intVal(Context::get('count'));
            if ($count < 1 || $count > 100) {
                $this->SetError(1);
                $this->SetMessage('msg_invitation_incorrect_count');
                return;
            }
            
            // 포인트 확인
            $point = Context::get('point');
            if (!$point || !is_numeric($point) || intVal($point) < 0){
                $this->SetError(1);
                $this->SetMessage('msg_invalid_number');
                return;
            }
            
            // 유효기간 확인
            $validdate = Context::get('validdate');
            if ($validdate && $validdate < date("Ymd")) {
                $this->SetError(1);
                $this->SetMessage('msg_validdate_past');
                return;
            }
            
            // 쿠폰 생성
            $oDB = &DB::getInstance();
            $oDB->begin();
            for ($i = 0; $i < $count; $i++) {
                
                while(1) {
                    $args->coupon_code = strtoupper(md5(microtime()+$i));
                    $output = $oDB->executeQuery('join_extend.getCoupon', $args);
                    if (!$output->toBool()) {
                        $oDB->rollback();
                        return $output;
                    }
                    if (!$output->data) break;
                }
                
                $args->coupon_srl = getNextSequence();
                $args->own_member_srl = 0;
                $args->validdate = $validdate;
                $args->point = $point;
                $output = $oDB->executeQuery('join_extend.insertCoupon', $args);
                if (!$output->toBool()) {
                    $oDB->rollback();
                    return $output;
                }
            }
            $oDB->commit();
        }
        
        /**
         * @brief 쿠폰 삭제
         **/
        function procJoin_extendAdminDeleteCoupon(){
            $args->coupon_srls = Context::get('coupon_srls');
            if (!$args->coupon_srls) return new Object(-1, 'coupon_srls is missing');
            
            $output = executeQuery('join_extend.deleteCoupon', $args);
            if (!$output->toBool()) return $output;
            
            $this->setMessage('success_deleted');
        }
    }
?>
