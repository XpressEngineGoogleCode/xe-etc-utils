jQuery(function($) {
    for (i = 0; i < no_mod.length; i++) {
        switch (no_mod_type[i]) {
            case 'text': case 'homepage': case 'email_address': case 'tel':
                if (jQuery("input[name="+no_mod[i]+"]").val())
                    jQuery("input[name="+no_mod[i]+"]").attr("disabled", "disabled");
                break;
            case 'radio': case 'checkbox':
                if (jQuery("input[name="+no_mod[i]+"]").attr("checked"))
                    jQuery("input[name="+no_mod[i]+"]").attr("disabled", "disabled");
                break;
            case 'select':
                if (jQuery("select[name="+no_mod[i]+"]").val())
                    jQuery("select[name="+no_mod[i]+"]").attr("disabled", "disabled");
                break;
            case 'kr_zip':
                if (jQuery("input[name="+no_mod[i]+"]").val()) {
                    jQuery("input[name="+no_mod[i]+"]").attr("disabled", "disabled");
                    jQuery("input[name="+no_mod[i]+"] ~ a").css('display', 'none');
                }
                break;
            case 'date':
                if (jQuery("input[name="+no_mod[i]+"]").val())
                    jQuery("input[name="+no_mod[i]+"] ~ .inputDate").attr("disabled", "disabled").removeClass('inputDate');
                break;
            case 'textarea':
                if (jQuery("textarea[name="+no_mod[i]+"]").val())
                    jQuery("textarea[name="+no_mod[i]+"]").attr("disabled", "disabled");
        }
    }
});
