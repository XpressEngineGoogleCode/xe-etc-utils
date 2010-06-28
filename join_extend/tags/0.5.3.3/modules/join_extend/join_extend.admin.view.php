<?php
    /**
     * @class  join_extendAdminView
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief  join_extend모듈의 admin view class
     **/

    class join_extendAdminView extends join_extend {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 모듈 설정 화면
         **/
        function dispJoin_extendAdminIndex() {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            $is_update_table = $oJoinExtendModel->isUpdateTable();
            Context::set('config',$config);
            Context::set('is_update_table',$is_update_table);
            
            // 스킨 목록을 구해옴
            $oModuleModel = &getModel('module');
            $skin_list = $oModuleModel->getSkins($this->module_path);
            Context::set('skin_list',$skin_list);

            // 템플릿 지정
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('index');
        }
        
        /**
         * @brief 약관 설정 화면
         **/
        function dispJoin_extendAdminAgreeConfig() {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            Context::set('config',$config);
            
            // 에디터 공통 설정
            $oEditorModel = &getModel('editor');
            
            $option->allow_fileupload = false;
            $option->enable_autosave = false;
            $option->enable_default_component = true;
            $option->enable_component = true;
            $option->resizable = true;
            $option->height = 300;
            
            // 이용약관 에디터
            $option->content_key_name = 'agreement';
            $editor_agreement = $oEditorModel->getEditor(0, $option);
            Context::set('editor_agreement', $editor_agreement);
            
            // 개인정보취급방침 에디터
            $option->content_key_name = 'private_agreement';
            $editor_private_agreement = $oEditorModel->getEditor(0, $option);
            Context::set('editor_private_agreement', $editor_private_agreement);
            
            // 개인정보 수집 및 이용 에디터
            $option->content_key_name = 'private_gathering_agreement';
            $editor_private_gathering_agreement = $oEditorModel->getEditor(0, $option);
            Context::set('editor_private_gathering_agreement', $editor_private_gathering_agreement);
            
            // 템플릿 지정
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('agree_config');
        }

        /**
         * @brief 확장변수 연동 설정 화면
         **/
        function dispJoin_extendAdminExtendVarConfig() {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            Context::set('config',$config);
            
            // 템플릿 지정
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('extend_var_config');
        }
        
        /**
         * @brief 가입 제한 설정 화면
         **/
        function dispJoin_extendAdminRestrictionsConfig() {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            Context::set('config',$config);
            
            // 템플릿 지정
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('restrictions_config');
        }
        
        /**
         * @brief 가입후 처리 설정 화면
         **/
        function dispJoin_extendAdminAfterConfig() {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            Context::set('config',$config);
            
            // 에디터 공통 설정
            $oEditorModel = &getModel('editor');
            
            $option->allow_fileupload = false;
            $option->enable_autosave = false;
            $option->enable_default_component = true;
            $option->enable_component = true;
            $option->resizable = true;
            $option->height = 300;

            // 가입 환영 쪽지 에디터
            $option->content_key_name = 'welcome';
            $editor_welcome = $oEditorModel->getEditor(0, $option);
            Context::set('editor_welcome', $editor_welcome);
            
            // 가입 환영 메일 에디터
            $option->content_key_name = 'welcome_email';
            $editor_welcome_email = $oEditorModel->getEditor(0, $option);
            Context::set('editor_welcome_email', $editor_welcome_email);
            
            // 템플릿 지정
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('after_config');
        }
        
        /**
         * @brief 주민등록번호 설정 화면
         **/
        function dispJoin_extendAdminJuminConfig() {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            Context::set('config',$config);
            
            // 템플릿 지정
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('jumin_config');
        }
        
        /**
         * @brief 정보입력 설정 화면
         **/
        function dispJoin_extendAdminInputConfig() {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            Context::set('config',$config);
            
            // 가입폼의 추가 변수 가져오기
            $oMemberModel = &getModel('member');
            $extend_list = $oMemberModel->getJoinFormList();
            if (!count($extend_list))   $extend_list = array();
            Context::set('extend_list', $extend_list);
            
            // 안내 메시지 언어팩
            $msg_user_id_length = Context::getLang('msg_user_id_length');
            $msg_user_name_length = Context::getLang('msg_user_name_length');
            $msg_nick_name_length = Context::getLang('msg_nick_name_length');
            $msg_email_length = Context::getLang('msg_email_length');
            Context::addHtmlHeader(sprintf('<script type="text/javascript"> var msg_user_id_length="%s"; var msg_user_name_length="%s"; var msg_nick_name_length="%s"; var msg_email_length="%s"; </script>',
                $msg_user_id_length, $msg_user_name_length, $msg_nick_name_length, $msg_email_length)
            );
            
            // 템플릿 지정
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('input_config');
        }
        
        
        /**
         * @brief 특정 스킨에 속한 컬러셋 목록을 return
         **/
        function getJoin_extendAdminColorset() {
            $skin = Context::get('skin');

            if(!$skin) $tpl = "";
            else {
                $oModuleModel = &getModel('module');
                $skin_info = $oModuleModel->loadSkinInfo($this->module_path, $skin);
                Context::set('skin_info', $skin_info);

                $oModuleModel = &getModel('module');
                $config = $oModuleModel->getModuleConfig('join_extend');
                if(!$config->colorset) $config->colorset = "white";
                Context::set('config', $config);

                $oTemplate = &TemplateHandler::getInstance();
                $tpl = $oTemplate->compile($this->module_path.'tpl', 'colorset_list');
            }

            $this->add('tpl', $tpl);
        }
        
        /**
         * @brief 초대장 설정
         **/
        function dispJoin_extendAdminInvitationConfig() {
            $oMemberModel = &getModel('member');
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            Context::set('config',$config);
            
            // 초대장 목록
            $args->page = Context::get('page');
            $args->invitation_code = Context::get('code');
            $args->joindate = Context::get('joindate');
            $output = executeQuery('join_extend.getInvitationList', $args);
            if (!$output->toBool()) return $output;
            
            if ($output->data) {
                foreach($output->data as $no => $val){
                    if ($val->member_srl) {
                        $member_info = $oMemberModel->getMemberInfoByMemberSrl($val->member_srl);
                        if ($member_info)   $val->join_id = $member_info->user_id;
                        else                $val->join_id = Context::getLang('deleted_member');
                    }
                    if ($val->joindate == "-")  $val->joindate = 0;
                    $val->code = substr($val->code, 0, 8) .'-'. substr($val->code, 8, 8) .'-'. substr($val->code, 16, 8) .'-'. substr($val->code, 24, 8);
                }
            }
            
            Context::set('invitation_list', $output->data);
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('page_navigation', $output->page_navigation);
            
            // 템플릿 지정
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('invitation_config');
        }
        
        /**
         * @brief 가입쿠폰 설정
         **/
        function dispJoin_extendAdminCouponConfig() {
            $oMemberModel = &getModel('member');
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            Context::set('config',$config);
            
            // 쿠폰 목록
            $args->page = Context::get('page');
            $args->invitation_code = Context::get('code');
            $args->joindate = Context::get('joindate');
            $output = executeQuery('join_extend.getCouponList', $args);
            if (!$output->toBool()) return $output;
            
            if ($output->data) {
                foreach($output->data as $no => $val){
                    if ($val->member_srl) {
                        $member_info = $oMemberModel->getMemberInfoByMemberSrl($val->member_srl);
                        if ($member_info)   $val->join_id = $member_info->user_id;
                        else                $val->join_id = Context::getLang('deleted_member');
                    }
                    if ($val->joindate == "-")  $val->joindate = 0;
                    $val->code = substr($val->code, 0, 8) .'-'. substr($val->code, 8, 8) .'-'. substr($val->code, 16, 8) .'-'. substr($val->code, 24, 8);
                }
            }
            
            Context::set('coupon_list', $output->data);
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('page_navigation', $output->page_navigation);
            
            // 템플릿 지정
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('coupon_config');
        }
    }
?>
