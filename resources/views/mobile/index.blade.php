@extends('mobile.layout')

@section('track-init')
<script>Track.init({ platform: 'mobile', page_type: 'home' });</script>
@endsection

@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ release_asset('/static/mobile/css/index.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ release_asset('/static/mobile/less/index.css') }}"/>

@stop

@section('script')
    @parent
    <script id="INDEX-M-1">
        $(window)["\u006f\u006e"]("llorcs".split("").reverse().join(""),function(){$("\u002e\u0070\u0072\u006f\u0064\u0075\u0063\u0074\u002d\u0062\u006f\u0078")["\u0065\u0061\u0063\u0068"](function(){var _0x2b2d3c=$(this)["\u006f\u0066\u0066\u0073\u0065\u0074"]()["\u0074\u006f\u0070"];var _0x22874f=$(window)["\u0068\u0065\u0069\u0067\u0068\u0074"]();var _0x43acfc=$(window)["\u0073\u0063\u0072\u006f\u006c\u006c\u0054\u006f\u0070"]();var _0x523382=_0x22874f*0.4;if(_0x2b2d3c-_0x43acfc<=_0x523382){$(this)["\u0066\u0069\u006e\u0064"]("txet-pmats. tfelegakcap. terces.".split("").reverse().join(""))["\u0061\u0064\u0064\u0043\u006c\u0061\u0073\u0073"]("\u0073\u0074\u0061\u006d\u0070\u002d\u0074\u0065\u0078\u0074\u002d\u0073\u0068\u006f\u0077");}});});
    </script>
@stop

@section('breadcrumb')@show
@section('content')

    <div class="row shop" id="point_product" data-track-block="m_home_shop_header">
        <div class="header-title clearfix">
            <div class="online clearfix">
                <div class="shopping">
                    <h1 class="p1" style="margin:0.05rem">威而鋼訂購</h1>
                    <p class="p2">SHOPPING ONLINE</p>
                </div>
            </div>
            <div class="online-right clearfix">
                <div class="go-button">
                    <a href="/product">前往商城<svg t="1718703755313" class="goicon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="13329" width="200" height="200"><path d="M464.52000031 121.68000031a67.15999969 67.15999969 0 0 1 94.95999938 0l342.84 342.84a67.15999969 67.15999969 0 0 1 0 94.95999938l-342.84 342.84a67.15999969 67.15999969 0 1 1-94.95999938-94.92L759.92 512l-295.39999969-295.39999969a67.12000031 67.12000031 0 0 1 0-94.92z" p-id="13330"></path><path d="M121.68000031 121.68000031a67.15999969 67.15999969 0 0 1 94.92 0l342.87999938 342.84a67.12000031 67.12000031 0 0 1 0 94.95999938l-342.87999938 342.84a67.15999969 67.15999969 0 0 1-94.92-94.92L417.03999969 512 121.68000031 216.60000031a67.15999969 67.15999969 0 0 1 0-94.92z" p-id="13331"></path></svg></a>
                </div>
            </div>
        </div>

        <div class="product-row" data-track-block="m_home_products">
            <div class="page-product">
                @foreach($products as $k=>$goods)
                    <div class="product-box clearfix">
                        <div class="product-sec">
                            <div class="shop-img">
                                <a href="{{ url('goods/'.$goods->id) }}"><img src="{{ asset_upload($goods->m_img?:$goods->img) }}" alt="{{ $goods->name }}"></a>
                            </div>
                            <div class="shop-text">
                                <h2 class="main-title"><a href="{{ url('goods/'.$goods->id) }}">{{ $goods->name }}</a></h2>
                                <div class="goods-label-sec">
                                    <p class="goods-label">100mg/粒</p>
                                    <p class="goods-label">一盒4粒</p>
                                    <p class="goods-label">原廠正品</p>
                                    <p class="goods-label">無效退款</p>
                                    @if($goods->quantity >= 4)
                                        <p class="goods-label">免運費</p>
                                        @else
                                        <p class="goods-label">4盒免運</p>
                                    @endif
                                </div>
                                
                                <p class="sub-title">【成份】枸橼酸西地那非</p>
                                <p class="sub-title">【生產】美國輝瑞製藥有限公司</p>
                                <p class="sub-title">【有效期】60個月</p>
                                <p class="grey-price">NT$ {{ number_format(round($goods->market_price)) }}</p>
                                <p class="red-price"><span class="twd">NT$</span>{{ number_format(round($goods->price)) }}</p>
                                <div class="buy-button">
                                    <a href="{{ url('shopping/'.$goods->id) }}" data-track="m_home_product_buy" data-track-zone="content" data-goods-id="{{ $goods->id }}"><button type="button">立即訂購</button></a>
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
            {{--<div class="page">
                <div class="leaf leaf-last"><a href="javascript:;">上一页</a></div>
                <div class="center">
                    <ul class="page-product-ul">
                        <li class="activat" data-page="1"><span></span></li>
                        <li data-page="2"><span></span></li>
                    </ul>
                </div>
                <div class="leaf leaf-next"><a href="javascript:;">下一页</a></div>
            </div>--}}

        </div>

    </div>


    <div class="row shop" data-track-block="m_home_about">
        <div class="header-title clearfix">
            <div class="online clearfix">
                <div class="shopping">
                    <p class="p1">威而鋼效果</p>
                    <p class="p2">DURG EFFICACY</p>
                </div>
            </div>

        </div>

        <div class="product-row durg-row">
            <div class="clearfix">
                <div class="left-row clearfix">
                    <div class="durg-img">
                        <img src="/static/mobile/img/g1.jpg" alt="提高勃起強度">
                    </div>
                    <div class="durg-text">
                        <p class="title">增強硬度</p>
                        <p class="desc"> 起效後，讓陰莖躍升4級硬度，一經刺激，堅如黃瓜！</p>
                    </div>
                    <div class="line">
                        <div class="line-top"></div>
                        <div class="line-top-x"></div>
                        <div class="line-bottom"></div>
                        <div class="line-bottom-x"></div>
                    </div>
                </div>

                <div class="right-row clearfix">
                    <div class="durg-text">
                        <p class="title">耐力持久</p>
                        <p class="desc"> 不論劑量是25、50、100mg，均能持續4小時，享受多次性愛！</p>
                    </div>
                    <div class="durg-img">
                        <img src="/static/mobile/img/g2.jpg" alt="加強勃起持久度">
                    </div>

                    <div class="line">

                        <div class="line-top"></div>
                        <div class="line-top-x"></div>
                        <div class="line-bottom"></div>
                        <div class="line-bottom-x"></div>

                    </div>
                </div>

                <div class="left-row clearfix">
                    <div class="durg-img">
                        <img src="/static/mobile/img/g3.jpg" alt="更堅挺">
                    </div>
                    <div class="durg-text">
                        <p class="title">全程堅挺</p>
                        <p class="desc">性愛期間「不漏氣、不洩氣」，保持堅硬挺拔直至完事。</p>
                    </div>
                    <div class="line">
                        <div class="line-top"></div>
                        <div class="line-top-x"></div>
                        <div class="line-bottom"></div>
                        <div class="line-bottom-x"></div>
                    </div>
                </div>

                <div class="right-row lengthen clearfix">
                    <div class="durg-text">
                        <p class="title">安全無副作用</p>
                        <p class="desc"> 美國FDA藥品安全認證，ED治療藥品行業的標杆牌。</p>
                    </div>
                    <div class="durg-img">
                        <img src="/static/mobile/img/g4.jpg" alt="安全、低副作用">
                    </div>

                    <div class="line">

                        <div class="line-top"></div>
                        <div class="line-top-x"></div>
                        <div class="line-bottom"></div>
                        <div class="line-bottom-x"></div>

                    </div>
                </div>

                <div class="left-row clearfix">
                    <div class="durg-img">
                        <img src="/static/mobile/img/g5.jpg" alt="應用廣泛">
                    </div>
                    <div class="durg-text">
                        <p class="title">應用廣泛</p>
                        <p class="desc">有效治療男性性功能勃起障礙、預防高山症、治療攝護腺肥大。</p>
                    </div>
                    <div class="line">
                        <div class="line-top"></div>
                        <div class="line-top-x"></div>
                        <div class="line-bottom"></div>
                        <div class="line-bottom-x"></div>
                    </div>
                </div>



            </div>

        </div>

    </div>


    <div class="row shop" data-track-block="m_home_effect_news">
        <div class="header-title clearfix">

            <div class="online-right clearfix">
                <div class="go-button">
                    <a href="/effect">更多藥品功效</a>
                </div>
            </div>
        </div>

        <div class="news-row">

            @foreach($effect as $v)
            <div class="news-box clearfix">

                <div class="new-img">
                    <a href="{{ url('effect/'.$v->id) }}.html"><img src="{{ asset_upload($v->img) }}" alt="{{ $v->title }}"></a>
                </div>

                <div class="new-text">
                    <p class="main-title"><a href="{{ url('effect/'.$v->id) }}.html">{{ $v->title }}</a></p>
                </div>
                <div class="new-button">
                    <a href="{{ url('effect/'.$v->id) }}.html">查看全文 ></a>
                </div>
            </div>
            @endforeach


        </div>

    </div>

    <div class="row shop" data-track-block="m_home_dosage">

        <div class="header-title clearfix">
            <div class="online clearfix">
                <div class="shopping">
                    <p class="p1">劑量與用法</p>
                    <p class="p2">DOSAGE AND USAGE</p>
                </div>
            </div>

        </div>

        <div class="dosage-row">

            <div class="swiper-container" id="dosage">
                <div class="swiper-wrapper">


                    <div class="swiper-slide">

                        <div class="jianyi">
                            <p class="txt">服用時間</p>
                            <div class="gradual-line"></div>
                        </div>

                        <div class="slide  yy-bg3">

                            <p>建議在性生活前30～60分鐘服用。服用案例證明服用威而鋼最快的起效時間為13分鐘，平均起效時間為36鐘，勃起成功率達87%。為了發揮更好的藥效，患者可以根據自身的年齡（年齡越大藥效吸收越慢）提高或者縮短用藥的時間。</p>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="jianyi">
                            <p class="txt">起效條件</p>
                            <div class="gradual-line"></div>
                        </div>

                        <div class="slide qx-bg">

                            <p>作用機理是藉由抑制PDE5，使PDE5對cGMP的特異破壞變緩慢，在人有性刺激時，陰莖海綿體中的NO釋放並啟動cGMPase讓cGMP的水準增高，海綿體內平滑肌將放鬆，流入血液增</p>
                        </div>

                    </div>

                    <div class="swiper-slide">

                        <div class="jianyi">
                            <p class="txt">服用劑量</p>
                            <div class="gradual-line"></div>
                        </div>

                        <div class="slide yy-bg2">

                            <p>根據用藥回饋，1、2級勃起硬度適合服用100mg，3級則適合服用50mg或25mg。 基於藥效和耐受性，劑量可增加至100mg（最大推薦劑量）或降低至25mg（最小推薦劑量）；每日（24小時內）最多服用1次。對於大多數患者而言，初次服用的推薦量為50mg。謹記，務必根據醫囑調整劑量。</p>
                        </div>
                    </div>

                    <div class="swiper-slide">

                        <div class="jianyi">
                            <p class="txt">用藥建議</p>
                            <div class="gradual-line"></div>
                        </div>

                        <div class="slide yy-bg1">

                            <p class="indent">（1）多數情況下第一次服用效果即佳，少部分人可能第一次
                                效果不佳，應繼續用藥，一般到第8次時達到最佳效果，
                                然後保持在該水平上。</p>
                            <p class="indent">（2）研究顯示，足夠的療效應保證至少6次用藥嘗試。</p>
                            <p class="indent">（3）接受威而鋼治療的ED患者，性交成功率隨著用藥次數增
                                加而上升，並在用藥8次（成功率達86%）後保持穩定。</p>
                            <p class="indent">（4）建議空腹服用，過於油膩的食物會影響吸收，延長藥物
                                發揮時間。且避免攝入酒精。</p>
                            <p class="indent">（5）本品用於治療男性勃起功能障礙，並不適合女性及兒童。</p>
                        </div>

                    </div>

                </div>
                <!-- Add Pagination -->
            </div>

            <div class="dosage-swiper-pagination"></div>

        </div>

    </div>

    <div class="row shop" data-track-block="m_home_sideeffect">

        <div class="header-title clearfix">
            <div class="online clearfix">
                <div class="shopping">
                    <p class="p1">副作用&不良反應</p>
                    <p class="p2">SIDE EFFECT & ADVERSE REACTIONSE</p>
                </div>
            </div>
        </div>

        <div class="dosage-row">
            <div class="jianyi">
                <p class="txt">副作用/不良反應佔比</p>
                <div class="gradual-line"></div>
            </div>
            <div class="side-effect">
                <div class="for-desc">
                    <ul>
                        <li><p>在臨床試驗中得知，威而鋼的副作用多數只是短暫性的，程度是可以預期且輕微的反應，尚未有需要更改治療方案的例子。因為威而鋼容易使血管擴張，增加陰莖的血供，所以過程就會產生一些反擴血管的副作用。包括：頭痛、臉色潮紅、消化不良、鼻塞等。此外約有百分之三的病人反應有視力方面的影響，特別是對顏色的感受有些異常。</p></li>
                        <li><p>如果發生少見且嚴重的副作用則應立即告知醫師。</p></li>
                        <li><p class="float-left">不良事件</p><p  class="float-right">報告不良事件的患者百分比</p></li>
                    </ul>

                </div>
                <div class="cans clearfix">
                    <p class="float-right"><span>威而鋼(N=734) </span><span>安慰劑(N=735)</span></p>
                </div>
                <div class="list">
                    <div class="list-item">
                        <span>頭痛</span>
                        <span>16%</span>
                        <span>4%</span>
                    </div>
                    <div class="list-item">
                        <span>潮紅</span>
                        <span>10%</span>
                        <span>1%</span>
                    </div>
                    <div class="list-item">
                        <span>消化不良</span>
                        <span>7%</span>
                        <span>2%</span>
                    </div>
                    <div class="list-item">
                        <span>鼻塞</span>
                        <span>4%</span>
                        <span>2%</span>
                    </div>
                    <div class="list-item">
                        <span>尿道感染</span>
                        <span>3%</span>
                        <span>2%</span>
                    </div>
                    <div class="list-item">
                        <span>視覺異常+</span>
                        <span>3%</span>
                        <span>0%</span>
                    </div>
                    <div class="list-item">
                        <span>腹瀉</span>
                        <span>3%</span>
                        <span>0%</span>
                    </div>
                    <div class="list-item">
                        <span>暈眩</span>
                        <span>2%</span>
                        <span>1%</span>
                    </div>
                    <div class="list-item">
                        <span>起疹</span>
                        <span>2%</span>
                        <span>1%</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="dosage-row" data-track-block="m_home_sideeffect_extra">
            <div class="jianyi">
                <p class="txt">未確定的情況</p>
                <div class="gradual-line"></div>
            </div>
            <div class="side-effect">
                <div class="for-desc">
                    <ul>
                        <li><p>

                                反應力降低、蕁麻疹、耳鳴、射精功能異常、噁心
                                、嘔吐、心絞痛、局部水腫、對光過敏、休克等情
                                況對照性臨床試驗中的不良反應的發生率低於2％
                                ，是否與VIAGRA有關連目前仍未確知。報告包含
                                與藥物的使用有合理關連性者，至於屬微事件或因
                                不確定而不具意義者則未列入其中。

                            </p></li>

                    </ul>

                </div>


            </div>

        </div>


    </div>

    <div class="row shop" data-track-block="m_home_article_list">
        <div class="header-title clearfix">

            <div class="online-right clearfix">
                <div class="go-button">
                    <a href="/sideeffect">更多藥品功效</a>
                </div>
            </div>
        </div>

        <div class="article-row">


            @foreach($side as $v)
            <div class="item clearfix">
                <div class="text">
                    <p><a href="{{ url('sideeffect/'.$v->id) }}.html">{{ $v->title }}</a></p>
                </div>

                <div class="article-img">
                    <a href="{{ url('sideeffect/'.$v->id) }}.html"><img src="{{ asset_upload($v->img) }}" alt="{{ $v->title }}"></a>
                </div>
            </div>
            @endforeach



        </div>

    </div>

    <div class="row shop" data-track-block="m_home_bottom_cta">

        <div class="header-title clearfix">
            <div class="online clearfix">
                <div class="shopping">
                    <p class="p1">常見問題</p>
                    <p class="p2">FREQUENTLY QUESTIONS</p>
                </div>
            </div>
            <div class="online-right clearfix">
                <div class="go-button">
                    <a href="/answer">更多常見問題</a>
                </div>
            </div>
        </div>

        <div class="question-row">

            @foreach($answer as $v)
                <div class="item clearfix">
                    <div class="title">
                        <p><a href="{{ url('answer/'.$v->id) }}.html">{{ $v->title }}</a></p>
                    </div>
                    <div class="text">
                        <p><a href="{{ url('answer/'.$v->id) }}.html">{{ $v->brief?\Illuminate\Support\Str::limit($v->brief,80):\Illuminate\Support\Str::limit(strip_tags($v->content),80) }}</a></p>
                    </div>
                    <div class="question-img">
                        <a href="{{ url('answer/'.$v->id) }}.html"><img src="{{ asset_upload($v->img) }}" alt="{{ $v->title }}"></a>
                    </div>
                    <div class="question-button">
                        <a href="{{ url('answer/'.$v->id) }}.html"><button>閱讀全文</button></a>
                    </div>
                </div>
            @endforeach

        </div>

    </div>




@endsection
