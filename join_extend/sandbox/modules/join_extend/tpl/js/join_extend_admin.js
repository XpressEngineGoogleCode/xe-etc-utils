
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