@extends('mobile.layout')

@section('track-init')
<script>Track.init({ platform: 'mobile', page_type: 'product_list' });</script>
@endsection

@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ release_asset('/static/mobile/css/product.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ release_asset('/static/mobile/less/product.css') }}"/>
    <style>
        .swiper-container {
            height: auto !important;
        }
    </style>
@stop

@section('script')
    @parent
    <script id="PRODUCT-P-1">
        $(window)["\u006f\u006e"]("llorcs".split("").reverse().join(""),function(){$("\u002e\u0070\u0072\u006f\u0064\u0075\u0063\u0074\u002d\u0062\u006f\u0078")["\u0065\u0061\u0063\u0068"](function(){var _0x5f3ad6=$(this)["\u006f\u0066\u0066\u0073\u0065\u0074"]()["\u0074\u006f\u0070"];var _0x48f9ab=$(window)["\u0068\u0065\u0069\u0067\u0068\u0074"]();var _0x1cfcf4=$(window)["\u0073\u0063\u0072\u006f\u006c\u006c\u0054\u006f\u0070"]();var _0x25cfaa=_0x48f9ab*0.4;if(_0x5f3ad6-_0x1cfcf4<=_0x25cfaa){$(this)["\u0066\u0069\u006e\u0064"]("txet-pmats. tfelegakcap. terces.".split("").reverse().join(""))["\u0061\u0064\u0064\u0043\u006c\u0061\u0073\u0073"]("wohs-txet-pmats".split("").reverse().join(""));}});});
    </script>
@stop


@section('content')

    <div class="row">
        <h1 style="font-size:16px;padding:12px 0 2px 15px;color:#333;">威而鋼訂購</h1>
        <div class="breadcrumb-box">
            <ul class="breadcrumb">
                <li><a href="#">首頁</a></li>
                <li class="active">在線訂購</li>
            </ul>
        </div>
    </div>
    <div class="row" style="border-left: 0.2rem solid #1C6AB4;padding-left: 0.2rem;">
        <h1 style="font-size: 0.4rem; font-weight: 500; margin: 0.05rem;">威而鋼在線訂購</h1>
        <p style="font-size: 0.28rem; color: #666;">原廠正品保證、100%隱密出貨、最高優惠65%</p>
    </div>
    <div class="row shop">
        <div class="product-row" data-track-block="m_plist_products">
            @foreach($product as $item)
                <div class="product-box clearfix" data-track-block="m_plist_product_item">
                    <div class="product-sec">
                        <div class="shop-img">
                            <a href="{{ url('goods/'.$item->id) }}"><img src="{{ asset_upload($item->m_img?:$item->img) }}" alt="{{ $item->name }}"></a>
                        </div>
                        <div class="shop-text">
                            <p class="main-title"><a href="{{ url('goods/'.$item->id) }}">{{ $item->name }}</a></p>
                            <div class="goods-label-sec">
                                <p class="goods-label">100mg/粒</p>
                                <p class="goods-label">一盒4粒</p>
                                <p class="goods-label">原廠正品</p>
                                <p class="goods-label">無效退款</p>
                                @if($item->quantity >= 4)
                                    <p class="goods-label">免運費</p>
                                    @else
                                    <p class="goods-label">4盒免運</p>
                                @endif
                            </div>
                            
                            <p class="sub-title">【成份】枸橼酸西地那非</p>
                            <p class="sub-title">【生產】美國輝瑞製藥有限公司</p>
                            <p class="sub-title">【有效期】60個月</p>
                            <p class="grey-price">NT$ {{ number_format(round($item->market_price)) }}</p>
                            <p class="red-price"><span class="twd">NT$</span>{{ number_format(round($item->price)) }}</p>
                            <div class="buy-button">
                                <a href="{{ url('shopping/'.$item->id) }}" data-track="m_plist_buy" data-track-zone="content" data-goods-id="{{ $item->id }}"><button type="button">立即訂購</button></a>
                            </div>
                        </div>
                    </div>
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
                                <p>包裹外觀<strong>絕不會提及</strong>威而鋼相關之任何字樣</p>
                            </div>
                            <div class="packagetext">
                                <p><svg t="1718693930066" class="righticon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="13167" width="200" height="200"><path  d="M512.1 512.4m-448 0a448 448 0 1 0 896 0 448 448 0 1 0-896 0Z" fill="#1C6AB4" p-id="13168"></path><path fill="#FFF" d="M445.9 723.9c-13.3 0-26-5.3-35.4-14.6L278.3 577c-19.5-19.5-19.5-51.2 0-70.7s51.2-19.5 70.7 0l96.9 96.9L675 374.1c19.5-19.5 51.2-19.5 70.7 0s19.5 51.2 0 70.7L481.2 709.3c-9.3 9.3-22 14.6-35.3 14.6z" p-id="13169"></path></svg>
                                <p>包裹送達超商後<strong>僅透過簡訊</strong>通知取貨，不會提及威而鋼相關之任何內容，<strong>絕不會致電</strong>打擾</p>
                            </div>
                            <div class="packagetext">
                                <p><svg t="1718693930066" class="righticon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="13167" width="200" height="200"><path  d="M512.1 512.4m-448 0a448 448 0 1 0 896 0 448 448 0 1 0-896 0Z" fill="#1C6AB4" p-id="13168"></path><path fill="#FFF" d="M445.9 723.9c-13.3 0-26-5.3-35.4-14.6L278.3 577c-19.5-19.5-19.5-51.2 0-70.7s51.2-19.5 70.7 0l96.9 96.9L675 374.1c19.5-19.5 51.2-19.5 70.7 0s19.5 51.2 0 70.7L481.2 709.3c-9.3 9.3-22 14.6-35.3 14.6z" p-id="13169"></path></svg>
                                <p>宅配人員及超商店員<strong>絕不會知道</strong>您所訂購的商品，請安心選購</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>



@endsection
