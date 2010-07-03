jQuery(function($) {
    for (i = 0; i < no_mod.length; i++) {
        switch (no_mod_type[i]) {
            case 'text': case 'homepage': case 'email_address': case 'tel':
                if (jQuery("input[name="+no_mod[i]+"]").val())
                    jQuery("input[name="+no_mod[i]+"]").attr("readonly", "readonly");
                break;
            case 'radio': case 'checkbox':
                if (jQuery("input[name="+no_mod[i]+"]:checked").length) {
                    jQuery("input[name="+no_mod[i]+"]").attr("disabled", "disabled")
                    jQuery("input[name="+no_mod[i]+"]:last").after('<input type="hidden" name="'+no_mod[i]+'" value="dummy" />');
                }
                break;
            case 'select':
                if (jQuery("select[name="+no_mod[i]+"] > option[selected=selected]").length)
                    jQuery("select[name="+no_mod[i]+"]").attr("disabled", "disabled").after('<input type="hidden" name="'+no_mod[i]+'" value="dummy" />');
                break;
            case 'kr_zip':
                if (jQuery("input[name="+no_mod[i]+"]").val()) {
                    jQuery("input[name="+no_mod[i]+"]").attr("readonly", "readonly");
                    jQuery("input[name="+no_mod[i]+"] ~ a").css('display', 'none');
                }
                break;
            case 'date':
                if (jQuery("input[name="+no_mod[i]+"]").val())
                    jQuery("input[name="+no_mod[i]+"] ~ .inputDate").attr("readonly", "readonly").removeClass('inputDate');
                jQuery("input[name="+no_mod[i]+"]").nextAll("span").remove();
                break;
            case 'textarea':
                if (jQuery("textarea[name="+no_mod[i]+"]").val())
                    jQuery("textarea[name="+no_mod[i]+"]").attr("readonly", "readonly");
        }
    }
});
