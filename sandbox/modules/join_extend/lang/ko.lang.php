<?php
    /**
     * @file   ko.lang.php
     * @author zero (zero@nzeo.com)
     * @brief  한국어 언어팩 (기본적인 내용만 수록)
     **/

	$lang->join_extend = '회원가입확장';
	$lang->join_extend_title = '회원 가입을 위한 약관 동의 절차'; 

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
    $lang->about_save_jumin = '입력받은 주민등록번호를 저장할지 여부를 선택합니다. 저장할 경우 MD5 해시를 이용하여 암호화되어 저장되며 주민등록번호를 이용하여 중복가입을 막을 수 있습니다. 저장하지 않을 경우 주민등록번호 유효성 검사만 수행하며 중복가입을 막을 수는 없습니다.';

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
    $lang->sex_restrictions = '성별제한';
    $lang->sex_restrictions_m = '남성만 가입할 수 있습니다.';
    $lang->sex_restrictions_w = '여성만 가입할 수 있습니다.';

    $lang->age_var_name = '나이 확장 변수명';
    $lang->about_age_var_name = '주민등록번호를 이용하여 나이정보를 자동으로 입력합니다. <br/>회원 관리 - 가입 폼 관리에 추가된 나이 정보의 <strong>입력항목 이름</strong>을 입력하세요. <br/>사용하지 않을 경우 비워두세요.';
        
	$lang->use_age_restrictions = '나이제한 사용';
	$lang->about_use_age_restrictions = '아래 설정된 나이 이상만 가입을 받습니다.';
	$lang->age_restrictions = '나이제한';
	$lang->msg_junior_join = '나이제한 미만 메시지';

    $lang->agree_agreement= '이용약관에 동의 합니다.'; 
    $lang->agree_private_agreement= '개인정보취급방침에 동의 합니다.'; 
	$lang->agree = '동의';
	$lang->junior = '%d세 이상';
	$lang->senior = '%d세 미만';

	$lang->msg_check_agree = '약관에 동의가 필요합니다.';
?>
