<!DOCTYPE html>
<html lang="zh-TW">
    <head>
        <meta charset="utf-8">
        @if(isset($mate))
            <title>{{ $mate->title }}</title>
            <meta name="keywords" content="{{ $mate->key_word }}"/>
            <meta name="description" content="{{ $mate->description }}"/>
        @else
            <title>@yield('title')</title>
            <meta name="keywords" content="@yield('keywords')"/>
            <meta name="description" content="@yield('description')"/>
        @endif
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="format-detection" content="telephone=no">
        @if (View::hasSection('landing_style'))
            @yield('landing_style')
            @vite(['resources/scss/landing.scss', 'resources/js/app.js'])
        @elseif (request()->is('/'))
            @vite(['resources/scss/home.scss', 'resources/js/app.js'])
            {{-- 首頁 header 首屏：避免等 JS 才變白字／誤觸白底動畫 --}}
            <style>
                .page-home .main-header { --header-solid: 0; }
                .page-home .main-header .nav .web-nav > .nav-item,
                .page-home .main-header .nav .web-nav > .nav-item a { color: #fff; }
                .page-home .main-header .nav .menu-btn .bar { background-color: #fff; }
                body.mobile-nav-open.page-home .main-header .nav .menu-btn .bar { background-color: rgba(0, 0, 0, 0.85); }
            </style>
        @else
            @vite(['resources/scss/app.scss', 'resources/js/app.js'])
        @endif
        <link rel="canonical" href="{{ config('app.url') }}/{{ trim(request()->path(),'/') }}">
        <link rel="shortcut icon" href="/favicon.ico">
        @php
            $breadcrumbNameMap = [
                'product' => '犀利士線上訂購',
                'promise' => '訂購指南',
                'effect' => '使用心得',
                'health' => '兩性健康',
                'check' => '訂單查詢',
                'message' => '線上客服',
                'news' => '健康專欄',
                'goods' => '商品詳情',
                'shopping' => '購物車',
                'page' => '頁面',
            ];

            $segments = request()->segments();
            $baseUrl = rtrim(config('app.url') ?: url('/'), '/');
            $breadcrumbItems = [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => '首頁',
                    'item' => $baseUrl ?: url('/'),
                ],
            ];

            $currentPath = '';
            foreach ($segments as $segment) {
                $currentPath .= '/' . $segment;
                $isNumericSegment = is_numeric($segment);
                $name = $isNumericSegment
                    ? '明細'
                    : ($breadcrumbNameMap[$segment] ?? str_replace(['-', '_'], ' ', $segment));

                $breadcrumbItems[] = [
                    '@type' => 'ListItem',
                    'position' => count($breadcrumbItems) + 1,
                    'name' => $name,
                    'item' => $baseUrl . $currentPath,
                ];
            }

            $breadcrumbSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $breadcrumbItems,
            ];
        @endphp
        <script type="application/ld+json">
            {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>

        @section('style')@show
        @section('script')
            <script src="/static/mobile/js/jquery.min.js" type="text/javascript" charset="utf-8"></script>

            <script>
                window.flash = @json(session('flash', null));
                document.addEventListener('DOMContentLoaded', () => {
                    if (window.flash) {
                        const { status, message, redirect, data } = window.flash;
                        if (status === 'success') {
                            Swal.fire({ icon: 'success', text: message, timer: 1500, showConfirmButton: false });
                        } else if (status === 'error') {
                            Swal.fire({ icon: 'error', text: message, timer: 2000, showConfirmButton: false });
                        } else {
                            Swal.fire({ icon: 'info', text: message });
                        }
                    }
                });
            </script>
            <script src="/static/mobile/js/iife.min.js" type="text/javascript" charset="utf-8"></script>
            {!! function_exists('request_log_script') ? request_log_script() : '' !!}

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // 文字逐字包 span
                    var el = document.querySelector('.go-shop-btn');
                    if (el) {
                        var text = el.textContent.trim();
                        if (text) {
                            el.innerHTML = text.split('').map(function (ch) {
                                return '<span>' + ch + '</span>';
                            }).join('');
                        }
                    }

                    // 移動端導航展開/收合
                    var btn = document.querySelector('.menu-btn');
                    var nav = document.querySelector('.mobile-nav');
                    if (btn && nav) {
                        function open() {
                            nav.classList.add('is-open');
                            nav.setAttribute('aria-hidden', 'false');
                            btn.classList.add('close-menu');
                            btn.setAttribute('aria-expanded', 'true');
                            document.body.classList.add('mobile-nav-open');
                        }
                        function close() {
                            nav.classList.remove('is-open');
                            nav.setAttribute('aria-hidden', 'true');
                            btn.classList.remove('close-menu');
                            btn.setAttribute('aria-expanded', 'false');
                            document.body.classList.remove('mobile-nav-open');
                        }
                        function toggle() {
                            nav.classList.contains('is-open') ? close() : open();
                        }
                        btn.addEventListener('click', toggle);
                        nav.querySelectorAll('.mobile-nav__link').forEach(function(a) {
                            a.addEventListener('click', close);
                        });
                        nav.addEventListener('click', function(e) {
                            if (e.target === nav) close();
                        });
                        nav.setAttribute('aria-hidden', 'true');
                        btn.setAttribute('aria-expanded', 'false');
                    }

                    // 捲動超過 100px 時給 main-header 加 .shadow
                    var header = document.querySelector('.main-header');
                    if (header) {
                        function updateHeaderShadow() {
                            if (window.scrollY > 100) {
                                header.classList.add('shadow');
                            } else {
                                header.classList.remove('shadow');
                            }
                        }
                        updateHeaderShadow();
                        window.addEventListener('scroll', updateHeaderShadow);
                    }
                });
            </script>

            {{-- Lenis disabled for testing
            <script>
                if (window.innerWidth > 1024) {
                const script = document.createElement('script');
                script.src = "https://unpkg.com/lenis@1.1.14/dist/lenis.min.js";
                script.onload = function() {
                    const lenis = new Lenis({
                    duration: 1.4
                    });

                    function raf(time) {
                    lenis.raf(time);
                    requestAnimationFrame(raf);
                    }
                    requestAnimationFrame(raf);
                };
                document.head.appendChild(script);
                }
            </script>
            --}}
        @endsection
        @stack('schema')
    </head>
    <body
        class="@yield('body-class')"
        @if(View::hasSection('body-data-page'))
            data-page="@yield('body-data-page')"
        @endif
    >

        <header class="main-header{{ request()->is('/') ? ' main-header--home-scroll' : '' }}">
            @if(!request()->is('/') &&!request()->is('goods*'))
                <div class="icon-packing">
                    <div class="pack-icon">
                        <svg class="packicon" viewBox="0 0 1024 1024"><use href="#icon-packicon-1"></use></svg>
                        原廠正品
                    </div>
                    <div class="pack-icon">
                        <svg class="packicon" viewBox="0 0 1024 1024"><use href="#icon-packicon-2"></use></svg>
                        隱密包裝
                    </div>
                    <div class="pack-icon">
                        <svg class="packicon" viewBox="0 0 1024 1024"><use href="#icon-packicon-3"></use></svg>
                        @if ($period === 'morning')
                            當天出貨
                        @else
                            現貨供應
                        @endif
                    </div>
                    <div class="pack-icon">
                        <svg class="packicon" viewBox="0 0 1024 1024"><use href="#icon-packicon-4"></use></svg>
                        安心訂購
                    </div>
                </div>
            @endif

            <nav class="nav">
                <a class="logo" href="/" itemprop="url">
                    <img src="/static/img/{{ request()->is('/') ? 'C_white.svg' : 'C_black.svg' }}" alt="禮來犀利士 Cialis®">
                </a>
                @if(!request()->is('shopping/*'))
                    <ul class="web-nav">
                        <li class="nav-item"><a href="/">首頁</a></li>
                        <li class="nav-item"><a href="/sideeffects">犀利士副作用與禁忌</a></li>
                        <li class="nav-item"><a href="/effect">犀利士使用心得</a></li>
                        <li class="nav-item"><a href="/health">兩性健康</a></li>
                    </ul>
                @endif
                @if(!request()->is('shopping/*') &&!request()->is('check/*') &&!request()->is('order/*'))
                    <a class="main-btn go-shop-btn {{ (request()->is('/') || request()->is('product*') || request()->is('goods*')) ? '' : 'noindex' }}" href="/product">
                        犀利士線上訂購
                    </a>
                    <button class="menu-btn" type="button" aria-label="Menu toggle">
                        <span class="bar bar--1"></span>
                        <span class="bar bar--2"></span>
                        <span class="bar bar--3"></span>
                    </button>
                @else
                    <div class="pro">
                        <svg class="pro-icon" viewBox="0 0 1024 1024"><use href="#icon-safeicon"></use></svg>
                        <div class="pro-running"></div>
                        <span class="pro-text">SSL加密保護中</span>
                    </div>
                @endif
            </nav>

            <nav class="mobile-nav" aria-label="主選單">
                <ul class="mobile-nav__list" role="list">
                    <li class="mobile-nav__item"><a class="mobile-nav__link" href="/">首頁<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
                    <li class="mobile-nav__item"><a class="mobile-nav__link main-btn" href="/product">犀利士線上訂購<span>最高享受65%優惠</span></a></li>
                    <li class="mobile-nav__item"><a class="mobile-nav__link" href="/sideeffects">犀利士副作用與禁忌<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
                    <li class="mobile-nav__item"><a class="mobile-nav__link" href="/effect">犀利士使用心得<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
                    <li class="mobile-nav__item"><a class="mobile-nav__link" href="/health">兩性健康<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
                    <li class="mobile-nav__item"><a class="mobile-nav__link" href="/check">訂單查詢<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
                    <li class="mobile-nav__item"><a class="mobile-nav__link" href="/message">線上客服<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
                </ul>
            </nav>
        </header>
        <main
            class="main-content"
            style="--page-banner-bg: url('{{ $page_banner_bg }}')"
        >
            @yield('content')

            @if(!request()->is('check/*') &&!request()->is('order/*') &&!request()->is('shopping/*'))
                @include('components.update-box')
            @endif

        </main>

        <footer class="main-footer">
            <div class="footer-logo">
                <a class="logo" href="/">
                    <img src="/static/img/C_black.svg" decoding="async" loading="lazy" alt="禮來犀利士 Cialis®">
                </a>
                <p class="site-name">禮來犀利士<sup>®</sup>（台灣）</p>
            </div>
            @include('components.core-sec')
            <div class="trust-box">
                <img class="trust-icon" src="/static/img/original.svg" decoding="async" loading="lazy" alt="原裝進口">
                <img class="trust-icon" src="/static/img/FDA.svg" decoding="async" loading="lazy" alt="FDA">
                <img class="trust-icon" src="/static/img/EMA.svg" decoding="async" loading="lazy" alt="EMA">
            </div>
            <nav class="footer-link-sec">
                <h4 class="footer-section-title">網站導航</h4>
                <ul class="footer-links">
                    <li class="footer-link"><a href="/">首頁</a></li>
                    <li class="footer-link"><a href="/product">犀利士線上訂購</a></li>
                </ul>
                <h4 class="footer-section-title">健康資訊</h4>
                <ul class="footer-links">
                    <li class="footer-link"><a href="/sideeffects">犀利士副作用與禁忌</a></li>
                    <li class="footer-link"><a href="/effect">犀利士使用心得</a></li>
                    <li class="footer-link"><a href="/health">兩性健康</a></li>
                </ul>
                <h4 class="footer-section-title">訂單服務</h4>
                <ul class="footer-links">
                    <li class="footer-link"><a href="/check">訂單查詢</a></li>
                    <li class="footer-link"><a href="/message">線上客服</a></li>
                </ul>
            </nav>
            <div class="footer-copyright">
                <p class="footer-copyright-text">本平台經美國禮來制藥廠官方授權，提供原廠正品犀利士Cialis，注重品質、效率、誠信，仿冒必究！</p>
                <p class="footer-copyright-text">犀利士為處方藥，請在醫師指導下使用</p>
                <p class="footer-copyright-text">訂購犀利士前，需確認您已年滿當地法定成年年齡。</p>
                <p class="footer-copyright-text">詳細資訊請參閱<a href="/promise">[我們承諾]</a></p>
                <p class="footer-copyright-text">All Rights Reserved.© 2010-2026<br>禮來犀利士Cialis（台灣） 版權所有</p>
            </div>
        </footer>
        @yield('script')
        @stack('update-box')
        @stack('rice-scroll')
        @stack('tick-scroll')
        @stack('qa-js')
        @include('web.svg-sprite')
    </body>
</html>
