(function($){
    $(function() {
        $('input.member_join_agree').click(function(){
            if (!$('input[name=invitation_code]').val()) {
                alert(msg_empty_invitation_code);
                $('input[name=invitation_code]').focus();
                return;
            }
                    
            var param = {
                invitation_code: $('input[name=invitation_code]').val()
            }
            exec_xml('join_extend','procJoin_extendInvitation',param, function(){ location.reload()});
        });
    });
})(jQuery);
