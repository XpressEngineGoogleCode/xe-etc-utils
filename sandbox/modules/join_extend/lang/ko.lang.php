<?php
    /**
     * @file   ko.lang.php
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief  한국어 언어팩
     **/

	$lang->join_extend = '회원가입확장';
	$lang->join_extend_title = '회원 가입 1단계'; 

    $lang->basic_config = '기본 설정';
    $lang->agree_config = '약관 설정';
    $lang->extend_var_config = '확장변수 연동';
    $lang->restrictions_config = '가입제한 설정';
    $lang->after_config = '가입후 처리';
    $lang->jumin_config = '주민등록번호 설정';

    $lang->input_config = '정보입력 설정';
    $lang->about_input_config = '회원 정보 입력 항목의 필수여부, 수정금지, 길이 제한 등을 할 수 있습니다. <br />확장 변수의 필수여부는 [회원관리]-[가입 폼 관리]에서 직접 설정하시기 바랍니다.<br/>확장변수 연동에 설정한 항목은 이곳의 설정과 상관없이 수정이 금지됩니다.';
    $lang->length = '길이';
    $lang->great_than = '이상';
    $lang->less_than = '이하';
    $lang->no_modification = '수정금지';
    
    $lang->use_join_extend = '회원가입 확장 사용';
    $lang->about_use_join_extend = '회원가입 확장 기능 사용 여부를 선택합니다.';
    
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
	$lang->msg_empty = '%s을 입력하세요.';
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
    $lang->use_welcome = '가입 환영 쪽지';
    $lang->about_use_welcome = '가입한 회원에게 환영 쪽지를 보냅니다.';
    
    $lang->agree_agreement= '이용약관에 동의 합니다.'; 
    $lang->agree_private_agreement= '개인정보취급방침에 동의 합니다.'; 
	$lang->agree = '동의';
	$lang->junior = '%d세 이상';
	$lang->senior = '%d세 미만';

	$lang->msg_check_agree = '약관에 동의가 필요합니다.';
	$lang->session_problem = '세션 에러! 다시 시도해 보세요!';
?>
