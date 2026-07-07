@extends('mobile.layout')

@section('robots', 'noindex,nofollow')

@section('track-init')
<script>Track.init({ platform: 'mobile', page_type: 'checkout', goods_id: {{ $goods->id }} });</script>
@endsection

@php
    $freight_where = \App\Services\ConfigService::get('freight_where',0);
    $freight_price = \App\Services\ConfigService::get('freight',0);

    $delivery_type_all = \App\Services\ConfigService::get('delivery_type',[]);
    if($delivery_type_all){
        $delivery_type_all = json_decode(\App\Services\ConfigService::get('delivery_type',[]),true);
    }
@endphp
@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ release_asset('/static/mobile/css/shopping.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ release_asset('/static/mobile/less/checkout.css') }}"/>
    <style>
        footer{
            display: none;
        }
        .right-href, .nav-buy-product {
            display: none;
        }
        .nav .nav-back {
            margin: 0 auto;
        }
        .ysj{
            margin-right: 0.3rem;
            display: flex;
            align-items: center;
        }
        input[type=radio]:checked + label{
            border-color: #1C6AB4;
        }
        .select-city-box{
            position: relative;
        }
        .select-load{
            right: 0.9rem!important;
            left: unset!important;
        }
        .nav{
            display: none;
        }
        .header-seize_seat{
            height: 1rem;
        }
    </style>
@stop

@section('header-class','other-header')


@section('title','快速結賬-'.$goods->name)

@section('script')
    @parent
    <script src="{{ asset('static/js/jquery.contip.js') }}"></script>
    <script src="{{ asset('static/js/sweetalert2.js') }}"></script>
    <script src="{{ release_asset('static/js/api.js')}}"></script>
    <script src="{{ release_asset('static/js/xarea.js')}}"></script>
    <script id="CHECKOUT-M-1">
        var freight_where = parseInt('{{ $freight_where }}');
        var freight_price = parseInt('{{ $freight_price }}');
        if (typeof FingerprintJS !== 'undefined') {
            FingerprintJS.load().then(function (fp) { return fp.get(); }).then(function (result) {
                if (result.visitorId) {
                    $('input[name="fingerprint_token"]').val(result.visitorId);
                }
            });
        }
        $("tupni-mrof.".split("").reverse().join(""))["\u0066\u006f\u0063\u0075\u0073"](function(){if(!$(this)["\u0068\u0061\u0073\u0043\u006c\u0061\u0073\u0073"](focus)){$(this)["\u0061\u0064\u0064\u0043\u006c\u0061\u0073\u0073"]("sucof".split("").reverse().join(""));}});$("tupni-mrof.".split("").reverse().join(""))["\u0062\u006c\u0075\u0072"](function(){if(!$(this)["\u0076\u0061\u006c"]()){$(this)["\u0072\u0065\u006d\u006f\u0076\u0065\u0043\u006c\u0061\u0073\u0073"]("sucof".split("").reverse().join(""));}});$("lebal.".split("").reverse().join(""))['click'](function(){$(this)['prev']()["\u0066\u006f\u0063\u0075\u0073"]();});$("]\"epyt_redro\"=eman[tupni".split("").reverse().join(""))["\u0063\u006c\u0069\u0063\u006b"](function(){if($(this)['val']()>(0x204f0^0x204f0)){$("epyt-redro-ler#".split("").reverse().join(""))["\u0074\u0065\u0078\u0074"]('取貨付款');}else{$("epyt-redro-ler#".split("").reverse().join(""))["\u0074\u0065\u0078\u0074"]("\u6B3E\u4ED8\u5230\u8CA8".split("").reverse().join(""));}});var id,name,price,img,original_price;$('body')['on']("\u0063\u006c\u0069\u0063\u006b","a uks-sdoog.".split("").reverse().join(""),function(){id=$(this)["\u0061\u0074\u0074\u0072"]("di-atad".split("").reverse().join(""));name=$(this)['attr']("\u0064\u0061\u0074\u0061\u002d\u006e\u0061\u006d\u0065");price=$(this)["\u0061\u0074\u0074\u0072"]("\u0064\u0061\u0074\u0061\u002d\u0070\u0072\u0069\u0063\u0065");img=$(this)['attr']('data-img');original_price=$(this)["\u0061\u0074\u0074\u0072"]("\u0064\u0061\u0074\u0061\u002d\u006f\u0072\u0069\u0067\u0069\u006e\u0061\u006c");$('.goods-template-goods-img\x20.goods-img')["\u0061\u0074\u0074\u0072"]('src',img);$("\u002e\u0067\u006f\u006f\u0064\u0073\u002d\u0074\u0065\u006d\u0070\u006c\u0061\u0074\u0065\u002d\u0074\u0069\u0074\u006c\u0065")['text'](name);$('.goods-template-red-price')["\u0074\u0065\u0078\u0074"]('優惠價：NT$\x20'+toThousands(price));$(this)['addClass']('article')["\u0070\u0061\u0072\u0065\u006e\u0074"]()["\u0073\u0069\u0062\u006c\u0069\u006e\u0067\u0073"]()["\u0066\u0069\u006e\u0064"]('a')['removeClass']("elcitra".split("").reverse().join(""));});$("\u002e\u0067\u006f\u006f\u0064\u0073\u002d\u0061\u0066\u0066\u0069\u0072\u006d")["\u0063\u006c\u0069\u0063\u006b"](function(){$('.goods-img')['attr']('src',img);$('.goods-title')["\u0074\u0065\u0078\u0074"](name);$('#goods-price')['text'](toThousands(original_price));$("\u0023\u0064\u0069\u0073\u0063\u006f\u0075\u006e\u0074\u002d\u0070\u0072\u0069\u0063\u0065")["\u0074\u0065\u0078\u0074"](toThousands(original_price-price));var _0x4550a9=0xe0358^0xe0358;if(price>=freight_where){$('#freight-price')['text']('NT$\x200');}else{_0x4550a9=freight_price;$("\u0023\u0066\u0072\u0065\u0069\u0067\u0068\u0074\u002d\u0070\u0072\u0069\u0063\u0065")["\u0074\u0065\u0078\u0074"](" $TN".split("").reverse().join("")+freight_price);}$("ecirp-redro-toof#,ecirp-redro#".split("").reverse().join(""))["\u0074\u0065\u0078\u0074"](toThousands(parseInt(price)+parseInt(_0x4550a9)));$("niam-uks.".split("").reverse().join(""))['removeClass']("wohsni".split("").reverse().join(""));$("]\"di_sdoog\"=eman[tupni".split("").reverse().join(""))["\u0076\u0061\u006c"](id);});function toThousands(_0x52fa97){var _0x20d95e=[],_0x41ab9b=0xb96e4^0xb96e4;_0x52fa97=(_0x52fa97||0xdc5a3^0xdc5a3)["\u0074\u006f\u0053\u0074\u0072\u0069\u006e\u0067"]()['split']("");for(var _0x5d952b=_0x52fa97["\u006c\u0065\u006e\u0067\u0074\u0068"]-(0xc5b21^0xc5b20);_0x5d952b>=(0xb22ab^0xb22ab);_0x5d952b--){_0x41ab9b++;_0x20d95e['unshift'](_0x52fa97[_0x5d952b]);if(!(_0x41ab9b%(0x2aec1^0x2aec2))&&_0x5d952b!=(0xe0ab5^0xe0ab5)){_0x20d95e["\u0075\u006e\u0073\u0068\u0069\u0066\u0074"](',');}}return _0x20d95e['join']('');}$(document)['ready'](function(){setTimeout(function(){$("txet-pmats.".split("").reverse().join(""))['addClass']("\u0073\u0074\u0061\u006d\u0070\u002d\u0074\u0065\u0078\u0074\u002d\u0073\u0068\u006f\u0077");},0x38465^0x3878d);$("\u002e\u0066\u006f\u0072\u006d\u002d\u0063\u006f\u006e\u0074\u0072\u006f\u006c")['blur'](function(){if($(this)['val']()){$(this)['addClass']("\u0066\u006f\u0063\u0075\u0073");$(this)['siblings']("gvs".split("").reverse().join(""))["\u0061\u0064\u0064\u0043\u006c\u0061\u0073\u0073"]("\u0076\u0061\u006c\u0069\u0064");}else{$(this)['removeClass']("\u0066\u006f\u0063\u0075\u0073");$(this)["\u0073\u0069\u0062\u006c\u0069\u006e\u0067\u0073"]('svg')["\u0072\u0065\u006d\u006f\u0076\u0065\u0043\u006c\u0061\u0073\u0073"]('valid');}});});var submitValidate=function(){var _0x3817b7=$("]'eman'=eman[tupni".split("").reverse().join(""))["\u0076\u0061\u006c"]();var _0x18beb6=$("]'enohp'=eman[tupni".split("").reverse().join(""))["\u0076\u0061\u006c"]();var _0x5d1064=$('input[name=\x27email\x27]')["\u0076\u0061\u006c"]();var _0xb94c54=$('select[name=\x27city\x27]')['val']();var _0x14f1ed=$('select[name=\x27county\x27]')['val']();var _0xd3ee89=$('select[name=\x27street\x27]')['val']();var _0x186d53=$('input[name=\x27address\x27]')["\u0076\u0061\u006c"]();var _0x383c66=$("dekcehc:]'epyt_redro'=eman[tupni".split("").reverse().join(""))["\u0076\u0061\u006c"]();var _0x145031=$('input[name=\x27store_id\x27]:checked')['val']();if(!_0x3817b7){error_message("\u540D\u59D3\u5165\u8F38\u8ACB".split("").reverse().join(""));return![];}if(!_0x18beb6){error_message('請輸入電話');return![];}_0x18beb6=_0x18beb6["\u0072\u0065\u0070\u006c\u0061\u0063\u0065\u0041\u006c\u006c"]('-',"");if(!/^09\d{8}$/["\u0074\u0065\u0073\u0074"](_0x18beb6)){error_message("\u8AA4\u932F\u5F0F\u683C\u8A71\u96FB".split("").reverse().join(""));return![];}if(!_0x5d1064){error_message('請輸入郵箱');return![];}if(!validateEmail(_0x5d1064)){error_message("\u90f5\u7bb1\u683c\u5f0f\u932f\u8aa4");return![];}if(!_0xb94c54){error_message('請選擇縣市');return![];}if(!_0x14f1ed){error_message('請選擇地區');return![];}if(!_0xd3ee89){error_message("\u6BB5\u8DEF\u64C7\u9078\u8ACB".split("").reverse().join(""));return![];}if(_0x383c66>(0xc0715^0xc0715)&&!_0x145031){error_message("\u5E02\u9580\u64C7\u9078\u8ACB".split("").reverse().join(""));return![];}else if(_0x383c66<=(0xe3354^0xe3354)&&!_0x186d53){error_message('請填寫宅配地址');return![];}addLoadingActionBtn('.form-btn');return!![];};function error_message(_0x57076f){Swal['fire']({"\u0069\u0063\u006f\u006e":"\u0065\u0072\u0072\u006f\u0072",'iconColor':'#fff',"\u0074\u0065\u0078\u0074":_0x57076f,'color':'#fff','background':'rgba(0,0,0,0.7)',"\u0077\u0069\u0064\u0074\u0068":'auto','backdrop':![],'timer':0x3e8,"\u0074\u0069\u006d\u0065\u0072\u0050\u0072\u006f\u0067\u0072\u0065\u0073\u0073\u0042\u0061\u0072":![],'showConfirmButton':![]});}var time_zone=Intl['DateTimeFormat']()['resolvedOptions']()['timeZone'];$('input[name=\x22timezone\x22]')['val'](time_zone);$('input[name=\x22phone\x22]')['on']("tupni".split("").reverse().join(""),function(){var _0x314003=$(this)["\u0076\u0061\u006c"]();_0x314003=_0x314003['replaceAll']('-',"");_0x314003=_0x314003['replaceAll']('\x20',"".split("").reverse().join(""));_0x314003=_0x314003["\u0072\u0065\u0070\u006c\u0061\u0063\u0065"](/[^\d]/g,'');var _0x48e47f=_0x314003["\u0073\u0070\u006c\u0069\u0074"]('');if(_0x48e47f['length']>0x4){_0x314003=_0x314003['slice'](0xeb917^0xeb917,0x4)+'-'+_0x314003['slice'](0x4);}if(_0x48e47f['length']>(0x77337^0x77330)){_0x314003=_0x314003['slice'](0x0,0x1c648^0x1c640)+'-'+_0x314003['slice'](0x8);}$(this)['val'](_0x314003['slice'](0x9bd84^0x9bd84,0xc));});
    </script>

    <script>
        // 切换盒数：同步弹层标题、图片、原价、优惠价
        $(document).on('click', '.goods-sku a', function () {
            var $link = $(this);
            id = $link.attr('data-id');
            name = $link.attr('data-name');
            price = $link.attr('data-price');
            img = $link.attr('data-img');
            original_price = $link.attr('data-original');

            $('.goods-template-goods-img .goods-img').attr('src', img);
            $('.goods-template-title').text(name);
            $('.goods-template-grey-price').html('原價：<span class="twd">NT$</span>' + toThousands(original_price));
            $('.goods-template-red-price').html('優惠價：<span class="twd">NT$</span>' + toThousands(price));

            $link.addClass('article').parent().siblings().find('a').removeClass('article');
        });

        // 配送方式标题
        function updateOrderTypeTitle() {
            var orderType = parseInt($('input[name="order_type"]:checked').val(), 10);
            $('#order-type-title').text(orderType > 0 ? '配送至門店' : '宅配地址');
        }
        $('input[name="order_type"]').on('change', updateOrderTypeTitle);
        updateOrderTypeTitle();

        // 表单浮动标签
        $(document).on('focus', '.form-control', function () {
            if (!$(this).hasClass('focus')) {
                $(this).addClass('focus');
            }
        });
        $(document).on('click', 'label.shut', function () {
            $(this).prev('.form-control').focus();
        });

        // 覆盖 submitValidate，修正 reverse 错误文案
        submitValidate = function () {
            var name = $('input[name="name"]').val();
            var phone = $('input[name="phone"]').val();
            var email = $('input[name="email"]').val();
            var city = $('select[name="city"]').val();
            var county = $('select[name="county"]').val();
            var street = $('select[name="street"]').val();
            var address = $('input[name="address"]').val();
            var orderType = parseInt($('input[name="order_type"]:checked').val(), 10);
            var storeId = $('input[name="store_id"]:checked').val();

            if (!name) {
                error_message('請輸入姓名');
                return false;
            }
            if (!phone) {
                error_message('請輸入電話');
                return false;
            }
            phone = phone.replaceAll('-', '').replace(/\s/g, '');
            if (!/^09\d{8}$/.test(phone)) {
                error_message('電話格式錯誤');
                return false;
            }
            if (!email) {
                error_message('請輸入郵箱');
                return false;
            }
            if (!validateEmail(email)) {
                error_message('郵箱格式錯誤');
                return false;
            }
            if (!city || city === '0') {
                error_message('請選擇縣市');
                return false;
            }
            if (!county || county === '0') {
                error_message('請選擇地區');
                return false;
            }
            if (!street || street === '0') {
                error_message('請選擇路段');
                return false;
            }
            if (orderType > 0 && !storeId) {
                error_message('請選擇門市');
                return false;
            }
            if (orderType <= 0 && !address) {
                error_message('請填寫宅配地址');
                return false;
            }
            addLoadingActionBtn('.form-btn');
            return true;
        };

        // SKU 确定：同步优惠行与运费结构
        $('.goods-affirm').off('click').on('click', function () {
            if (typeof id === 'undefined' || !id) {
                return;
            }
            $('.goods-img').attr('src', img);
            $('.goods-title').text(name);
            $('#goods-price').text(toThousands(original_price));
            var discount = parseFloat(original_price) - parseFloat(price);
            $('#discount-price').text(toThousands(discount));
            $('#discount-price').closest('dl').css('display', discount > 0 ? 'flex' : 'none');

            var freightAdd = 0;
            if (parseFloat(price) >= freight_where) {
                $('#freight-price').html('<span class="twd">NT$</span><span>0</span>');
            } else {
                freightAdd = freight_price;
                $('#freight-price').html('<span class="twd">NT$</span>' + freight_price);
            }
            $('#order-price, #foot-order-price').text(toThousands(parseInt(price, 10) + freightAdd));
            $('.sku-main').removeClass('inshow');
            $('input[name="goods_id"]').val(id);
        });

        // 便利店选择 → 同步 shop_name 和 address 到表单
        $(document).on('change', 'input[name="store_id"]', function(){
            var shopName = $(this).data('shop-name') || '';
            var storeAddr = $(this).data('address') || '';
            $('input[name="shop_name"]').val(shopName);
            $('input[name="address"]').val(storeAddr);
        });
        // 页面加载后如果已有选中门店，同步一次
        $(document).on('ajaxComplete', function(){
            var checked = $('input[name="store_id"]:checked');
            if(checked.length){
                var shopName = checked.data('shop-name') || '';
                var storeAddr = checked.data('address') || '';
                $('input[name="shop_name"]').val(shopName);
                $('input[name="address"]').val(storeAddr);
            }
        });
    </script>

    {{-- 禁止浏览器自动填充 --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.querySelector('form');
            if (!form) return;
            form.setAttribute('autocomplete', 'off');
            form.querySelectorAll('input').forEach(function (input) {
                if (!input.value) {
                    input.setAttribute('readonly', 'readonly');
                    input.addEventListener('focus', function () {
                        this.removeAttribute('readonly');
                    }, { once: true });
                    input.addEventListener('click', function () {
                        this.removeAttribute('readonly');
                    }, { once: true });
                }
            });
        });
    </script>

@stop

@section('footer-menu')

    <div class="row footer-menu">
        <div class="shop-price">
            <p class="goods-title">{{ $goods->name }}</p>
            <p class="red-price"><span style="font-size: 0.22rem; font-weight: 700; margin-right: 0.1rem;">訂單總額：NT$</span><span id="foot-order-price">{{ number_format(round($goods->price>=$freight_where?$goods->price:$goods->price+$freight_price)) }}</span></p>
            <!-- <p class="green-title">{{ $goods->price>=$freight_where?"免運費":"含運費NT$ ".$freight_price }}</p> -->
        </div>
        <div class="shop-buy" data-track-block="m_co_submit"><button class="form-btn" type="button" data-track="m_checkout_submit" data-track-zone="content" onclick="$('#order-form').submit();"><svg t="1718871599412" class="checkouticon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="13167" width="200" height="200"><path d="M514.062027 1018.052384h-12.821041c-57.694685-18.936986-230.764712-113.621918-307.690959-195.682192-76.912219-88.372603-115.375342-170.432877-115.375342-265.117808v-378.739726c0-12.624658 6.410521-25.249315 19.217534-31.561644l403.848767-138.871233h19.217535l397.424219 138.871233c12.821041 6.312329 19.231562 18.936986 19.231561 31.561644v366.115068c0 100.99726-32.038575 183.057534-115.375342 265.117808-70.515726 94.684932-243.585753 189.369863-294.855891 208.30685h-12.821041z m161.52548-586.906302h-35.629589v-80.938082c0-67.766356-56.348055-122.82389-125.713534-122.82389-69.295342 0-125.727562 55.127671-125.727562 122.837917v80.938083h-35.629589c-17.099397 0-31.014575 13.606575-31.014575 30.299178v221.548712c0 16.776767 13.915178 30.383342 31.014575 30.383342h322.700274c17.169534 0 31.028603-13.606575 31.028603-30.383342V461.459288c0.070137-16.692603-13.859068-30.299178-31.028603-30.299178zM531.736548 578.026959v48.352438a4.067945 4.067945 0 0 1-4.081973 3.983781h-26.694137a4.067945 4.067945 0 0 1-4.067945-3.983781v-48.352438c-12.568548-6.256219-21.251507-18.852822-21.251507-33.52548 0-20.830685 17.295781-37.789808 38.673535-37.789808 21.363726 0 38.659507 16.959123 38.659506 37.775781 0 14.686685-8.668932 27.283288-21.237479 33.539507z m60.03726-146.866849H436.841205V352.227945c0-41.773589 34.787945-75.747945 77.529425-75.747945 42.755507 0 77.473315 33.974356 77.473315 75.747945l-0.070137 78.90411z" p-id="13168"></path></svg>提交訂單</button></div>
    </div>
    <div id="cover"></div>
@stop

@section('content')
    
    <div class="row">
        <div class="checkout-container">
            <form method="POST" action="{{ url('order') }}" id="order-form" onsubmit="return orderStore()">
                {{ csrf_field() }}
                <input type="hidden" value="{{ $goods->id }}" name="goods_id">
                <input type="hidden" value="" name="timezone">
                <input type="hidden" value="" name="fingerprint_token">
                <div class="checkout-wrapper" data-track-block="m_co_form">


                    <div class="main">
                        <div class="others-sec">
                            <div class="secret">
                                <div class="packageleft">
                                    <svg version="1.1" class="packageicon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                        viewBox="0 0 400 400" enable-background="new 0 0 400 400" xml:space="preserve">
                                        <g style="transform: translate(-380px,-560px);">
                                            <polygon fill="#CFB594" points="593.681,654.906 690.7,701.207 565.869,738.273 467.468,688.141 	"/>
                                            <polygon fill="#BCA286" points="690.126,833.889 565.641,878.104 565.641,737.836 690.7,701.207 	"/>
                                            <polygon fill="#F0DCC0" points="467.468,688.141 467.731,818.383 565.641,878.104 565.641,737.836 	"/>
                                            <polygon fill="#E0D3C2" points="503.856,706.543 631.332,672.873 644.787,679.277 518.163,713.838 	"/>
                                            <polygon fill="#E0D3C2" points="522.395,673.727 619.738,721.99 636.41,717.201 537.614,669.707 	"/>
                                            <polygon fill="#DAC4AE" points="619.738,721.99 636.41,717.201 636.41,755.25 620.336,762.26 	"/>
                                            <polygon fill="#F7E8D5" points="503.856,706.543 518.163,713.838 518.163,754.994 503.799,746.102 	"/>
                                        </g>
                                    </svg>
                                    <p class="stamp-text">隱密發貨</p>
                                </div>
                                <div class="packageright">
                                    <div class="packagetext">
                                        <svg t="1718693930066" class="righticon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="13167" width="200" height="200"><path  d="M512.1 512.4m-448 0a448 448 0 1 0 896 0 448 448 0 1 0-896 0Z" fill="#1C6AB4" p-id="13168"></path><path fill="#FFF" d="M445.9 723.9c-13.3 0-26-5.3-35.4-14.6L278.3 577c-19.5-19.5-19.5-51.2 0-70.7s51.2-19.5 70.7 0l96.9 96.9L675 374.1c19.5-19.5 51.2-19.5 70.7 0s19.5 51.2 0 70.7L481.2 709.3c-9.3 9.3-22 14.6-35.3 14.6z" p-id="13169"></path></svg>
                                        <p>包裹外觀<strong>絕不會提及</strong>與威而鋼相關之任何字樣</p>
                                    </div>
                                    <div class="packagetext">
                                        <p><svg t="1718693930066" class="righticon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="13167" width="200" height="200"><path  d="M512.1 512.4m-448 0a448 448 0 1 0 896 0 448 448 0 1 0-896 0Z" fill="#1C6AB4" p-id="13168"></path><path fill="#FFF" d="M445.9 723.9c-13.3 0-26-5.3-35.4-14.6L278.3 577c-19.5-19.5-19.5-51.2 0-70.7s51.2-19.5 70.7 0l96.9 96.9L675 374.1c19.5-19.5 51.2-19.5 70.7 0s19.5 51.2 0 70.7L481.2 709.3c-9.3 9.3-22 14.6-35.3 14.6z" p-id="13169"></path></svg>
                                        <p>包裹送達後<strong>僅透過簡訊</strong>通知取貨，簡訊不會提及威而鋼相關之任何內容，<strong>絕不會致電</strong>打擾</p>
                                    </div>
                                    <div class="packagetext">
                                        <p><svg t="1718693930066" class="righticon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="13167" width="200" height="200"><path  d="M512.1 512.4m-448 0a448 448 0 1 0 896 0 448 448 0 1 0-896 0Z" fill="#1C6AB4" p-id="13168"></path><path fill="#FFF" d="M445.9 723.9c-13.3 0-26-5.3-35.4-14.6L278.3 577c-19.5-19.5-19.5-51.2 0-70.7s51.2-19.5 70.7 0l96.9 96.9L675 374.1c19.5-19.5 51.2-19.5 70.7 0s19.5 51.2 0 70.7L481.2 709.3c-9.3 9.3-22 14.6-35.3 14.6z" p-id="13169"></path></svg>
                                        <p>宅配人員及超商店員<strong>絕不會知道</strong>您所訂購的商品，請安心選購</p>
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                        <div class="card">
                            <p class="title">訂購內容：</p>
                            <div class="product-main" data-track-block="m_co_product">

                                <div class="goods">
                                    <div class="img-wrap">
                                        <img class="goods-img" src="{{ asset('uploads/'.$goods->img) }}" alt="{{ $goods->name }}">
                                    </div>
                                    <div class="info">
                                        <p class="goods-title">{{ $goods->name }}</p>
                                        
                                        <p class="sub-title">【生產】美國輝瑞製藥有限公司</p>
                                        <p class="sub-title">【有效期】60個月</p>
                                        <button class="goods-suit" type="button" data-track="m_checkout_sku_open" data-track-zone="content" onclick="$('.sku-main').addClass('inshow')">更換盒數</button>
                                    </div>
                                </div>

                                <div class="census">
                                    <div class="compute">
                                        <dl>
                                            <dt>
                                                <p class="p-title">商品原價</p>
                                            </dt>
                                            <dd>
                                                <span class="twd">NT$</span><span id="goods-price">{{ number_format(round($goods->market_price)) }}</span>
                                            </dd>
                                        </dl>

                                        <dl style="display: {{ $goods->market_price-$goods->price>0?"flex":'none' }}">
                                            <dt>
                                                <p class="p-title">官網優惠</p>
                                            </dt>
                                            <dd>
                                                <span class="twd">已為您優惠 NT$</span><span id="discount-price">{{ number_format(round($goods->market_price-$goods->price)) }}</span>
                                            </dd>
                                        </dl>

                                        <dl>
                                            <dt>
                                                <p class="p-title">運費<span class="grep">（訂購4盒以上可享受官方免費配送服務）</span></p>
                                            </dt>
                                            <dd>
                                                <span id="freight-price">
                                                    @if($goods->price<$freight_where)
                                                        <span class="twd">NT$</span>{{ round($freight_price) }}
                                                    @else
                                                        <span class="twd">NT$</span><span>0</span>
                                                    @endif
                                                </span>
                                            </dd>
                                        </dl>
                                        <dl>
                                            <dt>
                                                <p class="p-title" style="font-weight: 500;color: #000;">訂單總額</p>
                                            </dt>
                                            <dd style="color: #E63434;">
                                                <span class="twd">NT$</span><span id="order-price">@if($goods->price<$freight_where){{ number_format(round($goods->price+$freight_price)) }}@else{{ number_format(round($goods->price)) }}@endif
                                                </span>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card">
                            <p class="title">配送訊息：</p>
                            <div class="mater">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="name" id="name">
                                    <label class="shut" for="name">請問如何稱呼您</label>
                                    <svg t="1718871599412" class="safeicon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="13167" width="200" height="200"><path d="M514.062027 1018.052384h-12.821041c-57.694685-18.936986-230.764712-113.621918-307.690959-195.682192-76.912219-88.372603-115.375342-170.432877-115.375342-265.117808v-378.739726c0-12.624658 6.410521-25.249315 19.217534-31.561644l403.848767-138.871233h19.217535l397.424219 138.871233c12.821041 6.312329 19.231562 18.936986 19.231561 31.561644v366.115068c0 100.99726-32.038575 183.057534-115.375342 265.117808-70.515726 94.684932-243.585753 189.369863-294.855891 208.30685h-12.821041z m161.52548-586.906302h-35.629589v-80.938082c0-67.766356-56.348055-122.82389-125.713534-122.82389-69.295342 0-125.727562 55.127671-125.727562 122.837917v80.938083h-35.629589c-17.099397 0-31.014575 13.606575-31.014575 30.299178v221.548712c0 16.776767 13.915178 30.383342 31.014575 30.383342h322.700274c17.169534 0 31.028603-13.606575 31.028603-30.383342V461.459288c0.070137-16.692603-13.859068-30.299178-31.028603-30.299178zM531.736548 578.026959v48.352438a4.067945 4.067945 0 0 1-4.081973 3.983781h-26.694137a4.067945 4.067945 0 0 1-4.067945-3.983781v-48.352438c-12.568548-6.256219-21.251507-18.852822-21.251507-33.52548 0-20.830685 17.295781-37.789808 38.673535-37.789808 21.363726 0 38.659507 16.959123 38.659506 37.775781 0 14.686685-8.668932 27.283288-21.237479 33.539507z m60.03726-146.866849H436.841205V352.227945c0-41.773589 34.787945-75.747945 77.529425-75.747945 42.755507 0 77.473315 33.974356 77.473315 75.747945l-0.070137 78.90411z" p-id="13168"></path></svg>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="tel" name="phone" id="phone">
                                    <label class="shut" for="phone">請留下您收貨時使用的電話號碼</label>
                                    <svg t="1718871599412" class="safeicon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="13167" width="200" height="200"><path d="M514.062027 1018.052384h-12.821041c-57.694685-18.936986-230.764712-113.621918-307.690959-195.682192-76.912219-88.372603-115.375342-170.432877-115.375342-265.117808v-378.739726c0-12.624658 6.410521-25.249315 19.217534-31.561644l403.848767-138.871233h19.217535l397.424219 138.871233c12.821041 6.312329 19.231562 18.936986 19.231561 31.561644v366.115068c0 100.99726-32.038575 183.057534-115.375342 265.117808-70.515726 94.684932-243.585753 189.369863-294.855891 208.30685h-12.821041z m161.52548-586.906302h-35.629589v-80.938082c0-67.766356-56.348055-122.82389-125.713534-122.82389-69.295342 0-125.727562 55.127671-125.727562 122.837917v80.938083h-35.629589c-17.099397 0-31.014575 13.606575-31.014575 30.299178v221.548712c0 16.776767 13.915178 30.383342 31.014575 30.383342h322.700274c17.169534 0 31.028603-13.606575 31.028603-30.383342V461.459288c0.070137-16.692603-13.859068-30.299178-31.028603-30.299178zM531.736548 578.026959v48.352438a4.067945 4.067945 0 0 1-4.081973 3.983781h-26.694137a4.067945 4.067945 0 0 1-4.067945-3.983781v-48.352438c-12.568548-6.256219-21.251507-18.852822-21.251507-33.52548 0-20.830685 17.295781-37.789808 38.673535-37.789808 21.363726 0 38.659507 16.959123 38.659506 37.775781 0 14.686685-8.668932 27.283288-21.237479 33.539507z m60.03726-146.866849H436.841205V352.227945c0-41.773589 34.787945-75.747945 77.529425-75.747945 42.755507 0 77.473315 33.974356 77.473315 75.747945l-0.070137 78.90411z" p-id="13168"></path></svg>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="email" name="email" id="email">
                                    <label class="shut" for="email">請預留您的電子郵箱</label>
                                    <svg t="1718871599412" class="safeicon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="13167" width="200" height="200"><path d="M514.062027 1018.052384h-12.821041c-57.694685-18.936986-230.764712-113.621918-307.690959-195.682192-76.912219-88.372603-115.375342-170.432877-115.375342-265.117808v-378.739726c0-12.624658 6.410521-25.249315 19.217534-31.561644l403.848767-138.871233h19.217535l397.424219 138.871233c12.821041 6.312329 19.231562 18.936986 19.231561 31.561644v366.115068c0 100.99726-32.038575 183.057534-115.375342 265.117808-70.515726 94.684932-243.585753 189.369863-294.855891 208.30685h-12.821041z m161.52548-586.906302h-35.629589v-80.938082c0-67.766356-56.348055-122.82389-125.713534-122.82389-69.295342 0-125.727562 55.127671-125.727562 122.837917v80.938083h-35.629589c-17.099397 0-31.014575 13.606575-31.014575 30.299178v221.548712c0 16.776767 13.915178 30.383342 31.014575 30.383342h322.700274c17.169534 0 31.028603-13.606575 31.028603-30.383342V461.459288c0.070137-16.692603-13.859068-30.299178-31.028603-30.299178zM531.736548 578.026959v48.352438a4.067945 4.067945 0 0 1-4.081973 3.983781h-26.694137a4.067945 4.067945 0 0 1-4.067945-3.983781v-48.352438c-12.568548-6.256219-21.251507-18.852822-21.251507-33.52548 0-20.830685 17.295781-37.789808 38.673535-37.789808 21.363726 0 38.659507 16.959123 38.659506 37.775781 0 14.686685-8.668932 27.283288-21.237479 33.539507z m60.03726-146.866849H436.841205V352.227945c0-41.773589 34.787945-75.747945 77.529425-75.747945 42.755507 0 77.473315 33.974356 77.473315 75.747945l-0.070137 78.90411z" p-id="13168"></path></svg>
                                </div>
                                <div class="form-group">
                                    <p class="form-group-title">配送方式：</p>
                                    <div class="radio-box">

                                        <div class="form-radio">
                                            <input type="radio" id="order-type-1" name="order_type" value="1" checked>
                                            <label class="radio-label" for="order-type-1">
                                                <svg class="sevenicon" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" xml:space="preserve" width="271.95663" height="264.24695" style="fill-rule:evenodd" viewBox="0 0 272.68729 257.44435" id="svg2" version="1.1" inkscape:version="0.48.1 " sodipodi:docname="AJAX.svg"><metadata id="metadata40"><rdf:RDF><cc:Work rdf:about=""><dc:format>image/svg+xml</dc:format><dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/></cc:Work></rdf:RDF></metadata><sodipodi:namedview pagecolor="#ffffff" bordercolor="#666666" borderopacity="1" objecttolerance="10" gridtolerance="10" guidetolerance="10" inkscape:pageopacity="0" inkscape:pageshadow="2" inkscape:window-width="1280" inkscape:window-height="1004" id="namedview38" showgrid="false" inkscape:zoom="1.1149061" inkscape:cx="244.05554" inkscape:cy="140.10103" inkscape:window-x="-8" inkscape:window-y="-8" inkscape:window-maximized="1" inkscape:current-layer="svg2" fit-margin-top="0" fit-margin-left="0" fit-margin-right="0" fit-margin-bottom="0"/>
                                                    <defs id="defs4"><inkscape:perspective sodipodi:type="inkscape:persp3d" inkscape:vp_x="0 : 150 : 1" inkscape:vp_y="0 : 1000 : 0" inkscape:vp_z="300 : 150 : 1" inkscape:persp3d-origin="150 : 100 : 1" id="perspective42"/>
                                                        <style type="text/css" id="style6">

                                                            .fil1 {fill:#008061;fill-rule:nonzero}
                                                            .fil3 {fill:#ED1B2D;fill-rule:nonzero}
                                                            .fil2 {fill:#F5821F;fill-rule:nonzero}
                                                            .fil0 {fill:white;fill-rule:nonzero}

                                                        </style>

                                                    </defs>
                                                    <g id="g2862" transform="translate(-14.3907,-17.5756)"><polygon transform="matrix(0.89812868,0,0,0.89515596,15.772391,15.321752)" class="fil0" points="0,0 300.806,0 300.806,292.277 0,292.277 " id="polygon10" style="fill:#ffffff;fill-rule:nonzero"/><polygon class="fil1" points="14.3907,13.3111 287.078,13.3111 287.078,278.268 14.3907,278.268 " id="polygon12" style="fill:#008061;fill-rule:nonzero"/><path class="fil0" d="m 182.519,260.835 0,10.6078 -65.4031,0 0,-10.6276 -49.4907,0 c -4.83579,0 -8.77484,-5.05423 -8.77484,-11.2863 L 38.14156,40.0879 c 0,-6.23585 4.83579,-11.2942 10.803,-11.2942 l 199.991,0 c 5.92834,0 10.7641,5.05837 10.7641,11.2942 l -20.4749,209.441 c 0,6.23203 -3.93921,11.2863 -8.77484,11.2863 l -47.931,0.0198 z" id="path14" style="fill:#ffffff;fill-rule:nonzero" inkscape:connector-curvature="0"/><path class="fil2" d="m 74.5675,59.459 133.887,0 c -13.1823,4.94142 -53.313,32.1125 -63.3357,50.3806 l -70.4339,0 -0.117429,-50.3806 z" id="path16" style="fill:#f5821f;fill-rule:nonzero" inkscape:connector-curvature="0"/><path class="fil3" d="m 181.272,203.817 c -1.91105,18.2445 -1.98912,40.4624 -1.98912,63.8 l -58.0318,0 c 0,-23.3376 0.97512,-45.5555 2.8857,-63.8 l 57.1352,0 z" id="path18" style="fill:#ed1b2d;fill-rule:nonzero" inkscape:connector-curvature="0"/><polygon class="fil1" points="91.4939,177.453 91.4939,184.524 82.212,184.524 82.212,191.598 91.4939,191.598 91.4939,199.703 70.2776,199.703 70.2776,161.986 91.4939,161.986 91.4939,170.387 82.212,170.387 82.212,177.453 " id="polygon20" style="fill:#008061;fill-rule:nonzero"/><polygon class="fil1" points="140.828,177.453 140.828,184.524 131.586,184.524 131.586,191.598 140.828,191.598 140.828,199.703 119.652,199.703 119.652,161.986 140.828,161.986 140.828,170.387 131.586,170.387 131.586,177.453 " id="polygon22" style="fill:#008061;fill-rule:nonzero"/><polygon class="fil1" points="195.194,177.453 195.194,184.524 185.912,184.524 185.912,191.598 195.194,191.598 195.194,199.703 173.978,199.703 173.978,161.986 195.194,161.986 195.194,170.387 185.912,170.387 185.912,177.453 " id="polygon24" style="fill:#008061;fill-rule:nonzero"/><polygon class="fil1" points="107.406,161.986 107.406,191.598 116.687,191.598 116.687,199.703 95.4718,199.703 95.4718,161.986 " id="polygon26" style="fill:#008061;fill-rule:nonzero"/><polygon class="fil1" points="157.95,187.477 153.425,161.986 143.013,161.986 148.94,199.703 164.267,199.703 171.443,161.986 162.59,161.986 " id="polygon28" style="fill:#008061;fill-rule:nonzero"/><polygon class="fil1" points="200.225,161.986 211.692,161.986 211.692,199.699 200.225,199.699 " id="polygon30" style="fill:#008061;fill-rule:nonzero"/><path class="fil1" d="m 211.691,173.183 c 0.62443,-4.13025 6.47422,-3.02241 6.47422,-0.66331 l 0,27.183 11.5049,0 0,-31.9681 c 0,-6.77823 -9.5551,-9.50443 -17.901,-3.53721 l -0.0781,8.98564 z" id="path32" style="fill:#008061;fill-rule:nonzero" inkscape:connector-curvature="0"/><path class="fil3" d="m 186.654,156.682 c 1.16967,-14.4341 20.9425,-38.0054 40.0914,-44.1948 l 0,-55.6845 c -52.6885,23.572 -86.9308,59.2219 -94.3404,99.7387 l 54.2489,0.14053 z" id="path34" style="fill:#ed1b2d;fill-rule:nonzero" inkscape:connector-curvature="0"/></g>
                                            </svg>
                                                <span class="text">7-Eleven便利店</br>取貨付款</span>
                                            </label>
                                        </div>

                                        <div class="form-radio">
                                            <input type="radio" id="order-type-0" name="order_type" value="0">
                                            <label class="radio-label" for="order-type-0">
                                                <svg class="sevenicon" style="transform: scale(1.15);" version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1548 1123" width="1548" height="1123">
                                                    <title>e_ir2021_2_00_A3-1 (1)-pdf-svg (2)-svg</title>
                                                    <style>
                                                        .s0 { fill: #fdd000 }
                                                        .s1 { fill: #000000 }
                                                    </style>
                                                    <path id="Path 77" class="s0" d="m0.4 562.1c0-373.3 343.4-561.6 775.9-561.6 432.6 0 771 188.3 771 561.6 0 371.7-338.4 556.7-771 560-432.5 4.9-775.9-188.3-775.9-560z"/>
                                                    <path id="Path 78" class="s1" d="m1431.8 444.9l-262.6 168.4 87.6 355.2-3.4 3.3h-97.4l-4.9-1.7-156.9-189.9h-259.2l-156.8 189.9-5 1.7h-97.4l-3.3-3.3 59.5-237.9-52.9-62.8v61.2c0 89.2-72.6 160.2-160.1 161.8h-54.5l-3.3-3.3v-36.3l1.7-3.3 56.1-38v-56.2l-76-38-4.9-6.6v-34.6l3.3-3.3h77.6v-5c-6.6 0-19.8-1.7-33-5-19.8-8.2-38-21.4-49.6-39.6-13.2-19.8-19.8-42.9-19.8-74.3v-97.5h18.2l61.1 61.1h19.8 3.3v-3.3c0 0-23.1-11.5-41.3-51.2-9.9-26.4-16.5-56.1-16.5-92.5v-176.7h19.8l112.3 117.3h176.6l112.3-117.3h19.8v176.7c0 33.1-4.9 62.8-14.8 87.6-6.6 14.8-14.9 23.1-14.9 23.1v4.9h462.3l277.3-105.7 5 1.7 14.9 64.4z"/>
                                                    <path id="Path 79" fill-rule="evenodd" class="s0" d="m0.4 562.1c0-373.3 343.4-561.6 775.9-561.6 432.6 0 771 188.3 771 561.6 0 371.7-338.4 556.7-771 560-432.5 4.9-775.9-188.3-775.9-560zm442.4-150.3c5-6.6 3.3-14.8-4.9-21.5-3.3-1.6-33.1-23.1-79.3-26.4-11.5-1.6-19.8 5-19.8 14.9 0 11.5 5 18.2 18.2 18.2 34.6 3.3 64.4 18.1 69.3 19.8 5 1.6 13.2 1.6 16.5-5zm-39.6 181.7c3.3-1.6 26.4-13.2 51.2-14.8 9.9-1.7 13.2-5 13.2-13.3 0-8.2-5-13.2-14.9-11.5-34.6 1.6-56.1 18.1-57.8 19.8-6.6 5-6.6 9.9-4.9 14.9 3.3 6.6 9.9 6.6 13.2 4.9zm-56.1-4.9c3.3-5 1.6-9.9-3.3-14.9-3.3-1.7-24.8-18.2-59.5-19.8-8.2-1.7-14.8 3.3-14.8 11.5 0 8.3 3.3 11.6 13.2 13.3 26.4 1.6 47.8 13.2 51.1 14.8 3.4 1.7 10 1.7 13.3-4.9zm189.8-171.8c5-1.7 34.7-16.5 69.4-19.8 13.2 0 18.1-6.7 18.1-18.2-1.6-9.9-8.2-16.5-19.8-14.9-46.2 3.3-77.6 24.8-80.9 26.4-8.2 6.7-8.2 14.9-4.9 21.5 4.9 6.6 13.2 6.6 18.1 5z"/>
                                                    <path id="Path 80" class="s1" d="m1431.8 444.9l-262.6 168.4 87.6 355.2-3.4 3.3h-97.4l-4.9-1.7-156.9-189.9h-259.2l-156.8 189.9-5 1.7h-97.4l-3.3-3.3 59.5-237.9-52.9-62.8v61.2c0 89.2-72.6 160.2-160.1 161.8h-54.5l-3.3-3.3v-36.3l1.7-3.3 56.1-38v-56.2l-76-38-4.9-6.6v-34.6l3.3-3.3h77.6v-5c-6.6 0-19.8-1.7-33-5-19.8-8.2-38-21.4-49.6-39.6-13.2-19.8-19.8-42.9-19.8-74.3v-97.5h18.2l61.1 61.1h19.8 3.3v-3.3c0 0-23.1-11.5-41.3-51.2-9.9-26.4-16.5-56.1-16.5-92.5v-176.7h19.8l112.3 117.3h176.6l112.3-117.3h19.8v176.7c0 33.1-4.9 62.8-14.8 87.6-6.6 14.8-14.9 23.1-14.9 23.1v4.9h462.3l277.3-105.7 5 1.7 14.9 64.4z"/>
                                                    <path id="Path 81" class="s0" d="m518.8 411.8c-3.3-6.6-3.3-14.8 4.9-21.5 3.3-1.6 34.7-23.1 80.9-26.4 11.6-1.6 18.2 5 19.8 14.9 0 11.5-4.9 18.2-18.1 18.2-34.7 3.3-64.4 18.1-69.4 19.8-4.9 1.6-13.2 1.6-18.1-5zm-92.5 5c-4.9-1.7-34.7-16.5-69.3-19.8-13.2 0-18.2-6.7-18.2-18.2 0-9.9 8.3-16.5 19.8-14.9 46.2 3.3 76 24.8 79.3 26.4 8.2 6.7 9.9 14.9 4.9 21.5-3.3 6.6-11.5 6.6-16.5 5zm-36.3 171.8c-1.7-5-1.7-9.9 4.9-14.9 1.7-1.7 23.2-18.2 57.8-19.8 9.9-1.7 14.9 3.3 14.9 11.5 0 8.3-3.3 11.6-13.2 13.3-24.8 1.6-47.9 13.2-51.2 14.8-3.3 1.7-9.9 1.7-13.2-4.9zm-56.2 4.9c-3.3-1.6-24.7-13.2-51.1-14.8-9.9-1.7-13.2-5-13.2-13.3 0-8.2 6.6-13.2 14.8-11.5 34.7 1.6 56.2 18.1 59.5 19.8 4.9 5 6.6 9.9 3.3 14.9-3.3 6.6-9.9 6.6-13.3 4.9z"/>
                                                </svg>
                                                <span class="text">黑貓宅配到府</br>貨到付款</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <p class="form-group-title" id="order-type-title">配送至門店</p>
                                    <div class="form-select">
                                        <div class="select-box" id="load-1">
                                            <select name="city" id="city">
                                                <option value="">選擇縣市</option>
                                            </select>
                                        </div>

                                        <div class="select-box" id="load-2">
                                            <select name="county" id="county">
                                                <option value="">選擇地區</option>
                                            </select>
                                        </div>

                                        <div class="select-box" id="load-3">
                                            <select name="street" id="street">
                                                <option value="">選擇路段</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="form-address" id="form-address-row">
                                        <input class="form-control" type="text" name="address" id="address">
                                        <label class="shut" for="address">詳細地址</label>
                                    </div>

                                    <div class="form-store" id="form-store-row">

                                    </div>
                                    <input type="hidden" name="shop_name" id="shop_name" value="">
                                </div>


                                <div class="form-group">
                                    <p class="form-group-title">訂單留言</p>
                                    <div class="textarea-container">
                                        <textarea class="form-textarea" name="remarks" placeholder=""></textarea>
                                        <p class="textarea-placeholder">
                                            1. 提交訂單時，請仔細核對商品項目及數量
                                            2. 如有指定日期送達需求，請於備註欄註明
                                            3. 訂單成立後，如需變更或取消，請聯絡客服
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>

                        



                    </div>

                </div>
            </form>
        </div>
{{--        <section id="vue-app">


            <form onsubmit="return orderStore();" method="POST" action="{{ url('order') }}" id="order-form">

                {{ csrf_field() }}
                <input type="hidden" value="{{ request()->keyt }}" name="keyt">
                <input type="hidden" value="{{ $goods->id }}" name="goods_id">

                <div class="form-card">
                    <div class="form-item clearfix">
                        <label class="form-item-label">收貨人</label>
                        <input class="form-item-input" placeholder="請填寫收貨人姓名" autocomplete="off" name="name" type="text">
                    </div>
                    <div class="form-item clearfix">
                        <label class="form-item-label">收貨電話</label>
                        <input class="form-item-input" placeholder="如：0912345678" autocomplete="off" type="number"  pattern="[0-9]*" name="phone" >
                    </div>
                    <div class="form-item clearfix">
                        <label class="form-item-label">電子信箱</label>
                        <input class="form-item-input" placeholder="如：example@email.com" autocomplete="off" type="email" autocapitalize="on" name="email">
                    </div>
                </div>

                <div class="split_line"></div>

                <div class="form-card">
                    <div class="form-item form-item-bala clearfix">
                        <label class="form-item-label">配送方式</label>
                        <div class="form-balance" style="margin-right: 0.35rem">


                            <div class="balance-item deva-item">
                                <input id="order_type_1" name="order_type" value="1" type="radio" class="input-hide" checked>
                                <label for="order_type_1" data-id="1" class="balance-label"><span><img style="width: 0.6rem" src="/static/mobile/img/711.png" alt=""></span><span class="balance-label-span2">7-11超商（取貨付款）</span></label>
                            </div>

                            <div class="balance-item deva-item">
                                <input id="order_type_0" name="order_type" value="0" type="radio" class="input-hide">
                                <label for="order_type_0" data-id="0" class="balance-label"><span><img style="width: 0.6rem" src="/static/mobile/img/heimao.png" alt=""></span><span class="balance-label-span2">宅配到府（貨到付款）</span></label>
                            </div>



                        </div>
                    </div>
                    <div class="form-item form-item-bala clearfix">
                        <label class="form-item-label">配送地區</label>
                        <div class="form-balance address-balance">
                            <div class="balance-item select-city-box" id="load-1">

                                <select name="city" id="city" class="form-item-input" style="padding-right: 0.2rem;    direction: rtl;">
                                    <option value="">選擇縣市</option>
                                </select>
                                <i class="iconfont ysj">&#xe61c;</i>
                            </div>
                            <div class="balance-item select-city-box" id="load-2">
                                <select name="county" id="county" class="form-item-input" style="padding-right: 0.2rem;    direction: rtl;">
                                    <option value="">選擇地區</option>
                                </select>
                                <i class="iconfont ysj">&#xe61c;</i>
                            </div>
                            <div class="balance-item select-city-box" id="load-3">
                                <select name="street" id="street" class="form-item-input" style="padding-right: 0.2rem;    direction: rtl;">
                                    <option value="">選擇路段</option>
                                </select>

                                <i class="iconfont ysj">&#xe61c;</i>

                            </div>
                        </div>
                    </div>
                    <div class="form-item" id="form-address-row">
                        <label class="form-item-label">詳細地址</label>
                        <input class="form-item-input" placeholder="如：1巷1弄1號" type="text"  name="address">
                    </div>
                    <div class="form-item form-item-bala clearfix" id="store-row-main" style="display: none">
                        <label class="form-item-label">選擇門市</label>
                        <div class="form-balance store-balance">


                            <div class="store-item-fe" id="casual" style="display: none">
                                <div class="store-fi-row clearfix">
                                    <input type="radio" checked id="store-000" name="store_id2" class="input-hide">
                                    <label for="store-000">
                                        <div class="store-box">
                                            <div class="store-icon"><img src="/static/img/711.jpg" alt="超商取貨"></div>
                                            <div class="store-info">
                                                <p class="store-name"></p>
                                                <p class="store-city"></p>
                                                <p class="store-road"></p>
                                                <p class="store-address"></p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="store-more">
                                    <a href="javascript:;">修改門市</a>
                                    <input style="display: none" class="temp_store_radio" type="radio" name="store_id" value="0">
                                </div>
                            </div>

                            <div class="balance-item show-store-shop clearfix" id="show-store-shop" data-track-block="m_co_store">


                            </div>

                        </div>
                    </div>

                </div>

                <div class="split_line"></div>

                <div class="form-card">
                    <div class="form-text-item">
                        <label class="form-item-label no-position">訂單備註</label>
                        <textarea class="form-item-textarea" autocomplete="off" placeholder="（選填）" name="remarks"></textarea>
                    </div>
                </div>

                <div class="place-description">
                    <p>1. 您可以在訂單備註留下您的特殊要求，如：需要延遲發貨時間，或到貨時間，我們將盡力按照您的要求來處理訂單。</p>
                    <p>2. 訂單生成後將無法自行修改，請聯絡本站客服為您修改。</p>
                    <p>3. 請您再次確認您的訂單資訊，核實後點擊提交訂單。</p>
                </div>


            </form>

        </section>--}}
    </div>

    <div class="sku-main" data-track-block="m_co_sku_panel">
        <div class="sku-shade" onclick="$('.sku-main').removeClass('inshow')"></div>
        <div class="sku-wrap">
            <div class="goods-template-goods-info clearfix">
                <div class="goods-template-goods-img">
                    <img class="goods-img" src="{{ asset_upload($goods->img) }}" alt="{{ $goods->name }}">
                </div>
                <div class="goods-template-price">
                    <p class="goods-template-title">{{ $goods->name }}</p>
                    <p class="goods-template-grey-price">原價：<span class="twd">NT$</span>{{ number_format(round($goods->market_price)) }}</p>
                    <p class="goods-template-red-price">優惠價：<span class="twd">NT$</span>{{ number_format(round($goods->price)) }}</p>
                </div>
                <span class="iconfont icon-guanbi close-goods" onclick="$('.sku-main').removeClass('inshow')"></span>
            </div>
            <div class="goods-sku clearfix">
                @foreach($product as $item)
                    <div class="sku-item col-xs-4" ><a class="{{ $goods->id==$item->id?"article":'' }}" href="javascript:;" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-img="{{ asset_upload($item->img) }}" data-price="{{ round($item->price) }}" data-original="{{ round($item->market_price) }}">{{ $item->quantity }}盒裝</a></div>
                @endforeach
            </div>
            <div class="goods-affirm">
                <button>確定</button>
            </div>
        </div>
    </div>
@endsection




