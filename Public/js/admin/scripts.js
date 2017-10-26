//------------用于 提示内容不为空的 js--------------//

jQuery(document).ready(function() {
    $('.page-container form').submit(function(){
    	if($('.error').css("display") != 'none'){
    		return false;
    	}
    	//登录时 在验证一次
    	$(".username,.password,.code").each(function(){
    		stada(this);
    	})
    	
        var username = $(this).find('.username');
        var password = $(this).find('.password');
        var code	 = $(this).find('.code');
        if(username.val() == '') {
            $(this).find('.error').fadeOut('fast', function(){
                $(this).css('top', '27px');
            });
            $(this).find('.error').fadeIn('fast', function(){
                $(this).parent().find('.username').focus();
            });
            return false;
        }
        if(password.val() == '') {
            $(this).find('.error').fadeOut('fast', function(){
                $(this).css('top', '96px');
            });
            $(this).find('.error').fadeIn('fast', function(){
                $(this).parent().find('.password').focus();
            });
            return false;
        }
        if(code.val() == ''){
    	   $(this).find('.error').fadeOut('fast', function(){
               $(this).css('top', '167px');
           });
           $(this).find('.error').fadeIn('fast', function(){
               $(this).parent().find('.code').focus();
           });
           return false;
        }
        
    });

    $('.page-container form .username, .page-container form .password, .page-container form .code').keyup(function(){
       $(this).parent().find('.error').fadeOut('fast');
    });

});
