(function($){
    var validator = xe.getApp('Validator')[0];
    if(!validator) return false;
    
    // 기존 필터를 얻는다.
    var og_filter = validator.cast("GET_FILTER", ['signup']);
    if (!og_filter) og_filter = validator.cast("GET_FILTER", ['modify_info']);
    
    for (i = 0; i < var_name.length; i++){
        var name = var_name[i];
        
        // 필수 입력 반영
        if (required[name]) {
            if (!og_filter[name])   og_filter[name] = {required: true};
            else {
                var og_obj = og_filter[name];
                og_obj.required = true;
                og_filter[name] = og_obj;
            }
        }
        
        // 길이 제한 반영
        if (lower_length[name] || upper_length[name]){
            var minlength = lower_length[name];
            var maxlength = upper_length[name];
            
            if (isNaN(minlength))   minlength = null;
            if (isNaN(maxlength))   maxlength = null;
            
            if (!og_filter[name]){
                var new_obj = new Object;
                
                if (minlength !== null) new_obj.minlength = minlength;
                if (maxlength !== null) new_obj.maxlength = maxlength;
                
                og_filter[name] = new_obj;
            }else{
                var og_obj = og_filter[name];
            
                if (minlength !== null) og_obj.minlength = minlength;
                if (maxlength !== null) og_obj.maxlength = maxlength;
                
                og_filter[name] = og_obj;
            }
        }
    }
    
    // 새 필터 적용
    try{
        validator.cast("ADD_FILTER", ['signup', og_filter]);
        validator.cast("ADD_FILTER", ['signup', modify_info]);
    }catch(e){ ; }
})(jQuery);

function doMark() {
    for(name in required)    markRequired(name);
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
    
    for (i = 0; i < var_name.length; i++){
        var name = var_name[i];
        
        // 필수 입력 확인
        if (required[name]) {
            if (!jQuery("[name="+name+"]").val())   return myAlertMsg(obj, name, 'isnull');
        }
        
        // 길이 확인
        if (lower_length[name] || upper_length[name]){
            if (!obj[name])   continue;
            minlength = lower_length[name];
            maxlength = upper_length[name];
            if (isNaN(minlength))  minlength = 0;
            if (isNaN(maxlength))  maxlength = 999;
            value = jQuery("[name="+name+"]").val();
            if(value.length < minlength || value.length > maxlength) return myAlertMsg(obj, name, 'outofrange', minlength, maxlength);
        }
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