<!DOCTYPE html>
<html lang="zh-TW" class="{{ release_token() }}" data-dpr="1">
    <script>
        // Update data-dpr to actual device pixel ratio
        var dpr = window.devicePixelRatio || 1;
        document.documentElement.setAttribute('data-dpr', dpr);
    </script>
<head>
    <meta charset="utf-8">

    {{-- LCP 优化：关键资源预加载 --}}
    <link rel="preload" as="style" href="{{ release_asset('static/mobile/css/styles.css') }}">
    <link rel="preload" as="image" href="/static/mobile/img/logo.png">

    @if(isset($layout['seo']))
        <title>{{ isset($layout['seo'])?$layout['seo']->title:"" }}</title>
    @else
        @hasSection('title')
            <title>@yield('title')</title>
        @else
            <title>@yield('title-before')</title>
        @endif
    @endif

    @hasSection('keywords')
        <meta name="keywords" content="@yield('keywords')"/>
    @else
        <meta name="keywords" content="{{ isset($layout['seo'])?$layout['seo']->key_word:"" }}"/>
    @endif

    @hasSection('description')
        <meta name="description" content="@yield('description')"/>
    @else
        <meta name="description" content="{{ isset($layout['seo'])?$layout['seo']->description:"" }}"/>
    @endif
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">

    {{-- Open Graph 基础标签 --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ \App\Services\ConfigService::get('site_name') ?: '威而鋼正品購買平台' }}">
    <meta property="og:url" content="{{ config('app.m_url') }}/{{ trim(request()->path(),'/') }}">
    @hasSection('og-title')
        <meta property="og:title" content="@yield('og-title')">
    @else
        @hasSection('title')
            <meta property="og:title" content="@yield('title')">
        @endif
    @endif
    @hasSection('description')
        <meta property="og:description" content="@yield('description')">
    @else
        <meta property="og:description" content="{{ isset($layout['seo'])?$layout['seo']->description:"" }}">
    @endif
    @hasSection('og-image')
        <meta property="og:image" content="@yield('og-image')">
    @else
        <meta property="og:image" content="{{ config('app.url') }}/static/v2/img/logo.png">
    @endif
    <meta property="og:locale" content="zh_TW">

    {{-- Twitter Card 标签 --}}
    <meta name="twitter:card" content="summary_large_image">
    @hasSection('og-title')
        <meta name="twitter:title" content="@yield('og-title')">
    @else
        @hasSection('title')
            <meta name="twitter:title" content="@yield('title')">
        @endif
    @endif
    @hasSection('description')
        <meta name="twitter:description" content="@yield('description')">
    @else
        <meta name="twitter:description" content="{{ isset($layout['seo'])?$layout['seo']->description:"" }}">
    @endif
    @hasSection('og-image')
        <meta name="twitter:image" content="@yield('og-image')">
    @else
        <meta name="twitter:image" content="{{ config('app.url') }}/static/v2/img/logo.png">
    @endif

    {{-- CSS 加载 --}}
    <link rel="stylesheet" type="text/css" href="{{ release_asset('static/mobile/bootstrap/css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ release_asset('static/mobile/css/styles.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ release_asset('static/mobile/css/common.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ release_asset('static/mobile/less/global.css') }}"/>
    <link rel="stylesheet" href="{{ release_asset('static/mobile/font/iconfont.css') }}">
    <link rel="stylesheet" href="{{ release_asset('static/mobile/js/swiper/package/swiper-bundle.min.css') }}">

    {{-- CLS 优化：字体显示策略 --}}
    <style>
        @font-face {
            font-family: 'iconfont';
            src: url('/static/mobile/font/iconfont.woff2') format('woff2');
            font-display: swap;
        }

        .swiper-container {
            overflow: hidden;
        }
    </style>

    <link rel="canonical" href="{{ config('app.url') }}/{{ trim(request()->path(),'/') }}">
    <link rel="shortcut icon" href="{{ \App\Services\ConfigService::get('favicon')?asset('uploads/'.\App\Services\ConfigService::get('favicon')):'/favicon.ico' }}">
    @section('style')@show

    <script>
        var flash_data = '{!! session()->get('flash') !!}';
        if (flash_data) {
            flash_data = JSON.parse('{!! session()->get('flash') !!}');
        } else {
            flash_data = false;
        }
    </script>

    <script src="{{ release_asset('static/mobile/js/jquery.min.js') }}" type="text/javascript" charset="utf-8"></script>
    <script src="{{ release_asset('static/mobile/js/rem.js') }}" type="text/javascript" charset="utf-8"></script>
    <script src="{{ release_asset('static/mobile/js/menu.js') }}" type="text/javascript" charset="utf-8"></script>
    <script>
        ['gesturestart', 'gesturechange', 'gestureend'].forEach(function (evt) {
            document.addEventListener(evt, function (e) { e.preventDefault(); }, { passive: false });
        });
    </script>

    {{-- 全局 Schema：Organization + WebSite --}}
    <x-schema.organization />

    <style type="text/css">

        .dosage-swiper-pagination{
            margin-top: 0.6rem;
            text-align: center;
        }

        .swiper-pagination-bullet {
            height: 0.1rem;
            width: 0.3rem;
            display: inline-block;
            background: #E6E6E6;
            border-radius: 0.04rem;
            margin-right: 0.2rem;
            opacity:1;
        }

        .swiper-pagination-bullet-active {
            background: #1C6AB4;
        }

        .swal2-container.swal2-center>.swal2-popup{
            font-size: 0.3rem;
        }
        .swal2-styled.swal2-confirm{
            background-color:#3390E0!important;
        }
        
        
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="fixed-header" data-track-block="m_layout_header">
            <header class="@yield('header-class')">
                <div class="back" data-track="m_header_back" data-track-zone="header"><span class="iconfont icon-shangchuanicon-fuben"></span></div>
                <div class="logo"><a href="/" data-track="m_header_logo" data-track-zone="header"><img alt="logo" src="/static/mobile/img/logo.png" ></a></div>
                <div class="right-packing"></div>
            </header>
            <div class="nav clearfix">
                <div class="nav-item" style="width: 16%;"><a class="" href="/" data-track="m_header_home" data-track-zone="header">首頁</a></div>
                <div class="nav-item"><a class="av-nav" href="/product" data-track="m_header_product" data-track-zone="header">在線訂購</a></div>
                <div class="nav-item"><a class="" href="/effect" data-track="m_header_effect" data-track-zone="header">藥品功效</a></div>
                <div class="nav-item"><a class="" href="/sideeffect" data-track="m_header_sideeffect" data-track-zone="header">副作用</a></div>
                <div class="nav-item"><a class="" href="/answer" data-track="m_header_answer" data-track-zone="header">常見問題</a></div>
            </div>
            <div class="header-right-icon"><img src="/static/mobile/img/header-right.png" alt="隱秘"></div>
        </div>
        <div class="header-seize_seat"></div>
    </div>


    @section('banner')
        <div data-track-block="m_layout_banner">
            <x-mobile-banner></x-mobile-banner>
        </div>
    @show


    @yield('content')


    @section('footer-menu')
        <div class="footer-menu" data-track-block="m_layout_footer_menu">
            <div class="col-xs-3 menu-item">
                <a title="威而鋼首頁" href="/" data-track="m_footer_home" data-track-zone="footer">
                    <div class="icon-box">
                        <img alt="威而鋼首頁" class="home-icon-img" src="/static/mobile/img/home-icon.png" data-img="/static/mobile/img/home-icon.png" data-activate-img="/static/mobile/img/home-icon-activate.png">
                    </div>
                    <p>威而鋼首頁</p>
                </a>
            </div>
            <div  class="col-xs-3  menu-item">
                <a title="在線訂購"  href="/product" data-track="m_footer_product" data-track-zone="footer">
                    <div class="icon-box">
                        <img alt="在線訂購" class="buy-icon-img" src="/static/mobile/img/buy-icon.png" data-img="/static/mobile/img/buy-icon.png" data-activate-img="/static/mobile/img/buy-icon-activate.png" >
                    </div>
                    <p>在線訂購</p>
                </a>
            </div>
            <div class="col-xs-3  menu-item">
                <a title="訂單查詢"   href="/check" data-track="m_footer_check" data-track-zone="footer">
                    <div class="icon-box">
                        <img alt="訂單查詢" class="check-icon-img" src="/static/mobile/img/order-icon.png" data-img="/static/mobile/img/order-icon.png" data-activate-img="/static/mobile/img/order-icon-activate.png">
                    </div>
                    <p>訂單查詢</p>
                </a>
            </div>

            <div  class="col-xs-3  menu-item">
                <a title="聯絡我們"   href="/message" data-track="m_footer_message" data-track-zone="footer">
                    <div class="icon-box">
                        <img alt="聯絡我們" class="contact-icon-img" src="/static/mobile/img/contact-icon.png" data-img="/static/mobile/img/contact-icon.png" data-activate-img="/static/mobile/img/contact-icon-activate.png">
                    </div>
                    <p>聯絡我們</p>
                </a>
            </div>

        </div>
    @show

    <div class="copyright">Copyright 1998-{{ date('Y') }} All Rights Reserved 版權所有</div>

</div>



<script src="{{ release_asset('static/mobile/layer-v3.1.1/layer/layer.js') }}" type="text/javascript" charset="utf-8"></script>
<script src="{{ release_asset('static/mobile/js/iife.min.js') }}" type="text/javascript" charset="utf-8"></script>
<script src="{{ release_asset('static/mobile/js/swiper/package/swiper-bundle.min.js') }}" defer></script>
<script defer>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swiper !== 'undefined') {
            new Swiper('#dosage', {
                pagination: { el: '.dosage-swiper-pagination' },
            });
            new Swiper('#banner', {
                autoplay: { disableOnInteraction: false },
                loop: true,
            });
        }
    });
</script>
<script>

    function msg(msg,sub_msg,jump,skin){
        var tmp = "<div class=\"tips-msg\">\n" +
            "            <div class=\"tips-msg-box\">\n" +
            "                <p class=\"main "+skin+"\">"+msg+"</p>\n" +
            "                <p class=\"sub-main\">"+sub_msg+"</p>\n" +
            "            </div>\n" +
            "        </div>";
        layer.open({
            type: 1,
            title: false,
            closeBtn: 0,
            shadeClose: true,
            time:2000,
            content: tmp,
            end : function() {
                if(jump){
                    window.location.href=jump;
                }
            }
        });
    }

    function mmsg(msg,sub_msg,jump,skin){
        var tmp = "<div class=\"tips-msg\">\n" +
            "            <div class=\"tips-msg-box\">\n" +
            "                <p class=\"main "+skin+"\">"+msg+"</p>\n" +
            "                <p class=\"sub-main\">"+sub_msg+"</p>\n" +
            "            </div>\n" +
            "        </div>";
        layer.open({
            type: 1,
            title: false,
            closeBtn: 0,
            shadeClose: true,
            time:2,
            content: tmp,
            end : function() {
                if(jump){
                    window.location.href=jump;
                }
            }
        });
    }

</script>

<script>
    var flash_data = '{!! session()->get('flash') !!}';
    if(flash_data){
        flash_data = JSON.parse('{!! session()->get('flash') !!}');
    }else{
        flash_data = false;
    }
</script>

@section('script')@show
@include('partials.analytics-scripts', ['trackPlatform' => 'mobile'])
</body>

</html>




