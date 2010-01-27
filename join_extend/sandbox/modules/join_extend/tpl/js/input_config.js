function doMark() {
    for(i = 0; i < required.length; i++)    markRequired(required[i]);
}

// 필수항목 표시
function markRequired(name) {
    jQuery("[name="+name+"]").parent().siblings("th").children("div").append('<span class="require">*</span>');
}

// 폼 필터 대체 함수
function my_procFilter(obj, filter) {
    // 기본 필수 항목 확인
    if (!defaultRequired(obj, 'user_id')) return false;
    if (!defaultRequired(obj, 'password1')) return false;
    if (!defaultRequired(obj, 'password2')) return false;
    if (!defaultRequired(obj, 'user_name')) return false;
    if (!defaultRequired(obj, 'nick_name')) return false;
    if (!defaultRequired(obj, 'email_address')) return false;
        
    // 필수 입력 확인
    for(i = 0; i < required.length; i++){
        if (!jQuery("[name="+required[i]+"]").val())   return myAlertMsg(obj, required[i], 'isnull');
    }
    
    // 길이 확인
    for(i = 0; i < length_name.length; i++){
        if (!obj[length_name[i]])   continue;
        minlength = lower_length[i];
        maxlength = upper_length[i];
        if (isNaN(minlength))  minlength = 0;
        if (isNaN(maxlength))  maxlength = 999;
        value = jQuery("[name="+length_name[i]+"]").val();
        if(value.length < minlength || value.length > maxlength) return myAlertMsg(obj, length_name[i], 'outofrange', minlength, maxlength);
    }
    
    return procFilter(obj, filter);
}

// 기본 필수 항목
function defaultRequired(obj, target) {
    var value = jQuery("[name="+target+"]").val();
    if(!value && obj[target]) return myAlertMsg(obj, target,'isnull');
    
    return true;
}

// 포커스
function mySetFocus(obj, target_name) {
    var obj = obj[target_name];
    if(typeof(obj)=='undefined' || !obj) return;

    var length = obj.length;
    try {
        if(typeof(length)!='undefined') {
            obj[0].focus();
        } else {
            obj.focus();
        }
    } catch(e) {
    }
}

// 메시지
function myAlertMsg(obj, target, msg_code, minlength, maxlength) {
    var target_msg = "";

    if(alertMsg[target]!='undefined') target_msg = alertMsg[target];
    else target_msg = target;

    var msg = "";
    if(typeof(alertMsg[msg_code])!='undefined') {
        if(alertMsg[msg_code].indexOf('%s')>=0) msg = alertMsg[msg_code].replace('%s',target_msg);
        else msg = target_msg+alertMsg[msg_code];
    } else {
        msg = msg_code;
    }

    if(typeof(minlength)!='undefined' && typeof(maxlength)!='undefined') msg += "("+minlength+"~"+maxlength+")";

    alert(msg);
    mySetFocus(obj, target);

    return false;
}