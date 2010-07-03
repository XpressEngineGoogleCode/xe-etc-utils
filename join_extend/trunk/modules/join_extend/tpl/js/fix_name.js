jQuery(function($) {
    try{
        jQuery("input[name=user_name]").val(user_name).attr("readonly", "readonly");
    }catch(e){}
    
    try{
        jQuery("#date_birthday").val(birthday).attr("readonly", "readonly");
        jQuery("#date_birthday ~ .inputDate").val(birthday2);
    }catch(e){}
    
    try{
        jQuery("input[name="+sex_var_name+"]:text").attr("readonly", "readonly");
        jQuery("input[name="+sex_var_name+"]:radio").attr("disabled", "disabled");
        jQuery("select[name="+sex_var_name+"]").attr("disabled", "disabled").after('<input type="hidden" name="'+sex_var_name+'" value="'+sex+'" />');
        
        jQuery("input[name="+sex_var_name+"][value="+sex+"]:radio").attr('checked','checked').after('<input type="hidden" name="'+sex_var_name+'" value="'+sex+'" />');
        jQuery("select[name="+sex_var_name+"] > option[value="+sex+"]").attr('selected','selected');
        jQuery("input[name="+sex_var_name+"]:text").val(sex);
    }catch(e){}
    
    try{
        jQuery("input[name="+age_var_name+"]").attr("readonly", "readonly");
        jQuery("input[name="+age_var_name+"]").val(age);
    }catch(e){}

    try{
        jQuery("input[name="+recoid_var_name+"]").attr("readonly", "readonly");
    }catch(e){}

    try{
        jQuery("input[name="+recoid_var_name2+"]").val(recoid);
    }catch(e){}
});
