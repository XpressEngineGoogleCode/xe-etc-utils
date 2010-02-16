
/* 스킨 컬러셋 구해옴 */
function doGetSkinColorset(skin) {
    var params = new Array();
    params['skin'] = skin;

    var response_tags = new Array('error','message','tpl');
    exec_xml('join_extend', 'getJoin_extendAdminColorset', params, doDisplaySkinColorset, response_tags);
}

function doDisplaySkinColorset(ret_obj) {
    var tpl = ret_obj["tpl"];
    var old_height = xHeight("skin_colorset");
    xInnerHtml("skin_colorset", tpl);
    var new_height = xHeight("skin_colorset");
    if(typeof(fixAdminLayoutFooter)=="function") fixAdminLayoutFooter(new_height - old_height);
}

// 테이블 업데이트 시작!
function update_table(fo_obj) {
    jQuery("#pre_update_table").hide();
    jQuery("#ing_update_table").show();
    jQuery("#progress_bar").width(0);
    
    var params = new Array();
    params['start_idx'] = 1;
    params['count'] = 100;
    
    var response_tags = new Array('error','message', 'next_idx', 'percent');
    exec_xml('join_extend','procJoin_extendAdminUpdateTable', params, completeProcessing, response_tags);
    
    return false;
}

// 다음 클릭시
function update_table2(fo_obj) {
    var start_idx = fo_obj.start_idx.value;
    
    jQuery("#btn_next").hide();
    
    var params = new Array();
    params['start_idx'] = start_idx;
    params['count'] = 100;
    
    var response_tags = new Array('error','message', 'next_idx', 'percent');
    exec_xml('join_extend','procJoin_extendAdminUpdateTable', params, completeProcessing, response_tags);
    
    return false;
}

// 완료시
function completeProcessing(ret_obj, response_tags){
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var next_idx = ret_obj['next_idx'];
    var percent = ret_obj['percent'];

    if (error != '0') {
        alert(message);
        return;
    }
    
    // 진행표시
    jQuery("#progress_bar").width( 200 * percent );
    jQuery("#start_idx").val(next_idx);
    jQuery("#percent").html( parseInt(percent * 100) + '%' );
    if (percent >= 0.5)  jQuery("#percent").css("color", "white");
    
    // 다음 진행
    if (percent < 1) {
        update_table2(document.getElementById("next_form"));
    }else{
        alert(message);
    }
}

// 초대장 개별 삭제
function doDeleteInvitation(invitation_srl) {
    var fo_obj = xGetElementById("fo_invitation");
    if(!fo_obj) return;
    fo_obj.invitation_srls.value = invitation_srl;
    procFilter(fo_obj, delete_invitation);
}

// 초대장 일괄 삭제
function doDeleteInvitations() {
    var fo_obj = xGetElementById("invitation_list");
    var invitation_srl = new Array();

    if(typeof(fo_obj.cart.length)=='undefined') {
        if(fo_obj.cart.checked) invitation_srl[invitation_srl.length] = fo_obj.cart.value;
    } else {
        var length = fo_obj.cart.length;
        for(var i=0;i<length;i++) {
            if(fo_obj.cart[i].checked) invitation_srl[invitation_srl.length] = fo_obj.cart[i].value;
        }
    }

    if(invitation_srl.length<1) return;

    var fo_form = xGetElementById("fo_invitation");
    fo_form.invitation_srls.value = invitation_srl.join(',');
    procFilter(fo_form, delete_invitation);
}

// 초대장 삭제 후 
function completeDeleteInvitation(ret_obj) {
    alert(ret_obj['message']);
    location.href = current_url;
}

// 쿠폰 개별 삭제
function doDeleteCoupon(coupon_srl) {
    var fo_obj = xGetElementById("fo_coupon");
    if(!fo_obj) return;
    fo_obj.coupon_srls.value = coupon_srl;
    procFilter(fo_obj, delete_coupon);
}

// 쿠폰 일괄 삭제
function doDeleteCoupons() {
    var fo_obj = xGetElementById("coupon_list");
    var coupon_srl = new Array();

    if(typeof(fo_obj.cart.length)=='undefined') {
        if(fo_obj.cart.checked) coupon_srl[coupon_srl.length] = fo_obj.cart.value;
    } else {
        var length = fo_obj.cart.length;
        for(var i=0;i<length;i++) {
            if(fo_obj.cart[i].checked) coupon_srl[coupon_srl.length] = fo_obj.cart[i].value;
        }
    }

    if(coupon_srl.length<1) return;

    var fo_form = xGetElementById("fo_coupon");
    fo_form.coupon_srls.value = coupon_srl.join(',');
    procFilter(fo_form, delete_coupon);
}

// 쿠폰 삭제 후 
function completeDeleteCoupon(ret_obj) {
    alert(ret_obj['message']);
    location.href = current_url;
}

// 정보입력 설정 검사 (XE의 기본 필터 길이제안 안에서 조절할 수 있다)
function doCheckInputConfig(obj){
    var lower_length = 0;
    var upper_length = 0;
    
    // 아이디 길이 검사
    lower_length = obj.user_id_lower_length.value?obj.user_id_lower_length.value:3;
    upper_length = obj.user_id_upper_length.value?obj.user_id_upper_length.value:20;
    if (lower_length < 3 || upper_length > 20) {
        alert(msg_user_id_length);
        return false;
    }
    
    // 이름 길이 검사
    lower_length = obj.user_name_lower_length.value?obj.user_name_lower_length.value:2;
    upper_length = obj.user_name_upper_length.value?obj.user_name_upper_length.value:40;
    if (lower_length < 2 || upper_length > 40) {
        alert(msg_user_name_length);
        return false;
    }
    
    // 닉네임 길이 검사
    lower_length = obj.nick_name_lower_length.value?obj.nick_name_lower_length.value:2;
    upper_length = obj.nick_name_upper_length.value?obj.nick_name_upper_length.value:40;
    if (lower_length < 2 || upper_length > 40) {
        alert(msg_nick_name_length);
        return false;
    }
    
    // 이메일 길이 검사
    lower_length = obj.email_address_lower_length.value?obj.email_address_lower_length.value:1;
    upper_length = obj.email_address_upper_length.value?obj.email_address_upper_length.value:200;
    if (lower_length < 1 || upper_length > 200) {
        alert(msg_email_length);
        return false;
    }
    
    return procFilter(obj, insert_config)
}