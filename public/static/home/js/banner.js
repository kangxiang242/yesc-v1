function DSHbanner(banner,time = 3000){
	var banIntervale;
	$(banner).find('.swiper-slide').eq(0).addClass('swiper-slide-active');
	var length = $(banner).find('.swiper-slide').length;
	for(var num = 0;num < length;num ++){
		$('.ban-pagination').append('<span class="swiper-pagination-bullet"></span>');
	}
	$(banner).find('.swiper-pagination-bullet').eq(0).addClass('swiper-pagination-bullet-active');
	var moveImgNum = 1;
	function moveimg(num){
		$(banner).find('.swiper-slide').removeClass('swiper-slide-active');
		$(banner).find('.swiper-slide').eq(num).addClass('swiper-slide-active');
		$(banner).find('.swiper-pagination-bullet').removeClass('swiper-pagination-bullet-active');
		$(banner).find('.swiper-pagination-bullet').eq(num).addClass('swiper-pagination-bullet-active');
		if(moveImgNum >= length - 1){
			moveImgNum = 0;
		}else{
			moveImgNum ++;
		}
	}
	$('.swiper-pagination-bullet').click(function(){
		clearInterval(banIntervale);
		moveImgNum = $(this).index();
		moveimg(moveImgNum);
		beginInterval();
	});
	function beginInterval(){
		banIntervale = setInterval(function(){
			moveimg(moveImgNum);
		}, time);
	}
	beginInterval();
}

function ShopSwiper(shop, prev, next){
	var width = $(shop).find('.swiper-slide').length * 227.5;
	var maxLeft = ($(shop).find('.swiper-slide').length - 4) * 227.5;
	$(shop).find('.swiper-wrapper').css('width', width+"px");
	$(prev).click(function(){
		var left = $(shop).find('.swiper-wrapper').position().left;
		if(left <= -227.5){
			$(shop).find('.swiper-wrapper').css('left', (left + 227.5) + 'px');
		}else{
			$(shop).find('.swiper-wrapper').css('left', '0px');
		}
	});
	$(next).click(function(){
		var left = $(shop).find('.swiper-wrapper').position().left;
		if(left >= -maxLeft){
			$(shop).find('.swiper-wrapper').css('left', (left - 227.5) + 'px');
		}else{
			$(shop).find('.swiper-wrapper').css('left', -maxLeft+'px');
		}
	});
}

function NewsSwiper(news, prev, next){
	var width = $(news).find('.swiper-slide').length * 306.667;
	var maxLeft = ($(news).find('.swiper-slide').length - 4) * 306.667;
	$(news).find('.swiper-wrapper').css('width', width+"px");
	$(prev).click(function(){
		var left = $(news).find('.swiper-wrapper').position().left;
		if(left <= -306.667){
			$(news).find('.swiper-wrapper').css('left', (left + 306.667) + 'px');
		}else{
			$(news).find('.swiper-wrapper').css('left', '0px');
		}
	});
	$(next).click(function(){
		var left = $(news).find('.swiper-wrapper').position().left;
		if(left >= -maxLeft){
			$(news).find('.swiper-wrapper').css('left', (left - 306.667) + 'px');
		}else{
			$(news).find('.swiper-wrapper').css('left', -maxLeft+'px');
		}
	});
}