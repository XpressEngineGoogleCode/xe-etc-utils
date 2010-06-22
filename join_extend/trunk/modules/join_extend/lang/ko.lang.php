<?php
    /**
     * @file   ko.lang.php
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief  한국어 언어팩
     **/

    $lang->join_extend = '회원가입확장';
    $lang->run_update = '업데이트 되었습니다. 추가로 회원가입 확장 설정으로 가서 테이블 업데이트를 실행하세요.';
    $lang->request_update_table = '회원 DB 업데이트가 필요하여 가입이 제한되었습니다. 관리자에게 요청하세요';
    $lang->admin_request_update_table = '회원 DB 업데이트가 필요합니다.';
    
    $lang->update_table = '테이블 업데이트';
    $lang->about_update_table = 'Ver. 0.4.2부터 회원가입 확장을 위한 별도의 테이블을 이용합니다.<br/>테이블 업데이트를 클릭하면 기존 member 테이블에 있는 주민등록번호 정보를 새로운 테이블로 옮깁니다.<br/>데이터는 단계적으로 처리됩니다. 완료가 될때까지 기다리세요.<br/><span style="color: red"><strong>반드시 회원 DB 백업 후 실행하세요.</strong></span>';
    $lang->complete_update_table = '회원 DB 업데이트가 완료되었습니다.';
    
    $lang->join_extend_title = '회원 가입 1단계'; 

    $lang->basic_config = '기본 설정';
    $lang->agree_config = '약관 설정';
    $lang->extend_var_config = '확장변수 연동';
    $lang->restrictions_config = '가입제한 설정';
    $lang->after_config = '가입/탈퇴 후 처리';
    $lang->jumin_config = '주민등록번호 설정';

    $lang->input_config = '정보입력 설정';
    $lang->about_input_config = '회원 정보 입력 항목의 필수여부, 수정금지, 길이 제한 등을 할 수 있습니다. <br />확장 변수의 필수여부는 [회원관리]-[가입 폼 관리]에서 직접 설정하시기 바랍니다.<br/>확장변수 연동에 설정한 항목은 이곳의 설정과 상관없이 수정이 금지됩니다.';
    $lang->length = '길이';
    $lang->great_than = '이상';
    $lang->less_than = '이하';
    $lang->no_modification = '수정금지';
    $lang->birthday_no_mod_op1 = '수정금지(가입시 가능)';
    $lang->birthday_no_mod_op2 = '수정금지';
    $lang->msg_user_id_length = '아이디 길이 제한은 3~20 사이에서 가능합니다.';
    $lang->msg_user_name_length = '이름 길이 제한은 2~40 사이에서 가능합니다.';
    $lang->msg_nick_name_length = '닉네임 길이 제한은 2~40 사이에서 가능합니다.';
    $lang->msg_email_length = '이메일 길이 제한은 1~200 사이에서 가능합니다.';
    $lang->msg_length = '글자 길이(%s)';
    
    $lang->my_about_user_id = '사용자 ID는 %s자 사이의 영문+숫자로 이루어져야 하며 영문으로 시작되어야 합니다.';
    $lang->my_about_user_name = '이름은 %s자 이내여야 합니다.';
    $lang->my_about_nick_name = '닉네임은 %s자 이내여야 합니다.';
    
    $lang->use_join_extend = '회원가입 확장 사용';
    $lang->about_use_join_extend = '회원가입 확장 기능 사용 여부를 선택합니다.';
    $lang->about_admin_id_2 = '이곳에 설정한 관리자 아이디가 환영 쪽지 등의 발송자로 사용됩니다.';
    
    $lang->use_agreement = '이용약관 표시';
    $lang->about_use_agreement = '이용약관을 표시 및 동의를 받습니다.';
    $lang->agreement = '이용약관';

    $lang->use_private_agreement = '개인정보취급방침 표시';
    $lang->about_use_private_agreement = '개인정보취급방침을 표시 및 동의를 받습니다.';
    $lang->private_agreement = '개인정보취급방침';
    $lang->private_gathering_agreement = '개인정보 수집 및 이용';

    $lang->use_jumin = '주민등록번호 받기';
    $lang->about_use_jumin = '주민등록번호를 받습니다.';
    $lang->jumin = '주민등록번호';
    $lang->name = '이름';
    $lang->msg_empty = '%s의 값을 입력하세요.';
    $lang->jumin_check = '실명확인';
    $lang->about_jumin = '주민등록번호는 중복가입을 막기 위해 사용됩니다.';
    $lang->jumin1 = '주민등록번호 앞부분';
    $lang->jumin2 = '주민등록번호 뒷부분';
    $lang->insert_fail_jumin = '주민등록번호 저장에 실패했습니다.';
    $lang->invaild_jumin = '잘못된 주민등록번호입니다.';
    $lang->jumin_exist = '입력한 주민등록번호는 이미 가입되어 있습니다.';
    
    $lang->save_jumin = '주민등록번호 저장하기';
    $lang->about_save_jumin = '입력받은 주민등록번호를 저장할지 여부를 선택합니다.<br/>저장할 경우 MD5 해시를 이용하여 암호화되어 저장되며 주민등록번호를 이용하여 중복가입을 막을 수 있습니다. <br/>저장하지 않을 경우 주민등록번호 유효성 검사만 수행하며 중복가입을 막을 수는 없습니다.';
    $lang->msg_save_jumin = '주민등록번호는 중복가입을 막기 위해 사용되며, 관리자가 볼 수 없도록 암호화되어 저장됩니다.';
    $lang->msg_not_save_jumin = '주민등록번호는 실명확인을 위해 사용되며 저장되지 않습니다.';

    $lang->use_sex_restrictions = '성별 제한 사용';
    $lang->about_use_sex_restrictions = '설정된 성별만 가입을 받습니다.';
    $lang->man = '남';
    $lang->woman = '여';
    $lang->sex_var_name = '성별 확장 변수명';
    $lang->about_sex_var_name = '주민등록번호를 이용하여 성별정보를 자동으로 입력합니다. <br/>회원 관리 - 가입 폼 관리에 추가된 성별 정보의 <strong>입력항목 이름</strong>을 입력하세요. <br/>사용하지 않을 경우 비워두세요.';
    $lang->man_value = '남성 값';
    $lang->about_man_value = '남성에 대해 설정한 값을 정확히 동일하게 입력하세요.';
    $lang->woman_value = '여성 값';
    $lang->about_woman_value = '여성에 대해 설정한 값을 정확히 동일하게 입력하세요.';
    $lang->sex_restrictions = '%s성만 가입할 수 있습니다.';
    $lang->sex_restrictions_m = '남성만 가입할 수 있습니다.';
    $lang->sex_restrictions_w = '여성만 가입할 수 있습니다.';

    $lang->age_var_name = '나이 확장 변수명';
    $lang->about_age_var_name = '주민등록번호를 이용하여 나이정보를 자동으로 입력합니다. <br/>회원 관리 - 가입 폼 관리에 추가된 나이 정보의 <strong>입력항목 이름</strong>을 입력하세요. <br/>사용하지 않을 경우 비워두세요.';
        
    $lang->use_age_restrictions = '나이제한 사용';
    $lang->about_use_age_restrictions = '아래 설정된 나이만 가입을 받습니다. (만나이)';
    $lang->age_restrictions = '나이제한';
    $lang->msg_age_restrictions = '나이제한으로 가입할 수 있습니다. (만 %s~%s)';
    $lang->msg_junior_join = '나이제한 메시지';

    $lang->recoid_var_name = '추천인 ID 확장 변수명';
    $lang->about_recoid_var_name = '추천인 ID에 포인트를 지급합니다. <br/>회원 관리 - 가입 폼 관리에 추가된 추천인 ID의 <strong>입력항목 이름</strong>을 입력하세요. <br/>사용하지 않을 경우 비워두세요.';
    $lang->recoid_point = '추천인 포인트';
    $lang->about_recoid_point = '추천된 회원에게 지급될 포인트입니다.';
    $lang->joinid_point = '추천 포인트';
    $lang->about_joinid_point = '추천인 ID을 작성한 회원에게 지급될 포인트입니다.';
    $lang->point_fail = '추천인 ID를 이용한 포인트 지급에 실패했습니다. 추천인 ID가 존재하지 않을 수 있습니다.';
    
    $lang->welcome = '가입 환영 쪽지 내용';
    $lang->welcome_title = '가입 환영 쪽지 제목';
    $lang->about_welcome_title = '가입 확영 쪽지 제목을 입력하세요. 내용은 아래에서 별도로 작성하세요.';
    $lang->use_welcome = '가입 환영 쪽지';
    $lang->about_use_welcome = '가입한 회원에게 환영 쪽지를 보냅니다.';
    $lang->welcome_email = '가입 환영 메일 내용';
    $lang->welcome_email_title = '가입 환영 메일 제목';
    $lang->about_welcome_email_title = '가입 환영 메일 제목을 입력하세요. 내용은 아래에서 별로도 작성하세요.';
    $lang->use_welcome_email = '가입 환영 메일';
    $lang->about_use_welcome_email = '가입한 회원에게 환영 메일를 보냅니다.';
    $lang->use_notify_admin = '관리자 통보 기능';
    $lang->about_use_notify_admin = '이용자가 회원가입이나 회원탈퇴 시 관련 정보를 관리자에게 통보합니다.';
    $lang->only_signin = '회원가입만';
    $lang->only_signout = '회원탈퇴만';
    $lang->both = '모두';
    $lang->notify_admin_period = '관리자 통보 주기';
    $lang->about_notify_admin_period = '관리자 통보 기능의 통보 주기를 선택하세요.';
    $lang->notify_each_time = '매번 통보';
    $lang->notify_collect = '모아서 통보';
    $lang->notify_admin_method = '관리자 통보 방법';
    $lang->about_notify_admin_method = '관리자에게 어떤 방법으로 통보할지 선택하세요.';
    $lang->message = '쪽지';
    $lang->email = '이메일';
    $lang->notify_admin_collect_number = '모아서 통보할 개수';
    $lang->about_notify_admin_collect_number = '이곳에 설정한 개수만큼 통보가 쌓이면 모아서 관리자에게 통보합니다. (기본값 10개)';
    $lang->notify_type = '구분';
    $lang->notify_title = '가입/탈퇴 통보';
    
    $lang->agree_agreement= '이용약관에 동의 합니다.'; 
    $lang->agree_private_agreement= '개인정보취급방침에 동의 합니다.'; 
    $lang->agree = '동의';
    $lang->junior = '%d세 이상';
    $lang->senior = '%d세 미만';

    $lang->msg_check_agree = '약관에 동의가 필요합니다.';
    $lang->session_problem = '세션 에러! 다시 시도해 보세요!';
    
    $lang->invitation_config = '초대장 설정';
    $lang->use_invitation = '초대장 기능 사용';
    $lang->about_use_invitation = '초대장 기능을 사용하면 초대장 번호를 가진 사람만 가입할 수 있습니다.';
    $lang->unit_number = '개';
    $lang->generate_invitation = '초대장 생성';
    $lang->about_generate_invitation = '한번에 생성할 수 있는 초대장의 최대 개수는 100개입니다. 유효기간을 설정하여 생성된 초대장은 해당 기간까지만 유효합니다. 유효기간을 비워두면 유효기간이 없는 초대장이 생성됩니다.';
    $lang->msg_invitation_incorrect_count = '1~100 사이를 입력해주세요.';
    $lang->invitation_code = '초대장 번호';
    $lang->invitation_join_id = '가입 아이디';
    $lang->invitation_joindate = '가입일';
    $lang->view = '보기';
    $lang->my_view_all = '모두';
    $lang->view_not_use = '사용안됨';
    $lang->join_extend_invitation = '초대장 확인';
    $lang->msg_invitation = '초대장 번호를 입력해주세요';
    $lang->msg_empty_invitation_code = '초대장 번호를 입력해주세요';
    $lang->msg_incorrect_invitation = '유효하지 않은 초대장 번호입니다';
    $lang->msg_used_invitation = '이미 사용된 초대장 번호입니다.';
    $lang->insert_fail_invitation = '초대장 처리 에러';
    $lang->deleted_member = '탈퇴한 회원';
    $lang->validdate = '유효기간';
    $lang->msg_validdate_past = '유효기간으로 과거 날짜를 사용할 수 없습니다.';
    $lang->msg_expired_invitation = '유효기간이 지난 초대장입니다. (%s)';
    
    $lang->coupon_config = '가입쿠폰 설정';
    $lang->use_coupon = '가입쿠폰 사용';
    $lang->coupon_var_name = '쿠폰 확장 변수명';
    $lang->about_coupon_var_name = '회원 관리 - 가입 폼 관리에 추가된 쿠폰 입력칸의 <strong>입력항목 이름</strong>을 입력하세요.';
    $lang->about_use_coupon = '가입쿠폰 기능을 사용하면 가입시 쿠폰번호를 입력한 회원은 쿠폰의 포인트를 지급 받습니다.';
    $lang->generate_coupon = '가입쿠폰 생성';
    $lang->receive_point = '받는 포인트';
    $lang->about_generate_coupon = '한번에 생성할 수 있는 쿠폰의 최대 개수는 100개입니다. 유효기간을 설정하여 생성된 쿠폰은 해당 기간까지만 유효합니다. 유효기간을 비워두면 유효기간이 없는 쿠폰이 생성됩니다.';
    $lang->msg_invalid_number = '숫자로 입력하세요.';
    $lang->msg_incorrect_coupon = '유효하지 않은 쿠폰 번호입니다.';
    $lang->msg_used_coupon = '이미 사용된 쿠폰 번호입니다.';
    $lang->msg_expired_coupon = '유효기간이 지난 쿠폰입니다. (%s)';
    
?>
