function onsub(){
	$('.form-item').find('span').css('color','#000')
	$('.form-item').find('input').css('border','1px solid #a0a0a0');
	var sub_ok = 1;
	var name = $('input[name="name"]').val();
	var email = $('input[name="email"]').val();
	var phone = $('input[name="phone"]').val();
	var choose_city = $("#city").val();
	var choose_county = $("#county").val();
	var choose_street = $("#street").val();
	var input_area = $('input[name="city_more"]').val();
	var ibon_si_choose = $("#ibon_si").val();
	var ibon_qu_choose = $("#ibon_qu").val();
	var ibon_area_choose = $('#ibon_area').val();
	var qj_si_choose = $("#qj_si").val();
	var qj_qu_choose = $("#qj_qu").val();
	var qj_area_choose = $('#qj_area').val();
	if(name == ""){
		$('input[name="name"]').siblings().css('color',"red");
		$('input[name="name"]').css('border',"1px solid #f00");
		sub_ok = 0;
	}else{
		var pattern = new RegExp("[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）—|{}【】‘；：”“'。，、？]")
		var rs = "";
		for (var i = 0; i < name.length; i++) {
			rs = rs+name.substr(i, 1).replace(pattern,'');
		}
		$('input[name="name"]').val(rs);
		if(rs.length < name.length){
			$('input[name="name"]').css('border',"1px solid #f00");
			sub_ok = 0;
		}
	}
	if(email == ""){
		$('input[name="email"]').siblings().css('color',"red");
		$('input[name="email"]').css('border',"1px solid #f00");
		sub_ok = 0;
	}else{
		if(email.search(/^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+\.(?:com|cn|tw|net|info)$/)!= -1){

		}else{
			sub_ok = 0;
			$('input[name="email"]').siblings().css('color',"red");
			$('input[name="email"]').css('border',"1px solid #f00");
		}
	}
	if(phone == ""){
		$('input[name="phone"]').siblings().css('color',"red");
		$('input[name="phone"]').css('border',"1px solid #f00");
		sub_ok = 0;
	}else{
		if(!(/^09\d{8}$/.test(phone))){
			$('input[name="phone"]').siblings().css('color',"red");
			$('input[name="phone"]').css('border',"1px solid #f00");
			sub_ok = 0;
		}
	}
	if(!choose_city){
		$("#city").css('border','1px solid #f00');
		$('#region-span').css('color','#f00');
		sub_ok = 0;
	}
	if(!choose_county){
		$("#county").css('border','1px solid #f00');
		$('#region-span').css('color','#f00');
		sub_ok = 0;
	}
	if(!choose_street){
		$("#street").css('border','1px solid #f00');
		$('#region-span').css('color','#f00');
		sub_ok = 0;
	}
	if($('input[name="order_type"]:checked').val() > 0){
		if(!$('input[name="store_id"]:checked').val()){
			sub_ok = 0;
			$('#select-store-span').css('color','#f00');
			$('#store-messages').text("請選擇門市");
			$('#store-messages').slideDown(500);
			$('#store-messages').css('color','#f00');

		}

	}else{
		if(!$('input[name="city_more"]').val()){

			$('input[name="city_more"]').css('border','1px solid #f00');
			$('input[name="city_more"]').prev('span').css('color','#f00');
			sub_ok = 0;
		}

	}
	if(sub_ok == 1){
		return true;
	}else{
		return false;
	}
}