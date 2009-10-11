jQuery(function($) {
    jQuery("input[name=user_name]").val(user_name).attr("disabled", "disabled");
    
    try{
        jQuery("#date_birthday").val(birthday).attr("disabled", "disabled");
        jQuery("#date_birthday ~ .inputDate").val(birthday2);
    }catch(e){}
    
    try{
        jQuery("input[name="+sex_var_name+"]").attr("disabled", "disabled");
        jQuery("select[name="+sex_var_name+"]").attr("disabled", "disabled");
        
        jQuery("input[name="+sex_var_name+"][value="+sex+"]:radio").attr('checked','checked');
        jQuery("select[name="+sex_var_name+"] > option[value="+sex+"]").attr('selected','selected');
        jQuery("input[name="+sex_var_name+"]:text").val(sex);
    }catch(e){}
});
