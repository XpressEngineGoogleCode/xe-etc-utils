(function($){
    $(function() {
        // agree
        $('input.member_join_agree').click(function(){
            if($('.member_join_extend :checkbox').length != $('.member_join_extend :checked').length){
                alert(msg_check_agree);
                return;
            }
            
            if (use_jumin == "Y") {
                if (!$('input[name=name]').val()) {
                    alert(msg_empty_name);
                    $('input[name=name]').focus();
                    return;
                }
                if ($('input[name=name]').val().length <2 || $('input[name=name]').val().length > 20) {
                    alert(about_user_name);
                    $('input[name=name]').focus();
                    return;
                }
                if (!$('input[name=jumin1]').val()) {
                    alert(msg_empty_jumin1);
                    $('input[name=jumin1]').focus();
                    return;
                }
                if (!$('input[name=jumin2]').val()) {
                    alert(msg_empty_jumin2);
                    $('input[name=jumin2]').focus();
                    return;
                }
            }

            var param = {
                        name: $('input[name=name]').val(),
                        jumin1: $('input[name=jumin1]').val(),
                        jumin2: $('input[name=jumin2]').val()
                        }
            exec_xml('join_extend','procJoin_extendAgree',param, function(){ location.reload()});
        });

        // junior
        $('input.junior_join').click(function(){ if(msg_junior_join) alert(msg_junior_join); });
    });
})(jQuery);
