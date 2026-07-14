

jQuery(document).ready(function(){



	function GetQueryString(name)
	{
		var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
		var r = window.location.search.substr(1).match(reg);
		if(r!=null)return  unescape(r[2]); return null;
	}
	//返回上一页
	$('.back').click(function(){
		var go_back_url = document.referrer;
		var domain = go_back_url.split('/');
		if( domain[2] ) {
			domain = domain[2];
			domain = domain.split(':')
			if(domain[0]){
				domain = domain[0];
				if(domain == document.domain){
					window.history.back(-1);
				}else{
					location.href='/';
				}
			}
		}
	})

	var pathname = window.location.pathname;
	pathname = pathname.replace('index.php','');
	var eq = null;
	if(pathname == '/'){
		eq = 0;
	}else if(pathname == '/product'){
		eq = 1;
	}else if(pathname == '/effect'){
		eq = 2;
	}else if(pathname == '/health'){
		eq = 3;
	}else if(pathname == '/message'){
		eq = 4;
	}
	if(eq != null && eq != undefined){
		$('.menu-item').eq(eq).addClass('activat');
		var data_img = $('.menu-item').eq(eq).find('img').attr('data-activate-img');
		$('.menu-item').eq(eq).find('img').attr('src',data_img);
	}

})