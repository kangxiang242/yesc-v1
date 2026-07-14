window.onload = function(){
	var str = '<div class="msg_div"><div class="fase_success fase_hide"></div><div class="fase_error"></div><p class="msg_text"></p><span class="msg_close"></span></div><div class="msg_ba"></div>';
	$('body').append(str);
	$('.msg_close').click(function(){
		$('.msg_div').animate({top:'-400px'},300);
		$('.msg_ba').hide();
	});
}
function show_msg(msg,num){
	if(num == 1){
		$('.fase_error').addClass('fase_hide');
		$('.fase_success').removeClass('fase_hide');
	}else{
		$('.fase_success').addClass('fase_hide');
		$('.fase_error').removeClass('fase_hide');
	}
	$('.msg_text').html(msg);
	$('.msg_ba').show();
	$('.msg_div').animate({top:'50%'},500);
}