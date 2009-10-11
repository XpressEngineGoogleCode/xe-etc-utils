jQuery(function($) {
    jQuery("input[name=user_name]").val(user_name).attr("disabled", "disabled");
    try{
        jQuery("#date_birthday").val(birthday).attr("disabled", "disabled");
        jQuery("#date_birthday ~ .inputDate").val(birthday2);
    }catch(e){}
});
