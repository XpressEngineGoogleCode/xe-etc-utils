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
         * @brief 초대장 확인
         **/
        function procJoin_extendInvitation() {
            $args->invitation_code = str_replace('-', '', Context::get('invitation_code'));
            if (empty($args->invitation_code))    return $this->stop('msg_empty_invitation_code');
            
            // 해당 초대장이 있는지 확인
            $output = executeQuery('join_extend.getInvitation', $args);
            if (!$output->toBool()) return $output;
            if (!$output->data) return $this->stop('msg_incorrect_invitation');
            
            // 해당 초대장이 사용된 것인지 확인
            if ($output->data->joindate != "-") return $this->stop('msg_used_invitation');
            
            // 유효기간 확인
            if (!empty($output->data->validdate) && $output->data->validdate < date("Ymd")) return $this->stop(sprintf(Context::getLang('msg_expired_invitation'), zdate($output->data->validdate, "Y-m-d")));
            
            // 초대장 번호 저장
            $_SESSION['join_extend_invitation'] = true;
            $_SESSION['join_extend_invitation_srl'] = $output->data->invitation_srl;
        }
        
        /**
         * @brief 동의
         **/
        function procJoin_extendAgree() {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            
            // 이름 확인
            if ($config->use_jumin == "Y") {
                $name = Context::get('name');
                
                // 한글만 허용
                $result = preg_match("/^[가-힣]*$/", $name);
                if (!$result) {
                    $lang_filter = Context::getLang('filter');
                    return $this->stop(sprintf($lang_filter->invalid_korean, Context::getLang('user_name')));
                }
                
                // 길이 확인
                Context::set('user_name', $name, true);
                $result = $oJoinExtendModel->checkInput();
                if (!$result->toBool()) return $result;
            }
            
            // 중복 확인
            $result = $oJoinExtendModel->isDuplicate();
            if ($result)    return $this->stop('jumin_exist');

            // 성별 확인
            $result = $oJoinExtendModel->isSex();
            if (!$result) {
                return $this->stop(sprintf(Context::getLang('sex_restrictions'), $config->use_sex_restrictions=='M'?Context::getLang('man'):Context::getLang('woman')));
            }

            // 나이제한 확인
            $result = $oJoinExtendModel->isAge();
            if (!$result) {
                if (empty($config->msg_junior_join))
                    return $this->stop(sprintf(Context::getLang('msg_age_restrictions'), $config->age_restrictions, $config->age_upper_restrictions));
                else
                    return $this->stop($config->msg_junior_join);
            }

            // 주민번호 확인
            $result = $oJoinExtendModel->isValid();
            if (!$result)   return $this->stop('invaild_jumin');

            // session 추가
            $oJoinExtendModel->createSession();

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
         * @brief 초대장 사용
         **/
        function procJoin_extendInvitationUse($member_srl) {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            if ($config->use_invitation != "Y") return new Object(0);
            if (!$member_srl)   return new Object(-1, 'member_srl is missing');
            
            if (!empty($_SESSION['join_extend_invitation_srl'])) {
                $args->invitation_srl = $_SESSION['join_extend_invitation_srl'];
                
                // 해당 초대장이 있는지 확인
                $output = executeQuery('join_extend.getInvitation', $args);
                if (!$output->toBool()) return $output;
                if (!$output->data) return new Object(-1, 'msg_incorrect_invitation');
                
                // 해당 초대장이 사용된 것인지 확인
                if ($output->data->joindate != "-") return new Object(-1, 'msg_used_invitation');
            
                // 유효기간 확인
                if (!empty($output->data->validdate) && $output->data->validdate < date("Ymd")) return new Object(-1, sprintf(Context::getLang('msg_expired_invitation'), zdate($output->data->validdate, "Y-m-d")));
            
                $args->invitation_srl = $_SESSION['join_extend_invitation_srl'];
                $args->member_srl = $member_srl;
                $args->joindate = date("YmdHis");
                $output = executeQuery('join_extend.updateInvitation', $args);
                if (!$output->toBool()) return $output;
            }
            
            return new Object(0);
        }
        
        /**
         * @brief 주민번호 입력
         **/
        function procJoin_extendJuminInsert($member_srl) {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            if ($config->use_jumin != "Y")  return true;
            if ($config->save_jumin != "Y") return true;

            if (!$member_srl) return false;
            if (!empty($_SESSION['join_extend_jumin']['jumin'])) {
                $args->jumin = $_SESSION['join_extend_jumin']['jumin'];
                $args->member_srl = $member_srl;
                $output = executeQuery('join_extend.insertJumin', $args);
                if (!$output->toBool())  return false;
            }

            return true;
        }
        
        /**
         * @brief 추천인 포인트 지급
         **/
        function procJoin_extendRecommender($member_srl) {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            if (empty($config->recoid_var_name))    return true;
            
            // 포인트 컨트롤러
            $oPointController = &getController('point');
            
            // 추천인
            $recoid = Context::get($config->recoid_var_name);
            if (empty($recoid)) return true;
            
            $oMemberModel = &getModel('member');
            $recoid_info = $oMemberModel->getMemberInfoByUserID($recoid);
            if (!$recoid_info)  return false;
            
            // 가입한 본인 아이디인지 확인
            if ($recoid_info->member_srl == $member_srl)    return false;
            
            // 추천인 포인트 지급
            if (intVal($config->recoid_point)) {
                $oPointController->setPoint($recoid_info->member_srl, intVal($config->recoid_point), 'add');
            }
            
            // 본인 포인트 지급
            if (intVal($config->joinid_point)) {
                $oPointController->setPoint($member_srl, intVal($config->joinid_point), 'add');
            }
            
            return true;
        }
        
        /**
         * @brief 쿠폰 포인트 지급
         **/
        function procJoin_extendCoupon($member_srl) {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            if (empty($config->coupon_var_name))    return new Object();
            
            // 쿠폰
            $args->coupon_code = str_replace('-', '', Context::get($config->coupon_var_name));
            if (empty($args->coupon_code))    return new Object();
            
            // 포인트 컨트롤러
            $oPointController = &getController('point');
            
            // 해당 쿠폰이 있는지 확인
            $output = executeQuery('join_extend.getCoupon', $args);
            if (!$output->toBool()) return $output;
            if (!$output->data) return $this->stop('msg_incorrect_coupon');

            // 해당 쿠폰이 사용된 것인지 확인
            if ($output->data->joindate != "-") return $this->stop('msg_used_coupon');
            
            // 유효기간 확인
            if (!empty($output->data->validdate) && $output->data->validdate < date("Ymd")) return $this->stop(sprintf(Context::getLang('msg_expired_coupon'), zdate($output->data->validdate, "Y-m-d")));
            
            // 본인 포인트 지급
            if (intVal($output->data->point)) {
                $oPointController->setPoint($member_srl, intVal($output->data->point), 'add');
            }
            
            // 쿠폰 사용 표시
            $args->coupon_srl = $output->data->coupon_srl;
            $args->member_srl = $member_srl;
            $args->joindate = date("YmdHis");
            $output = executeQuery('join_extend.updateCoupon', $args);
            if (!$output->toBool()) return $output;
                
            return new Object();
        }

        /**
         * @brief 가입 환영 쪽지 발송
         **/
        function procSendWelcomeMessage($member_srl) {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            if ($config->use_welcome != "Y")    return;

            // 관리자 정보
            $oMemberModel = &getModel('member');
            $admin_info = $oMemberModel->getMemberInfoByUserID($config->admin_id);
            $admin_member_srl = $admin_info->member_srl;
            
            // 가입자 정보
            $member_info = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
            
            // 쪽지 발송
            if (strlen($config->welcome_title)) $title = $config->welcome_title;
            else                                $title = cut_str($this->unhtmlentities(strip_tags($config->welcome)), 40);
            $content = $config->welcome;
            $oCommunicationController = &getController('communication');
            $oCommunicationController->sendMessage($admin_member_srl, $member_srl, $title, $content, false);
            
            // 메일 발송
            if ($config->use_welcome_email != "Y")  return;
            
            $title = $config->welcome_email_title;
            $content = $config->welcome_email;
            $oMail = new Mail();
            $oMail->setTitle($title);
            $oMail->setContent($content);
            $oMail->setSender($admin_info->user_name, $admin_info->email_address);
            $oMail->setReceiptor($member_info->user_name, $member_info->email_address);
            $oMail->send();
            
//            // 쪽지가 가든 말든 일단 보내고 본다!
//            $receiver_args->message_srl = getNextSequence();
//            $receiver_args->related_srl = 0;
//            $receiver_args->list_order = $receiver_args->message_srl*-1;
//            $receiver_args->sender_srl = $member_srl;
//            $receiver_args->receiver_srl = $member_srl;
//            $receiver_args->message_type = 'R';
//            $receiver_args->title = cut_str($this->unhtmlentities(strip_tags($config->welcome)), 40);
//            $receiver_args->content = $config->welcome;
//            $receiver_args->readed = 'N';
//            $receiver_args->regdate = date("YmdHis");
//            
//            executeQuery('communication.sendMessage', $receiver_args);
//            
//            // 받는 회원의 쪽지 발송 플래그 생성 (파일로 생성)
//            $flag_path = './files/member_extra_info/new_message_flags/'.getNumberingPath($member_srl);
//            FileHandler::makeDir($flag_path);
//            $flag_file = sprintf('%s%s', $flag_path, $member_srl);
//            $flag_count = FileHandler::readFile($flag_file);
//            FileHandler::writeFile($flag_file, ++$flag_count);
        }
        
        /**
         * @brief 관리자 통보 준비
         **/
        function procNotifyAdmin($mode, $member_info) {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            
            // 실행 조건 검사
            if ($mode == 'signin' && !($config->use_notify_admin == 'signin' || $config->use_notify_admin == 'both'))    return;
            if ($mode == 'signout' && !($config->use_notify_admin == 'signout' || $config->use_notify_admin == 'both'))    return;

            // 통보 주기가 있을 경우 정보를 DB에 추가
            if ($config->notify_admin_period == 'collect') {
                $args->mode = $mode;
                $args->member_info = serialize($member_info);
                
                // DB추가(오류는 무시)
                executeQuery('join_extend.insertNotify', $args);
                
                // 통보 개수
                $output = executeQuery('join_extend.getCountNotify');
                $count = $output->data->count;
                
                // 통보할 개수가 되었으면 통보
                if (intVal($count) >= intVal($config->notify_admin_collect_number))
                    $this->procSendNotifyAdmin('DB');
                    
                return;
            }

            // 매번 통보이면 바로 통보
            $this->procSendNotifyAdmin('member_info', $member_info, $mode);
        }
        
        /**
         * @brief 관리자 통보
         **/
        function procSendNotifyAdmin($mode, $member_info = null, $type = 'signin') {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            
            // DB 정보를 이용
            if ($mode == 'DB') {
                // 통보할 정보를 가져온다
                $output = executeQueryArray('join_extend.getNotify');
                $member_infos = $output->data;
                
                // member_info 일렬화를 해제
                if (count($member_infos))
                    for ($i = 0; $i < count($member_infos); $i++)   $member_infos[$i]->member_info = unserialize($member_infos[$i]->member_info);
            
                // DB를 비운다
                executeQuery('join_extend.emptyNotify');
                
            // member_info를 이용
            }else if ($mode == 'member_info' && $member_info) {
                $member_infos[0]->member_info = $member_info;
                $member_infos[0]->mode = $type;
                $member_infos[0]->regdate = date("YmdHis");
            }

            // 통보할 내용이 있는지 확인
            if (!count($member_infos))  return;
            
            // 템플릿을 컴파일하여 내용을 생성
            Context::set('member_infos', $member_infos);
            $oTemplate = &TemplateHandler::getInstance();
            $tpl = $oTemplate->compile('./modules/join_extend/tpl/', 'notify_email');

            // 관리자 정보
            $oMemberModel = &getModel('member');
            $admin_info = $oMemberModel->getMemberInfoByUserID($config->admin_id);
            $admin_member_srl = $admin_info->member_srl;

            // 설정에 따라 쪽지나 메일을 발송
            if ($config->notify_admin_method == 'message' || $config->notify_admin_method == 'both') {
                $title = Context::getLang('notify_title');
                $content = $tpl;
                $oCommunicationController = &getController('communication');
                $oCommunicationController->sendMessage($admin_member_srl, $admin_member_srl, $title, $content, false);
            }
            
            if ($config->notify_admin_method == 'email' || $config->notify_admin_method == 'both') {
                $title = Context::getLang('notify_title');
                $content = $tpl;
                $oMail = new Mail();
                $oMail->setTitle($title);
                $oMail->setContent($content);
                $oMail->setContentType('plain');
                $oMail->setSender($admin_info->user_name, $admin_info->email_address);
                $oMail->setReceiptor($admin_info->user_name, $admin_info->email_address);
                $oMail->send();
            }
        }
        
        /**
         * @brief 회원 DB 추가 후 트리거
         **/
        function triggerInsertMember(&$obj) {
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            if ($config->use_join_extend != 'Y')    return new Object();
            
            $member_srl = $obj->member_srl;
            $oMemberController = &getController('member');

            // join_extend 테이블에 회원정보 추가[주민번호 이동 쿼리가 어차피 동일하니 사용한다.]
            $args->member_srl = $member_srl;
            $output = executeQuery('join_extend.insertJuminToNewTable', $args);
            if (!$output->toBool()) {
                $oMemberController->deleteMember($member_srl);
                 return $output;
            }

            $res = $this->procJoin_extendJuminInsert($member_srl);
            
            // 주민번호 입력에 실패하면 회원가입을 취소
            if (!$res){
                $oMemberController->deleteMember($member_srl);
                return new Object(-1, 'insert_fail_jumin');
            }

            // 초대장
            $res = $this->procJoin_extendInvitationUse($member_srl);
            if (!$res->toBool()){
                $oMemberController->deleteMember($member_srl);
                return $res;
            }
            
            // 추천인 포인트 지급
            $res = $this->procJoin_extendRecommender($member_srl);
            
            // 포인트 지급에 실패하면 회원가입을 취소
            if (!$res){
                $oMemberController->deleteMember($member_srl);
                return new Object(-1, 'point_fail');
            }
            
            // 가입 쿠폰 포인트 지급
            $res = $this->procJoin_extendCoupon($member_srl);
            
            // 포인트 지급에 실패하면 회원가입을 취소
            if (!$res->toBool()){
                $oMemberController->deleteMember($member_srl);
                return $res;
            }
            
            // 회원 가입 환영 쪽지
            $this->procSendWelcomeMessage($member_srl);
            
            // 관리자 통보
            $oMemberModel = &getModel('member');
            $member_info = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
            $this->procNotifyAdmin('signin', $member_info);
            
            unset($_SESSION['join_extend_authed_act']);
            unset($_SESSION['join_extend_jumin']);

            return new Object();
        }
        
        /**
         * @brief 회원 DB 삭제 전 트리거
         **/
        function triggerDeleteMember(&$obj) {
            $member_srl = $obj->member_srl;

            // 회원정보
            $oMemberModel = &getModel('member');
            $member_info = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
            
            // join_extend 테이블에서 회원정보 삭제
            $args->member_srl = $member_srl;
            $output = executeQuery('join_extend.deleteMemberInfo', $args);
            if (!$output->toBool()) return $output;
            
            // 관리자 통보
            // 회원확장 모듈에서 회원가입 중단시에도
            // deleteMember를 실행하지만 이때는 통보하지 않는다.
            if (Context::get('act') != 'procMemberInsert')
                $this->procNotifyAdmin('signout', $member_info);
            
            return new Object();
        }
        
        /**
         * @brief 모듈핸들러 실행 후 트리거 (애드온의 after_module_proc에 대응)
         **/
        function triggerModuleHandlerProc(&$oModule) {
            // member 모듈 옵션
            $oMemberModel = &getModel('member');
            $member_config = $oMemberModel->getMemberConfig();
            
            // 모듈 설정
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            if ($config->use_join_extend != 'Y')    return new Object();
            
            // 회원가입시
            if(Context::get('act') == "dispMemberSignUpForm"){
                // 회원 DB 업데이트 되었는지 확인
                $is_update_table = $oJoinExtendModel->isUpdateTable();
                if (!$is_update_table)   return new Object(-1, 'request_update_table');

                // 초대장
                // 회원가입 허용되어 있지 않거나 초대장 기능 사용안하면 생략
                if (!Context::get('logged_info') && $member_config->enable_join == "Y" && $config->use_invitation == "Y") {
                    if (!$_SESSION['join_extend_invitation']){
                        
                        Context::addHtmlHeader(sprintf('<script type="text/javascript"> var msg_empty_invitation_code ="%s"; </script>', Context::getLang('msg_empty_invitation_code')));
                    
                        // change module template
                        Context::addJsFile('./modules/join_extend/tpl/js/invitation.js',false);
                        if ($config->skin)
                            $addon_tpl_path = sprintf('./modules/join_extend/skins/%s/', $config->skin);
                        else
                            $addon_tpl_path = './modules/join_extend/skins/default/';
        
                        $addon_tpl_file = 'invitation.html';
                        
                        // 템플릿파일 존재하는지 확인(기존 스킨 호환을 위해 존재하지 않으면 기본 스킨의 템플릿 사용)
                        if (!file_exists($addon_tpl_path. $addon_tpl_file))
                            $addon_tpl_path = './modules/join_extend/skins/default/';
                            
                        $oModule->setTemplatePath($addon_tpl_path);
                        $oModule->setTemplateFile($addon_tpl_file);
                        
                        unset($_SESSION['join_extend_authed']);
                        
                        return new Object();
                    }
                }

                // 로그인 상태이거나 약관, 개인정보, 주민번호 모두 사용하지 않거나 회원가입 허용되어 있지 않으면 1단계 화면은 생략
                if (Context::get('logged_info') || ($config->use_jumin != "Y" && $config->use_agreement != "Y" && $config->use_private_agreement != "Y") || $member_config->enable_join != "Y") {
                    $_SESSION['join_extend_authed'] = true;
                }

                // 회원가입 1단계
                if(!$_SESSION['join_extend_authed']){
                    Context::set('config', $config);
    
                    Context::addHtmlHeader(sprintf('<script type="text/javascript"> var msg_junior_join ="%s"; var msg_check_agree ="%s"; var msg_empty_name = "%s"; var msg_empty_jumin1 = "%s"; var msg_empty_jumin2 = "%s"; var use_jumin = "%s"; var about_user_name = "%s"; </script>',
                        trim($config->msg_junior_join),Context::getLang('msg_check_agree'), 
                        sprintf(Context::getLang('msg_empty'), Context::getLang('name')),
                        sprintf(Context::getLang('msg_empty'), Context::getLang('jumin1')),
                        sprintf(Context::getLang('msg_empty'), Context::getLang('jumin2')),
                        $config->use_jumin,
                        Context::getLang('about_user_name')
                    ));
    
                    // change module template
                    Context::addJsFile('./modules/join_extend/tpl/js/member_join_extend.js',false);
                    if ($config->skin)
                        $addon_tpl_path = sprintf('./modules/join_extend/skins/%s/', $config->skin);
                    else
                        $addon_tpl_path = './modules/join_extend/skins/default/';
    
                    $addon_tpl_file = 'member_join_extend.html';
                    
                    $oModule->setTemplatePath($addon_tpl_path);
                    $oModule->setTemplateFile($addon_tpl_file);

                    unset($_SESSION['join_extend_jumin']);
                    unset($_SESSION['join_extend_invitation']);
                    
                // 회원가입 2단계
                }else{
                    // 추천인 아이디
                    if (!empty($config->recoid_var_name) && Context::get('recoid')) {
                        Context::addHtmlHeader(sprintf('<script type="text/javascript"> var recoid_var_name2 ="%s"; var recoid = "%s"; </script>', 
                                                        $config->recoid_var_name,
                                                        Context::get('recoid')));
                        Context::addJsFile('./modules/join_extend/tpl/js/fix_name.js',false);
                    }
                    
                    // 주민번호를 입력받고 성별 정보가 있으면 자동으로 선택한다.
                    if ($config->use_jumin == "Y" && !empty($config->sex_var_name)) {
                        Context::addHtmlHeader(sprintf('<script type="text/javascript"> var sex_var_name ="%s"; var sex = "%s"; </script>', 
                                                        $config->sex_var_name,
                                                        $_SESSION['join_extend_jumin']['sex']));
                    }
                    
                    // 주민번호를 입력받고 나이 정보가 있으면 자동으로 입력한다.
                    if ($config->use_jumin == "Y" && !empty($config->age_var_name)){
                        Context::addHtmlHeader(sprintf('<script type="text/javascript"> var age_var_name ="%s"; var age = "%s"; </script>', 
                                                        $config->age_var_name,
                                                        $_SESSION['join_extend_jumin']['age']));
                    }
                    
                    // 주민번호를 입력받으면 이름을 고정시킨다.
                    if ($config->use_jumin == "Y") {
                        Context::addHtmlHeader(sprintf('<script type="text/javascript"> var user_name ="%s"; var birthday = "%s"; var birthday2 = "%s"; </script>', 
                                                        $_SESSION['join_extend_jumin']['name'],
                                                        $_SESSION['join_extend_jumin']['birthday'],
                                                        $_SESSION['join_extend_jumin']['birthday2']));
                        Context::addJsFile('./modules/join_extend/tpl/js/fix_name.js',false);
                        
                        // 가입화면에서도 생일 수정금지일 때
                        if ($config->input_config->no_mod['birthday'] == "Y2") {
                            unset($_SESSION['join_extend_no_mod']);
                            $_SESSION['join_extend_no_mod']['birthday'] = $_SESSION['join_extend_jumin']['birthday'];
                            
                            Context::addHtmlHeader('<script type="text/javascript"> var no_mod = new Array(); var no_mod_type = new Array(); no_mod[0] = "birthday"; no_mod_type[0] = "date"; </script>');
                            Context::addJsFile('./modules/join_extend/tpl/js/no_mod.js',false);
                        }
                    }
    
                    // 필수입력 항목, 길이 제한 메시지
                    $oJoinExtendModel->procRequiredLength();
                    
                    unset($_SESSION['join_extend_authed']);
                    unset($_SESSION['join_extend_invitation']);
                    $_SESSION['join_extend_authed_act'] = true;
                }
    
            // 회원 정보 수정 화면 주민번호 사용시 이름 변경 금지!
            }else if (Context::get('act') == 'dispMemberModifyInfo'){
                $member_info = Context::get('member_info');
                
                if (!empty($config->recoid_var_name)) {
                    Context::addHtmlHeader(sprintf('<script type="text/javascript"> var recoid_var_name ="%s"; </script>', $config->recoid_var_name));
                    if (!$member_info->{$config->recoid_var_name}) $member_info->{$config->recoid_var_name} = '';
                    $_SESSION['join_extend_jumin']['recoid'] = $member_info->{$config->recoid_var_name};
                }

                if ($config->use_jumin == "Y" && !empty($config->age_var_name)) {
                    Context::addHtmlHeader(sprintf('<script type="text/javascript"> var age_var_name ="%s"; </script>', $config->age_var_name));
                    if (!$member_info->{$config->age_var_name}) $member_info->{$config->age_var_name} = '';
                    $_SESSION['join_extend_jumin']['age'] = $member_info->{$config->age_var_name};
                }                
                
                if ($config->use_jumin == "Y" && !empty($config->sex_var_name)) {
                    Context::addHtmlHeader(sprintf('<script type="text/javascript"> var sex_var_name ="%s"; </script>', $config->sex_var_name));
                    if (!$member_info->{$config->sex_var_name}) $member_info->{$config->sex_var_name} = '';
                    $_SESSION['join_extend_jumin']['sex'] = $member_info->{$config->sex_var_name};
                }
                
                if ($config->use_jumin == "Y") {
                    Context::addHtmlHeader(sprintf('<script type="text/javascript"> var user_name ="%s";  </script>', $member_info->user_name));
                    Context::addJsFile('./modules/join_extend/tpl/js/fix_name.js',false);
                    $_SESSION['join_extend_jumin']['name'] = $member_info->user_name;
                }
                
                // 수정금지
                unset($_SESSION['join_extend_no_mod']);
                if (count($config->input_config->no_mod)) {
                    $i = 0;
                    foreach($config->input_config->no_mod as $var_name => $val) {
                        if (!($val == "Y" || $val == "Y2"))    continue;
                        $js_str .= "no_mod[$i] = '$var_name';";
                        $js_str .= "no_mod_type[$i] = '{$config->input_config->type[$var_name]}';";
                        if (!$member_info->{$var_name}) $member_info->{$var_name} = '';
                        $_SESSION['join_extend_no_mod'][$var_name] = $member_info->{$var_name};
                        $i++;
                    }
                }
                Context::addHtmlHeader(sprintf('<script type="text/javascript"> var no_mod = new Array(); var no_mod_type = new Array(); %s </script>', $js_str));
                Context::addJsFile('./modules/join_extend/tpl/js/no_mod.js',false);
                
                // 필수입력 항목, 길이 제한 메시지
                $oJoinExtendModel->procRequiredLength();
            }
        
            return new Object();
        }
        
        /**
         * @brief 모듈 핸들러 초기화 후 트리거 (애드온의 before_module_proc에 대응)
         **/
        function triggerModuleHandlerInit(&$module_info){
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            if ($config->use_join_extend != 'Y')    return new Object();
            
            // 실제 가입시 체크
            if(Context::get('act')=='procMemberInsert'){
                // 회원 DB 업데이트 되었는지 확인
                $is_update_table = $oJoinExtendModel->isUpdateTable();
                if (!$is_update_table)   return new Object(-1, 'request_update_table');
                    
                // session 체크
                if(!$_SESSION['join_extend_authed_act']){
                    $this->xmlMessage('msg_not_permitted');
                }
    
                // 세션 체크
                $res = $oJoinExtendModel->checkSession();

                if ($res)   $this->xmlMessage($res);
                
                // 입력 항목 체크
                $output = $oJoinExtendModel->checkInput();
                if (!$output->toBool())  $this->xmlMessage($output->message);
                
                // 입력 항목 수정 체크
                $output = $oJoinExtendModel->checkInputMod();
                if (!$output->toBool())  $this->xmlMessage($output->message);
                
            // 회원 정보 수정 시
            }else if (Context::get('act') == 'procMemberModifyInfo') {
                // 세션 체크
                $res = $oJoinExtendModel->checkSession();
                if ($res)   $this->xmlMessage($res);
                
                // 입력 항목 체크
                $output = $oJoinExtendModel->checkInput();
                if (!$output->toBool())  $this->xmlMessage($output->message);
                
                // 입력 항목 수정 체크
                $output = $oJoinExtendModel->checkInputMod();
                if (!$output->toBool())  $this->xmlMessage($output->message);
            }
            
            return new Object();
            
        }
        
        /**
         * @brief 출력 전 트리거 (애드온의 before_display_content에 대응)
         **/
        function triggerDisplay(&$output){
            $oJoinExtendModel = &getModel('join_extend');
            $config = $oJoinExtendModel->getConfig();
            if ($config->use_join_extend != 'Y')    return new Object();
            
            if (Context::getResponseMethod() == 'HTML' && in_array(Context::get('act'), array("dispMemberSignUpForm", "dispMemberModifyInfo"))) {
                // 폼 필터 변경 (구버전의 js filter 사용시만 변경한다.)
                if (__ZBXE_VERSION__ < '1.4') {
                    $output = str_replace('procFilter(this, signup)', 'my_procFilter(this, signup)', $output);
                    $output = str_replace('procFilter(this, modify_info)', 'my_procFilter(this, modify_info)', $output);
                }
                
                if (empty($config->recoid_var_name))    return new Object();
                
                // 추천인 포인트
                $output = str_replace('$recoid_point', intVal($config->recoid_point), $output);
                
                // 추천 포인트
                $output = str_replace('$joinid_point', intVal($config->joinid_point), $output);
                
                // 포인트 단위
                $oModuleModel = &getModel('module');
                $point_config = $oModuleModel->getModuleConfig('point');
                $output = str_replace('$point_name', $point_config->point_name, $output);
            }
            
            return new Object();
        }
        
        /**
         * @brief XML RPC 메시지 출력
         **/
        function xmlMessage($msg){
            header("Content-Type: text/xml; charset=UTF-8");
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            printf("<response>\r\n<error>-1</error>\r\n<message>%s</message>\r\n</response>", Context::getLang($msg));

            Context::close();
            exit();
        }
        
        /**
         * @brief html_entity_docode 대체 함수
         **/
        function unhtmlentities($string)
        {
            $string = str_replace('&nbsp;', '', $string);
            // 숫자 엔티티 치환
            $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
            // 문자 엔티티 치환
            $trans_tbl = get_html_translation_table(HTML_ENTITIES);
            $trans_tbl = array_flip($trans_tbl);
            return strtr($string, $trans_tbl);
        }
    }
?>
