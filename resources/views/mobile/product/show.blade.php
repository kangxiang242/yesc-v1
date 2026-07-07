@extends('mobile.layout')

@section('track-init')
<script>Track.init({ platform: 'mobile', page_type: 'product_detail', goods_id: {{ $product->id }} });</script>
@endsection

@section('og-title', $product->name)
@section('og-image', asset_upload($product->m_img ?: $product->img))

@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ release_asset('/static/mobile/css/product-desc.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ release_asset('/static/mobile/less/goods.css') }}"/>
    <style>
        .container-fluid{
            padding-bottom: 0;
        }
    </style>
@stop

@section('script')
    @parent

    {{-- Product Schema --}}
    <x-schema.product :product="$product" />

    {{-- Breadcrumb Schema --}}
    <x-schema.breadcrumb :items="[['name' => '首頁', 'url' => '/'], ['name' => '威而鋼訂購', 'url' => '/product'], ['name' => $product->name, 'url' => '/product/'.$product->id]]" />

    <script id="PRODUCT-M-2">
        $('.goods-imgs\x20.cover')["\u0063\u006c\u0069\u0063\u006b"](function(){var _0x940f46=$(this)["\u0061\u0074\u0074\u0072"]("\u0073\u0072\u0063");$("gmi-wohs#".split("").reverse().join(""))["\u0061\u0074\u0074\u0072"]("crs".split("").reverse().join(""),_0x940f46);$(this)["\u0061\u0064\u0064\u0043\u006c\u0061\u0073\u0073"]("etavitca".split("").reverse().join(""))["\u0073\u0069\u0062\u006c\u0069\u006e\u0067\u0073"]()['removeClass']("etavitca".split("").reverse().join(""));});$("alaix-noci.".split("").reverse().join(""))['click'](function(){$(this)['parent']()['hide']();$(this)["\u0070\u0061\u0072\u0065\u006e\u0074"]()['siblings']()["\u0073\u0068\u006f\u0077"]();});$("1algnahs-noci.".split("").reverse().join(""))["\u0063\u006c\u0069\u0063\u006b"](function(){$(this)['parents']("ton-edih.".split("").reverse().join(""))["\u0068\u0069\u0064\u0065"]();$(this)['parents']("\u002e\u0068\u0069\u0064\u0065\u002d\u006e\u006f\u0074")["\u0073\u0069\u0062\u006c\u0069\u006e\u0067\u0073"]()['show']();});$(document)['ready'](function(){const _0x2f35ff=$("edils-gmi.".split("").reverse().join(""));const _0x14b457=$("gmi edils-gmi.".split("").reverse().join(""));const _0x2ed63c=_0x14b457["\u006c\u0065\u006e\u0067\u0074\u0068"];let _0x15446d=0x3fa6f^0x3fa6f;function _0x133ab9(){_0x15446d++;if(_0x15446d>=_0x2ed63c){_0x15446d=0x49359^0x49359;}_0x2f35ff["\u0063\u0073\u0073"]("mrofsnart".split("").reverse().join(""),'translateX('+-_0x15446d*(0xebbbb^0xebb89)+")%".split("").reverse().join(""));}setInterval(_0x133ab9,0xbd74f^0xbc80f);let _0x2fb90d,_0x10b6a6;$("\u002e\u0073\u0068\u006f\u0070\u002d\u0069\u006d\u0067")["\u006f\u006e"]("tratshcuot".split("").reverse().join(""),function(_0x311cb8){_0x2fb90d=_0x311cb8['originalEvent']["\u0074\u006f\u0075\u0063\u0068\u0065\u0073"][0x64e4b^0x64e4b]['pageX'];});$("gmi-pohs.".split("").reverse().join(""))['on']('touchmove',function(_0x25f174){_0x10b6a6=_0x25f174['originalEvent']['touches'][0x84bc1^0x84bc1]["\u0070\u0061\u0067\u0065\u0058"];});$("\u002e\u0069\u006d\u0067\u002d\u0073\u006c\u0069\u0064\u0065")["\u006f\u006e"]("\u0074\u006f\u0075\u0063\u0068\u0065\u006e\u0064",function(){if(_0x2fb90d>_0x10b6a6+(0xce629^0xce628)){_0x15446d++;if(_0x15446d>=_0x2ed63c){_0x15446d=0x63732^0x63732;}}else if(_0x2fb90d<_0x10b6a6-(0x614cf^0x614ce)){_0x15446d--;if(_0x15446d<(0xb57b7^0xb57b7)){_0x15446d=_0x2ed63c-0x1;}}_0x2f35ff["\u0063\u0073\u0073"]("\u0074\u0072\u0061\u006e\u0073\u0066\u006f\u0072\u006d",'translateX('+-_0x15446d*0x32+")%".split("").reverse().join(""));});});$(window)["\u006f\u006e"]("llorcs".split("").reverse().join(""),function(){$("xob-tcudorp.".split("").reverse().join(""))["\u0065\u0061\u0063\u0068"](function(){var _0x5c8530=$(this)['offset']()["\u0074\u006f\u0070"];var _0x383ea1=$(window)['height']();var _0x2c1174=$(window)["\u0073\u0063\u0072\u006f\u006c\u006c\u0054\u006f\u0070"]();var _0x3797e0=_0x383ea1*0.3;if(_0x5c8530-_0x2c1174<=_0x3797e0){$(this)['find']('.secret\x20.packageleft\x20.stamp-text')["\u0061\u0064\u0064\u0043\u006c\u0061\u0073\u0073"]("wohs-txet-pmats".split("").reverse().join(""));}});});
    
        var _0xf49b7e=(799169^799176)+(537601^537600);const salesSwiper=document['\u0067\u0065\u0074\u0045\u006C\u0065\u006D\u0065\u006E\u0074\u0042\u0079\u0049\u0064']("\u0073\u0061\u006C\u0065\u0073\u0053\u0077\u0069\u0070\u0065\u0072");_0xf49b7e=(638841^638845)+(219283^219281);var _0xe73c;const height=salesSwiper['\u0063\u0068\u0069\u006C\u0064\u0072\u0065\u006E'][807054^807054]['\u006F\u0066\u0066\u0073\u0065\u0074\u0048\u0065\u0069\u0067\u0068\u0074'];_0xe73c=(577554^577553)+(793676^793668);let isAnimating=false;var _0x83354e=(946676^946674)+(828367^828362);let counter=293546^293546;_0x83354e=(948555^948546)+(435004^435007);var _0x5c68b=(885285^885293)+(391561^391560);const FIXED_INTERVAL=1002052^999932;_0x5c68b=(670064^670069)+(591913^591914);var _0x04gacf;const RANDOM_MIN=654601^650881;_0x04gacf=(364962^364970)+(464013^464005);var _0xd_0xe43=(729408^729408)+(707394^707395);const RANDOM_MAX=261876^252388;_0xd_0xe43=916499^916496;const BOX_OPTIONS=[316567^316565,597583^597580,907265^907269,219223^219218,129280^129286,121766^121774,402954^402944,326584^326580,378480^378464,354341^354353,149772^149780,787694^787696,999256^999280];function randomInt(min,max){return Math['\u0066\u006C\u006F\u006F\u0072'](Math['\u0072\u0061\u006E\u0064\u006F\u006D']()*(max-min+(239785^239784)))+min;}function generateRandomPhoneSuffix(){return randomInt(579576^579484,785905^785942);}function generateRandomBoxCount(){const _0xfd7bb=randomInt(376779^376779,BOX_OPTIONS['\u006C\u0065\u006E\u0067\u0074\u0068']-(601995^601994));return BOX_OPTIONS[_0xfd7bb];}function updateNextSalesInfo(){var _0x1bggc=(265996^265997)+(806509^806505);const _0x76bf1d=salesSwiper['\u0063\u0068\u0069\u006C\u0064\u0072\u0065\u006E'][855462^855463];_0x1bggc="ddlpol".split("").reverse().join("");if(_0x76bf1d){var _0x4b_0xaad=(507593^507595)+(162853^162860);const _0x3f7dcd=generateRandomPhoneSuffix();_0x4b_0xaad=(649574^649572)+(160210^160218);const _0x6ff=generateRandomBoxCount();_0x76bf1d['\u0069\u006E\u006E\u0065\u0072\u0048\u0054\u004D\u004C']=`感恩末三碼09*****${_0x3f7dcd}顧客訂購<span>${_0x6ff}盒</span>　剛剛`;}}function initializeSalesInfo(){const _0xab8g=salesSwiper['\u0071\u0075\u0065\u0072\u0079\u0053\u0065\u006C\u0065\u0063\u0074\u006F\u0072\u0041\u006C\u006C']("\u002E\u0073\u0061\u006C\u0065\u0073\u002D\u006E\u006F\u0077");_0xab8g['\u0066\u006F\u0072\u0045\u0061\u0063\u0068'](item=>{const _0x2fdec=generateRandomPhoneSuffix();const _0xd8ec1c=generateRandomBoxCount();item['\u0069\u006E\u006E\u0065\u0072\u0048\u0054\u004D\u004C']=`感恩末三碼09*****${_0x2fdec}顧客訂購<span>${_0xd8ec1c}盒</span>　剛剛`;});}function startSwiper(){setInterval(()=>{if(isAnimating)return;isAnimating=!![];var _0xf8fa=(582663^582671)+(772625^772629);const _0x2ag=counter%(611639^611637)===(923361^923361)?randomInt(RANDOM_MIN,RANDOM_MAX):FIXED_INTERVAL;_0xf8fa=(752452^752448)+(961943^961936);setTimeout(()=>{updateNextSalesInfo();salesSwiper['\u0073\u0074\u0079\u006C\u0065']['\u0074\u0072\u0061\u006E\u0073\u0069\u0074\u0069\u006F\u006E']=")1 ,0 ,0 ,5.0(reizeb-cibuc s1 mrofsnart".split("").reverse().join("");salesSwiper['\u0073\u0074\u0079\u006C\u0065']['\u0074\u0072\u0061\u006E\u0073\u0066\u006F\u0072\u006D']=`translateY(-${height}px)`;setTimeout(()=>{salesSwiper['\u0073\u0074\u0079\u006C\u0065']['\u0074\u0072\u0061\u006E\u0073\u0069\u0074\u0069\u006F\u006E']="\u006E\u006F\u006E\u0065";salesSwiper['\u0061\u0070\u0070\u0065\u006E\u0064\u0043\u0068\u0069\u006C\u0064'](salesSwiper['\u0063\u0068\u0069\u006C\u0064\u0072\u0065\u006E'][978995^978995]);salesSwiper['\u0073\u0074\u0079\u006C\u0065']['\u0074\u0072\u0061\u006E\u0073\u0066\u006F\u0072\u006D']=`translateY(0)`;setTimeout(()=>{salesSwiper['\u0073\u0074\u0079\u006C\u0065']['\u0074\u0072\u0061\u006E\u0073\u0069\u0074\u0069\u006F\u006E']="\u0074\u0072\u0061\u006E\u0073\u0066\u006F\u0072\u006D\u0020\u0031\u0073\u0020\u0063\u0075\u0062\u0069\u0063\u002D\u0062\u0065\u007A\u0069\u0065\u0072\u0028\u0030\u002E\u0035\u002C\u0020\u0030\u002C\u0020\u0030\u002C\u0020\u0031\u0029";isAnimating=false;},611177^611163);},335401^335297);},_0x2ag);counter++;},FIXED_INTERVAL);}initializeSalesInfo();startSwiper();
    </script>
    <script>
        function getRandomNumber(){return Math['\u0066\u006C\u006F\u006F\u0072'](Math['\u0072\u0061\u006E\u0064\u006F\u006D']()*((858563^855731)-(782525^786205)+(812258^812259)))+(572746^570090);}function updateRandomNumber(){var _0x55e2d=(171796^171794)+(868471^868464);var _0xcc83d=Date['\u006E\u006F\u0077']();_0x55e2d="ffoagc".split("").reverse().join("");var _0x88f12g=localStorage['\u0067\u0065\u0074\u0049\u0074\u0065\u006D']("\u006C\u0061\u0073\u0074\u0055\u0070\u0064\u0061\u0074\u0065\u0054\u0069\u006D\u0065");var _0x125a6a=localStorage['\u0067\u0065\u0074\u0049\u0074\u0065\u006D']("\u0074\u006F\u0074\u0061\u006C\u0073\u0061\u006C\u0065");var _0x9f3abd=(522396^522384)*(255750^255802)*(187159^187179)*(797165^797189);if(!_0x88f12g||_0xcc83d-_0x88f12g>=_0x9f3abd){var _0x57agea=getRandomNumber();localStorage['\u0073\u0065\u0074\u0049\u0074\u0065\u006D']("elaslatot".split("").reverse().join(""),_0x57agea);localStorage['\u0073\u0065\u0074\u0049\u0074\u0065\u006D']("\u006C\u0061\u0073\u0074\u0055\u0070\u0064\u0061\u0074\u0065\u0054\u0069\u006D\u0065",_0xcc83d);_0x125a6a=_0x57agea;}document['\u0067\u0065\u0074\u0045\u006C\u0065\u006D\u0065\u006E\u0074\u0042\u0079\u0049\u0064']("\u0074\u006F\u0074\u0061\u006C\u0073\u0061\u006C\u0065")['\u0069\u006E\u006E\u0065\u0072\u0054\u0065\u0078\u0074']=_0x125a6a;}updateRandomNumber();setInterval(updateRandomNumber,(938559^938547)*(379617^379613)*(305242^305254)*(658309^657517));
    </script>
@stop

@section('header-class','other-header')
@section('content')
    <div class="row">
        <div class="breadcrumb-box">
            <ul class="breadcrumb">
                <li><a href="/">首頁</a></li>
                <li><a href="/product">威而鋼訂購</a></li>
                <li class="active">{{ $product->name }}</li>
            </ul>
        </div>
    </div>


    <div class="row shop">
        <div class="product-row">
            <div class="product-box clearfix" data-track-block="m_pd_shop_main">
                <div class="clearfix">
                    <div class="shop-img">
                        <div class="img-slide">
                            <img src="{{ asset_upload($product->m_img?:$product->img) }}" alt="{{ $product->name }}" style="transform: scale(0.8);">
                            <img src="/static/mobile/img/goods-1.jpg" alt="威而鋼">
                        </div>
                    </div>
                    <div class="shop-text">
                        <h1 class="main-title">{{ $product->name }}</h1>
                        <div class="goods-label-sec">
                            <p class="goods-label">100mg/粒</p>
                            <p class="goods-label">一盒4粒</p>
                            <p class="goods-label">原廠正品</p>
                            <p class="goods-label">無效退款</p>
                            @if($product->quantity >= 4)
                                <p class="goods-label">免運費</p>
                                @else
                                <p class="goods-label">4盒免運</p>
                            @endif
                        </div>
                        
                        <p class="red-price"><span class="twd">NT$</span>{{ number_format(round($product->price)) }}<span class="grey-price">NT$ {{ number_format(round($product->market_price)) }}</span></p>
                        <div class="buy-button" data-track-block="m_pd_buy_cta">
                            <a href="{{ url('shopping/'.$product->id) }}" data-track="m_product_buy" data-track-zone="content" data-goods-id="{{ $product->id }}"><button type="button">立即訂購</button></a>
                        </div>

                        <div class="sales-sec" data-track-block="m_pd_sales_swiper"> 
                            <p class="sales-week">近七天已售<span id="totalsale"></span>盒</p>
                            <div class="order-views">
                                <div class="sales-swiper" id="salesSwiper">
                                    <p class="sales-now">感恩末三碼09*****<span class="phone-suffix"></span>顧客訂購<span class="box-count"></span>盒&nbsp;&nbsp;剛剛</p>
                                    <p class="sales-now">感恩末三碼09*****<span class="phone-suffix"></span>顧客訂購<span class="box-count"></span>盒&nbsp;&nbsp;剛剛</p>
                                </div>
                            </div>
                        </div>
                        <div class="ensures">
                            <div class="icons">
                                <p class="ioc"><svg t="1740985282332" class="salesicon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="10685" width="200" height="200"><path d="M25.208829 336.71419v659.614465c0.86337 15.540655 14.677285 28.491201 30.21794 27.627831h906.538204c15.540655 0.86337 29.35457-11.223806 30.21794-27.627831V336.71419H25.208829z m590.544887 309.949729l-216.705799 267.644613 73.386426-188.214599h-81.156754l82.020123-221.886017h151.089701L525.963265 646.663919h89.790451zM478.477931 6.906958v275.41494H25.208829L131.403304 41.441746c10.360437-21.584243 31.94468-35.398158 56.119032-34.534788h290.955595zM830.732776 6.906958c24.174352-0.86337 45.758595 12.950546 56.119032 34.534788L992.182913 282.321898H538.913811V6.906958h291.818965z" p-id="10686"></path></svg><span>現在下單</span></p>
                                <p class="ico-sub">預計&nbsp;{{ date('n月j日',strtotime('+1 day')) }}～{{ date('n月j日',strtotime('+2 day')) }}&nbsp;可送達指定地址</p>
                            </div>
                            <div class="icons">
                                <p class="ioc"><svg t="1740985536511" class="salesicon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="33474" width="200" height="200"><path d="M379.75365 1023.999451a169.343819 169.343819 0 0 1-29.257111-2.468568 163.583825 163.583825 0 0 1-102.290176-65.590787 463.140075 463.140075 0 0 1-57.234225-104.813602 296.228254 296.228254 0 0 1-143.176989-80.255914A164.717538 164.717538 0 0 1 6.341479 625.18845a243.602025 243.602025 0 0 1 21.942834-62.335933 384.182446 384.182446 0 0 0 17.11541-41.654813 268.415712 268.415712 0 0 0-19.913122-51.035373 237.604317 237.604317 0 0 1-23.771403-67.419357 161.188399 161.188399 0 0 1 9.508562-82.377054 169.800961 169.800961 0 0 1 42.422811-59.721079 400.456714 400.456714 0 0 1 119.497015-69.906211 279.953986 279.953986 0 0 1 70.948495-133.741571A176.822668 176.822668 0 0 1 370.555946 4.169687a208.109491 208.109491 0 0 1 36.004533 3.236568 261.028292 261.028292 0 0 1 49.170233 13.988556 140.580421 140.580421 0 0 1 16.932553 8.612563 97.298181 97.298181 0 0 0 28.178256 12.324558 19.455979 19.455979 0 0 0 2.779425 0.365714c0.877713 0 1.828569 0.109714 2.724569 0.109714a182.984947 182.984947 0 0 0 61.513077-21.083406 223.981474 223.981474 0 0 1 53.796513-20.114264 200.3015 200.3015 0 0 1 25.179402-1.645713 164.717538 164.717538 0 0 1 28.543969 2.431998 167.643249 167.643249 0 0 1 104.22846 67.27307 500.753749 500.753749 0 0 1 57.307367 101.394177 286.957407 286.957407 0 0 1 142.280991 81.023914 171.190674 171.190674 0 0 1 42.349669 144.895844 261.832862 261.832862 0 0 1-23.222833 68.333641c-5.796565 12.927986-11.775987 26.294829-16.621696 40.703957a355.949333 355.949333 0 0 0 19.108551 52.022801 190.902653 190.902653 0 0 1 21.57712 112.347308c-13.659414 92.653615-81.645627 124.818152-147.400985 155.940405-7.058278 3.327996-14.262842 6.747421-21.229692 10.14856a299.647679 299.647679 0 0 1-69.906211 131.382717 167.094678 167.094678 0 0 1-123.20901 53.394228 197.320931 197.320931 0 0 1-29.915397-2.340569l-3.529139-0.621713a259.12658 259.12658 0 0 1-64.127931-22.326834 413.786985 413.786985 0 0 0-41.307385-16.877696 270.628281 270.628281 0 0 0-53.577085 21.083406 201.36207 201.36207 0 0 1-65.02393 22.454833 206.811207 206.811207 0 0 1-23.40569 1.371427z m294.070542-508.342312a116.297018 116.297018 0 0 0-88.155334 34.87082 132.059287 132.059287 0 0 0-33.023964 94.299327 120.137014 120.137014 0 0 0 31.085681 86.674193 109.019312 109.019312 0 0 0 81.865055 32.201108 115.199877 115.199877 0 0 0 87.66162-34.870819 131.529002 131.529002 0 0 0 32.914251-93.641043 123.995296 123.995296 0 0 0-29.549683-88.319905 108.854741 108.854741 0 0 0-82.797626-31.213681zM634.985377 280.887105L325.499994 757.06488h81.042199l310.125382-476.177775zM377.303367 274.285969a116.242161 116.242161 0 0 0-88.155334 34.852534A132.13243 132.13243 0 0 0 256.105783 403.456116a120.118728 120.118728 0 0 0 31.085681 86.655907 108.964455 108.964455 0 0 0 81.865055 32.201109 115.803304 115.803304 0 0 0 87.643335-34.523392 130.30386 130.30386 0 0 0 32.91425-93.257043 124.818152 124.818152 0 0 0-29.714254-88.667333A108.379312 108.379312 0 0 0 377.303367 274.285969z m292.205401 431.01211c-31.085681 0-46.811378-21.44912-46.811378-63.762218a87.442192 87.442192 0 0 1 12.031987-50.559946 41.636527 41.636527 0 0 1 36.114247-16.841124 39.6251 39.6251 0 0 1 33.188536 16.932553 79.030772 79.030772 0 0 1 12.617129 47.817091 86.564479 86.564479 0 0 1-11.794273 49.810233 40.685671 40.685671 0 0 1-35.327962 16.603411z m-296.520825-241.37117c-30.628539 0-46.153093-21.44912-46.153093-63.762218a86.966764 86.966764 0 0 1 12.123416-50.559946 42.057098 42.057098 0 0 1 36.370246-16.841124c29.750825 0 44.836523 21.778262 44.836524 64.749645a86.564479 86.564479 0 0 1-11.794273 49.810232 40.685671 40.685671 0 0 1-35.38282 16.566839z" p-id="33475"></path></svg><span>優惠福利</span></p>
                                <p class="ico-sub">訂購組合享受折扣優惠 / 四盒以上免運優惠</p>
                            </div>
                            <div class="icons">
                                <p class="ioc"><svg t="1744007031156" class="salesicon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3551" width="200" height="200"><path d="M512 512m-512 0a512 512 0 1 0 1024 0 512 512 0 1 0-1024 0Z" p-id="3552"></path><path d="M684.8 262.4v64l-179.2 435.2H416l179.2-428.8h-256V262.4h345.6z" fill="#FFFFFF" p-id="3553"></path></svg><span>七天鑑賞</span></p>
                                <p class="ico-sub">商品鑑賞期七天內未拆封可免費退換</p>
                            </div>
                            <div class="icons">
                                <p class="ioc"><svg t="1740986070832" class="salesicon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="42986" width="200" height="200"><path d="M933.376 145.408l-24.576-0.512c-1.024 0-98.816-2.048-199.68-41.984-103.424-40.96-170.496-88.064-171.008-88.576L523.264 3.584c-3.072-2.56-7.168-3.584-11.264-3.584-4.096 0-8.192 1.536-11.264 3.584l-14.848 10.24c-1.024 0.512-67.584 47.616-170.496 88.576-100.864 39.936-198.656 41.984-199.68 41.984l-25.088 0.512c-10.752 0.512-18.944 8.192-18.944 18.432v414.208c0 217.088 286.208 446.464 440.32 446.464s440.32-229.376 440.32-445.952V163.84c0-10.24-8.704-18.432-18.944-18.432z m-189.952 265.728l-260.608 260.608c-15.872 15.872-41.984 15.872-57.856 0l-144.896-144.896c-15.872-15.872-15.872-41.984 0-57.856 8.192-8.192 18.432-11.776 29.184-11.776 10.24 0 20.992 4.096 29.184 11.776l115.712 115.712L686.08 352.768c15.872-15.872 41.984-15.872 57.856 0 15.36 16.384 15.36 42.496-0.512 58.368z" p-id="42987"></path></svg><span>安全訂購</span></p>
                                <p class="ico-sub">安全支付&隱密發貨，加密保護訂購訊息</p>
                            </div>
                        </div>
                    </div>

                    
                    
                </div>

                <div class="secret" data-track-block="m_pd_secret">
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
        </div>
    </div>

    <div class="row grey-line"></div>


    <div class="product-content" data-track-block="m_pd_desc">
        <h2>藥品說明</h2>
        <div class="detailed">
            <div class="present">
                @foreach($goods_instructions as $val)

                    <div class="ls">
                        <span class="s1">{{ array_get($val,'name') }}</span>
                        <span class="s2">{!! array_get($val,'value') !!}</span>
                    </div>
                @endforeach

            </div>
            <div class="img img-increase"><img src="/static/mobile/img/goods-2.jpg" /></div>
            <div class="img img-increase"><img src="/static/mobile/img/goods-3.jpg" /></div>
            <div class="img img-increase"><img src="/static/mobile/img/goods-4.jpg" /></div>
            <div class="img img-increase"><img src="/static/mobile/img/goods-5.jpg" /></div>
        </div>

    </div>
@endsection



