@extends('web.layout')
@section('title',$product->name)
@section('style')
    @parent
@stop

@section('script')
    @parent
    <script src="{{ assetv('static/js/price-animator.js') }}"></script>

    <script>
        // 滚动监听
        $(window).on('scroll', function() {
            var isDesktop = window.innerWidth >= 1024;

            if(!isDesktop && $(window).scrollTop() >= 300) {
                $('.footer-buy').addClass('show');
            } else {
                $('.footer-buy').removeClass('show');
            }

            if (isDesktop && $(window).scrollTop() >= 1000) {
                $('.footer-buy').addClass('pc-show');
            } else {
                $('.footer-buy').removeClass('pc-show');
            }

            if($(window).scrollTop() >= 150) {
                $('.stamp-text').addClass('stamp-text-show');
            }
        });
    </script>

    <script>
        // 倒计时功能 - 只在元素存在时运行
        function updateCountdown() {
            const countdownElement = document.getElementById('targetTimestamp');
            
            // 如果元素不存在，不执行
            if (!countdownElement) {
                return;
            }

            var today = new Date();
            today.setHours(17, 0, 0, 0);
            var targetTimestamp = today.getTime();
            const currentTimestamp = new Date().getTime();
            let remainingTime = targetTimestamp - currentTimestamp;

            // 如果已经过了17:00，指向明天的17:00
            if (remainingTime <= 0) {
                today.setDate(today.getDate() + 1);
                targetTimestamp = today.getTime();
                remainingTime = targetTimestamp - currentTimestamp;
            }

            // 计算时、分、秒、毫秒
            const hours = String(Math.floor((remainingTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
            const minutes = String(Math.floor((remainingTime % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
            const seconds = String(Math.floor((remainingTime % (1000 * 60)) / 1000)).padStart(2, '0');
            // 取毫秒的最后一位（0-9）
            const milliseconds = String(Math.floor(remainingTime % 100)).slice(-1);

            countdownElement.innerHTML = `${hours}:${minutes}:${seconds}:${milliseconds}`;

            // 每10毫秒更新一次，实现实时倒计时
            setTimeout(updateCountdown, 10);
        }

        // 页面加载完成后启动倒计时
        $(document).ready(function() {
            updateCountdown();
        });
    </script>
    <script>
        $(document).ready(function() {
            // 初始化已选中的选项样式
            $('input[type="radio"][name^="iief_q"]:checked').each(function() {
                $(this).closest('.iief-option').addClass('selected');
            });
            
            // 计算总分
            function calculateTotalScore() {
                var totalScore = 0;
                var allAnswered = true;
                
                // 遍历所有5个问题
                for (var i = 1; i <= 5; i++) {
                    var selected = $('input[name="iief_q' + i + '"]:checked');
                    if (selected.length > 0) {
                        totalScore += parseInt(selected.data('score'));
                    } else {
                        allAnswered = false;
                    }
                }
                
                // 移除所有卡片的高亮
                $('.iief-card').removeClass('active');
                
                // 如果所有问题都已回答，显示结果并高亮对应卡片
                if (allAnswered) {
                    $('#iiefTotalScore').text(totalScore);
                    $('#iiefResult').slideDown(300);
                    
                    // 根据分数高亮对应的卡片
                    if (totalScore >= 21) {
                        $('#iiefCard21').addClass('active');
                    } else if (totalScore >= 12) {
                        $('#iiefCard12').addClass('active');
                    } else if (totalScore >= 8) {
                        $('#iiefCard8').addClass('active');
                    } else {
                        $('#iiefCard5').addClass('active');
                    }
                } else {
                    $('#iiefResult').slideUp(300);
                }
            }
            
            // 监听所有单选按钮的变化
            $('input[type="radio"][name^="iief_q"]').on('change', function() {
                // 移除同组其他选项的选中样式
                $(this).closest('.iief-options').find('.iief-option').removeClass('selected');
                // 添加当前选项的选中样式
                $(this).closest('.iief-option').addClass('selected');
                calculateTotalScore();
            });
            
            // 为选项添加点击效果
            $('.iief-option').on('click', function(e) {
                // 如果点击的不是input本身，触发input的点击
                if (!$(e.target).is('input')) {
                    $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
                }
            });
            
            // 初始计算（如果有已选中的选项）
            calculateTotalScore();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bgBox = document.getElementById('bg-box');
            if (!bgBox) return;
            
            let bgImages = JSON.parse(bgBox.dataset.bgImages || '[]');
            if (!bgImages || bgImages.length === 0) return;
            
            // 随机打乱数组顺序（Fisher-Yates 洗牌算法）
            function shuffleArray(array) {
                const shuffled = [...array]; // 创建副本，避免修改原数组
                for (let i = shuffled.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
                }
                return shuffled;
            }

            // 首图先随机固定一张，页面加载后其余图片再随机轮播
            const firstBgIndex = Math.floor(Math.random() * bgImages.length);
            const firstBgImage = bgImages[firstBgIndex];
            const remainingBgImages = bgImages.filter((_, index) => index !== firstBgIndex);
            bgImages = [firstBgImage, ...shuffleArray(remainingBgImages)];
            
            let currentBgIndex = 0;
            let intervalId = null;
            let isLayerModeEnabled = false;
            
            function assetPath(relativePath) {
                // 路径已经在控制器中处理好了，直接返回
                return relativePath;
            }
            
            function createBgLayer(index) {
                const layer = document.createElement('div');
                layer.className = 'bg-layer';
                layer.dataset.index = index;
                layer.style.backgroundImage = `url(${assetPath(bgImages[index])})`;
                layer.style.backgroundSize = 'cover';
                layer.style.backgroundPosition = 'top';
                layer.style.backgroundRepeat = 'no-repeat';
                return layer;
            }

            function enableLayerMode() {
                if (isLayerModeEnabled) return;
                bgBox.classList.add('has-bg-layers');
                isLayerModeEnabled = true;
            }

            function applyFallbackBackground(index) {
                const url = assetPath(bgImages[index]);
                bgBox.style.backgroundImage = `url(${url})`;
                bgBox.style.backgroundSize = 'cover';
                bgBox.style.backgroundPosition = 'top';
                bgBox.style.backgroundRepeat = 'no-repeat';
            }

            function ensureFirstBgLoaded() {
                const firstUrl = assetPath(bgImages[0]);

                // 先用元素本身背景兜底，避免首屏出现无图空窗
                applyFallbackBackground(0);

                const preloader = new Image();
                preloader.decoding = 'async';
                preloader.src = firstUrl;

                preloader.onload = () => {
                    const firstLayer = createBgLayer(0);
                    firstLayer.style.opacity = '1';
                    bgBox.appendChild(firstLayer);
                    enableLayerMode();
                };
            }
            
            function showBgImage(index) {
                const existing = bgBox.querySelectorAll('.bg-layer');
                
                // 查找当前索引的层
                let current = Array.from(existing).find(layer => 
                    parseInt(layer.dataset.index) === index
                );
                
                // 如果当前索引的层不存在，创建它
                if (!current) {
                    current = createBgLayer(index);
                    bgBox.appendChild(current);
                }
                
                // 隐藏所有层
                existing.forEach(layer => {
                    layer.style.opacity = '0';
                });
                
                // 显示当前层
                current.style.opacity = '1';
            }
            
            function startBgSlideshow() {
                if (intervalId) return;
                intervalId = setInterval(() => {
                    currentBgIndex = (currentBgIndex + 1) % bgImages.length;
                    showBgImage(currentBgIndex);
                }, 8000);
            }
            
            function stopBgSlideshow() {
                if (intervalId) {
                    clearInterval(intervalId);
                    intervalId = null;
                }
            }
            
            const bgObserver = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) {
                    startBgSlideshow();
                } else {
                    stopBgSlideshow();
                }
            }, {
                threshold: 0.8
            });

            // 先显示 fallback 背景，首图加载完成后再切换到层模式
            ensureFirstBgLoaded();
            
            bgObserver.observe(bgBox);
        });
    </script>

    <script>
        (() => {
            const observeItems = document.querySelectorAll(
                '.step-content-item, .center-banner .mon, .center-banner svg.line'
            );
            if (!observeItems.length) return;

            const processed = new WeakSet();

            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    const el = entry.target;
                    if (processed.has(el)) return;

                    if (el.classList.contains('step-content-item') || el.classList.contains('mon')) {
                        el.classList.add('now');
                    }

                    if (el.tagName.toLowerCase() === 'svg' && el.classList.contains('line')) {
                        const lineDraw = el.querySelector('#lineDraw');
                        const fillFade = el.querySelector('#fillFade');
                        const dotMove  = el.querySelector('#dotMove');

                        if (lineDraw) lineDraw.beginElement();
                        if (fillFade) fillFade.beginElement();
                        if (dotMove)  dotMove.beginElement();

                        el.querySelectorAll('.line-dot2 animate, .line-dot3 animate')
                            .forEach(anim => anim.beginElement());

                        const decorationCircle = el.parentElement?.querySelector('.decoration-circle');
                        if (decorationCircle) decorationCircle.classList.add('now');
                    }

                    processed.add(el);
                });
            }, {
                root: null,
                threshold: 0.15
            });

            observeItems.forEach(el => observer.observe(el));
        })();
    </script>
@stop

@section('body-class', 'page-goods-info')
@section('content')
    @include('components.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item"><a href="' . url('product') . '">訂購犀利士</a></li><li class="breadcrumb__item">犀利士 20mg ' . $product->quantity . '盒</li>'])

    <section class="goods-info-card">
        <h2 class="goods-title">禮來犀利士Cialis 20mg<strong class="box-count">{{ $product->name }}<span class="box-num">{{ $product->quantity }}</span>盒</strong></h2>
        <div class="price-box" data-market-price="{{ round($product->market_price) }}" data-price="{{ round($product->price) }}">
            <div class="mk-price">
                <p class="grey-price">NT$ {{ number_format(round($product->market_price)) }}</p>
                <div class="discount-box">
                    <p class="discount">-<span class="descount-num">{{ $product->discount_percent }}</span>%</p>
                </div>
            </div>
            <div class="red-price-box">
                <p class="red-price"><span class="twd">NT$</span><span class="price-number">{{ number_format(round($product->market_price)) }}</span></p>
                @if($product->quantity >= 4)
                    <p class="free">免運費</p>
                @endif 
            </div>
        </div>
        <div class="goods-label-sec">
            <p class="goods-label">100%隱密包裝</p>
            <p class="goods-label box-buyer-count" data-box-count="{{ $product->quantity }}">近24小時已有0人訂購 {{ $product->quantity }}盒@if($product->quantity < 3)裝@elseif($product->quantity >= 3 && $product->quantity < 10)方案@else組合@endif</p>
        </div>
        {{--<p class="goods-subname">"{{ $product->subname }}"</p>--}}
        <dl class="sub-sec">
            <dt class="sub-title">活性成份</dt>
            <dd class="sub-content">Sildenafil</dd>
            <dt class="sub-title">數量規格</dt>
            <dd class="sub-content">4顆/盒</dd>
            <dt class="sub-title">原裝進口</dt>
            <dd class="sub-content">原廠標籤</dd>
            <dt class="sub-title">保質期限</dt>
            <dd class="sub-content">60個月</dd>
        </dl>
        {{--<div class="indication-sec">
            <h3 class="indication-title">適用於：</h3>
            <ul class="indication-box">
                <li class="indication-item">
                    <span class="tick"><svg class="tickicon" viewBox="0 0 1024 1024"><use href="#icon-tickicon"></use></svg></span>
                    <p class="indication-text">有效改善勃起功能障礙</p>
                </li>
                <li class="indication-item">
                    <span class="tick"><svg class="tickicon" viewBox="0 0 1024 1024"><use href="#icon-tickicon"></use></svg></span>
                    <p class="indication-text">提升勃起硬度 / 穩定維持 / 延長時間 / 改善早洩</p>
                </li>
                <li class="indication-item">
                    <span class="tick"><svg class="tickicon" viewBox="0 0 1024 1024"><use href="#icon-tickicon"></use></svg></span>
                    <p class="indication-text">降低肺動脈高壓、預防高山肺水腫</p>
                </li>
            </ul>
        </div>--}}
        <ul class="tags">
            @foreach($goods->label_tags as $label)
                <li class="tag-item">
                    <span class="tick"><svg class="tickicon" viewBox="0 0 1024 1024"><use href="#icon-tickicon"></use></svg></span>
                    <p class="tag-text">{{ $label }}</p>
                </li>
            @endforeach
        </ul>
        
        <ul class="ensures">
            <li class="icons">
                <svg class="salesicon" viewBox="0 0 1024 1024"><use href="#icon-salesicon-daily"></use></svg>
                <span class="ioc-l">每日出貨</span>
                @if ($period === 'morning')
                    <span class="ioc-r">17:00前下單，當天寄出<br>預計明天&nbsp;{{ date('n月j日',strtotime('+1 day')) }}～{{ date('n月j日',strtotime('+2 day')) }}&nbsp;送達</span>
                @else
                    <span class="ioc-r">今天 17:00 前訂單已全部寄出<br>現在下單，明天優先寄出<br>預計後天&nbsp;{{ date('n月j日',strtotime('+2 day')) }}～{{ date('n月j日',strtotime('+3 day')) }}&nbsp;送達</span>
                @endif

                @if($showCountdown)
                    <p class="timeout">即將截單出貨<span id="targetTimestamp" class="countdown">02:00:00:0</span></p>
                @endif
            </li>
            <li class="icons">
                <svg class="salesicon" viewBox="0 0 1024 1024"><use href="#icon-salesicon-return"></use></svg><span class="ioc-l">鑑賞期內未拆封可無憂退貨 · 包裹破損免費退換</span>
            </li>
            <li class="icons">
                <svg class="salesicon" viewBox="0 0 1024 1024"><use href="#icon-salesicon-safe"></use></svg><span class="ioc-l">安全支付 · 隱密包裝 · 訂購資訊加密 · 安心訂購</span>
            </li>
        </ul>
        <p class="pro-tips">作為全球信賴的勃起功能障礙長效治療藥物，<strong>禮來犀利士Cialis</strong>的藥效穩定性與安全性經嚴格控管。為了確保您獲得的是 100% 原廠正品，建議通過 <a href="/" class="inner-link" title="犀利士Cialis台灣正品通路">犀利士正品通路</a> 進行訂購。
        </p>
        <p class="pro-tips">本站所有產品均有原廠防偽標籤，並提供隱密包裝與快速配送服務。若您想瞭解更多犀利士資訊，可查閱下方詳細的藥品訊息。</p>
        <a class="main-btn" href="{{ url('shopping/'.$product->id) }}">立即訂購<svg class="btn-icon buy-icon" viewBox="0 0 1055 1024"><use href="#icon-buyicon"></use></svg>
            @if($product->quantity >= 4)
                <div class="discount">
                    <span class="discount-content">還有免運哦</span>
                </div>
            @endif 
        </a>
    </section>
    <div class="product-album">
        <span class="original-label" aria-label="原裝進口">原裝進口</span>
        <img class="goods-img" src="{{ storage_url($product->m_img?:$product->img) }}" loading="auto" decoding="async" width="380" height="260" alt="{{ $product->name }}">
        <div class="bg-box" id="bg-box" data-bg-images="{{ json_encode($goods_images ?? []) }}">
        </div>
    </div>
    @include('components.secret')
    <section class="detailed">
        <h2 class="sec-title">藥品訊息</h2>
        <dl class="present">
            @foreach(get_setting('goods_instructions')->toArray() as $val)
                <dt class="s1">{{ data_get($val,'name') }}</dt>
                <dd class="s2">{!! nl2br(e(data_get($val, 'value'))) !!}</dd>
            @endforeach
        </dl>
    </section>
    @php
        $pageIndexSprite = asset('static/svg/page-index.svg');
    @endphp
    <div class="center-banner">
        <div class="center-banner-content">
            <p class="banner-desc mon">*數據統計回饋</p>
            <p class="banner-desc mon">大多數男士隨犀利士使用經驗增加</p>
            {{--<p class="banner-desc highlight-line1 mon">對性生活的焦慮感降低
                <strong class="highlight">
                    <span class="highlight-number">85</span><span class="highlight-percent">%</span>
                </strong>
                <svg class="downbg-icon"><use href="{{ $pageIndexSprite }}#icon-godown"/></svg>
            </p>--}}
            <p class="banner-desc highlight-line2 mon">自信心大幅提升
            {{--<strong class="highlight">
                    <span class="highlight-number">200</span><span class="highlight-percent">%</span>
                </strong>--}}
                <svg class="downbg-icon"><use href="{{ $pageIndexSprite }}#icon-godown"/></svg>
            </p>
        </div>
        <div class="line-wrap" aria-hidden="true">
            <svg class="line" viewBox="0 0 300 200" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="aurora-gradient-v2" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" class="aurora-stop-start"/>
                        <stop offset="100%" class="aurora-stop-end"/>
                    </linearGradient>

                    <filter id="dot-glow" x="-200%" y="-200%" width="400%" height="400%">
                        <feGaussianBlur stdDeviation="2" result="blur"/>
                        <feMerge>
                            <feMergeNode in="blur"/>
                            <feMergeNode in="SourceGraphic"/>
                        </feMerge>
                    </filter>
                </defs>


                <path
                    d="M0,190 C90,185 220,140 300,0"
                    class="inner-line"
                    pathLength="1"
                    stroke-dasharray="1"
                    stroke-dashoffset="1">
                    <animate
                        id="lineDraw"
                        attributeName="stroke-dashoffset"
                        from="1"
                        to="0"
                        dur="2s"
                        begin="indefinite"
                        fill="freeze"
                        calcMode="spline"
                        keyTimes="0;1"
                        keySplines="0.4 0 0.2 1"
                    />
                </path>
                <g class="dot-group" filter="url(#dot-glow)">
                    <circle class="line-dot" r="4" cx="0" cy="0"/>

                    <circle class="line-dot2" r="0" cx="0" cy="0">
                        <animate attributeName="r" from="0" to="18" dur="2s" begin="indefinite"/>
                        <animate attributeName="opacity" from="0.8" to="0" dur="2s" begin="indefinite"/>
                        <animate attributeName="stroke-width" from="4" to="0" dur="2s" begin="indefinite"/>
                    </circle>


                    <animateMotion
                        id="dotMove"
                        dur="2s"
                        begin="indefinite"
                        fill="freeze"
                        path="M0,190 C90,185 220,140 300,0"
                        calcMode="spline"
                        keyTimes="0;1"
                        keySplines="0.4 0 0.2 1"
                    />
                </g>
            </svg>
            <div class="decoration-circle"></div>

        </div>
        <p class="bottom-text mon">*數據來源：根據 1998-2025 年多項臨床研究與使用者問卷回饋綜合統計</p>
        <div class="center-banner-bg">
            <img src="/static/img/center-banner.webp" decoding="async" loading="lazy" alt="犀利士Cialis使用者心理焦慮降數據統計：使用者感到更自信">
        </div>
    </div>
    <section class="iief">
        <h2 class="sec-title">國際勃起功能指數表 (IIEF-5)</h2>
        <p class="iief-sub">國際通用的勃起性功能障礙自我評估表(簡稱IIEF-5)為國際臨床常用的勃起功能自我評估工具，可用於初步了解目前狀態與風險程度。</p>
        <table class="iief-table">
            <thead>
                <tr class="iief-header">
                    <th class="iief-header-item" scope="col">問題</th>
                    <th class="iief-header-item" scope="col">1分</th>
                    <th class="iief-header-item" scope="col">2分</th>
                    <th class="iief-header-item" scope="col">3分</th>
                    <th class="iief-header-item" scope="col">4分</th>
                    <th class="iief-header-item" scope="col">5分</th>
                </tr>
            </thead>
            <tbody>
                <tr class="iief-row" data-question="1">
                    <th class="iief-question" scope="row">1. 您對陰莖能夠且維持勃起的信心程度如何？</th>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q1" value="1" data-score="1"><span class="iief-score">1分</span>毫無信心</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q1" value="2" data-score="2"><span class="iief-score">2分</span>信心較低</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q1" value="3" data-score="3"><span class="iief-score">3分</span>信心中等</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q1" value="4" data-score="4"><span class="iief-score">4分</span>信心較高</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q1" value="5" data-score="5"><span class="iief-score">5分</span>信心十足</label></td>
                </tr>
                <tr class="iief-row" data-question="2">
                    <th class="iief-question" scope="row">2. 受到性刺激後，有多少次能夠堅挺地進入伴侶體內？</th>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q2" value="1" data-score="1"><span class="iief-score">1分</span>幾乎沒有</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q2" value="2" data-score="2"><span class="iief-score">2分</span>只有幾次</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q2" value="3" data-score="3"><span class="iief-score">3分</span>有時可以</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q2" value="4" data-score="4"><span class="iief-score">4分</span>多數時候</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q2" value="5" data-score="5"><span class="iief-score">5分</span>總是可以</label></td>
                </tr>
                <tr class="iief-row" data-question="3">
                    <th class="iief-question" scope="row">3. 性交時，有多少次能在進入伴侶體內後維持勃起？</th>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q3" value="1" data-score="1"><span class="iief-score">1分</span>幾乎沒有</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q3" value="2" data-score="2"><span class="iief-score">2分</span>只有幾次</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q3" value="3" data-score="3"><span class="iief-score">3分</span>有時可以</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q3" value="4" data-score="4"><span class="iief-score">4分</span>多數時候</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q3" value="5" data-score="5"><span class="iief-score">5分</span>總是可以</label></td>
                </tr>
                <tr class="iief-row" data-question="4">
                    <th class="iief-question" scope="row">4. 性交時，維持堅挺勃起至性交完成，有多大困難？</th>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q4" value="1" data-score="1"><span class="iief-score">1分</span>極其困難</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q4" value="2" data-score="2"><span class="iief-score">2分</span>非常困難</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q4" value="3" data-score="3"><span class="iief-score">3分</span>困難</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q4" value="4" data-score="4"><span class="iief-score">4分</span>有點困難</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q4" value="5" data-score="5"><span class="iief-score">5分</span>沒有困難</label></td>
                </tr>
                <tr class="iief-row" data-question="5">
                    <th class="iief-question" scope="row">5. 您對性生活整體滿意嗎？</th>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q5" value="1" data-score="1"><span class="iief-score">1分</span>極不滿意</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q5" value="2" data-score="2"><span class="iief-score">2分</span>少數滿意</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q5" value="3" data-score="3"><span class="iief-score">3分</span>一半滿意</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q5" value="4" data-score="4"><span class="iief-score">4分</span>多數滿意</label></td>
                    <td class="iief-options"><label class="iief-option"><input type="radio" name="iief_q5" value="5" data-score="5"><span class="iief-score">5分</span>非常滿意</label></td>
                </tr>
            </tbody>
        </table>
        <div class="iief-note">
            <div class="iief-result" id="iiefResult" style="display: none;">
                <div class="iief-result-header">
                    <h3 class="iief-result-title">您的評分結果</h3>
                    <p class="iief-result-score">總分：<span id="iiefTotalScore">0</span>分</p>
                </div>
            </div>
            <p class="iief-guide-title">多數使用者並不是一次解決，而是透過循序調整 + 實際反應觀察，找到最適合自己的使用頻率與組合。</p>
            <ol class="iief-cards-wrapper">
                <li class="iief-card" id="iiefCard21">
                    <h4 class="iief-card-header"><span class="iief-card-score">21 分以上</span>功能正常</h4>
                    <p class="iief-card-desc">▶ 目前未顯示明顯勃起功能障礙</p>
                    <p class="iief-card-desc">▶ 若偶發狀況，通常與疲勞、壓力或作息相關，可選擇試用體驗效果</p>
                    <div class="iief-card-recommend">
                        <p class="iief-card-recommend-title">建議方案：<a href="/goods/11" class="iief-card-recommend-text">體驗型方案</span><span class="iief-card-recommend-box">單次體驗裝1盒<svg class="arrowicon" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg></a></p>
                        <p class="iief-card-recommend-sub">適合：一般使用者，嘗試提升性生活質量或延長勃起時間</p>
                    </div>
                </li>
                
                <li class="iief-card" id="iiefCard12">
                    <h4 class="iief-card-header"><span class="iief-card-score">12–20 分</span>輕度功能下降</h4>
                    <p class="iief-card-desc">▶ 在特定情境下可能出現硬度或持久度不足</p>
                    <p class="iief-card-desc">▶ 多數屬於可逆型狀態，提高性生活質量可嘗試短期方案</p>
                    <div class="iief-card-recommend">
                        <p class="iief-card-recommend-title">建議方案：<a href="/goods/13" class="iief-card-recommend-text">短期改善方案</span><span class="iief-card-recommend-box">短期試用組3盒<svg class="arrowicon" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg></a></p>
                        <p class="iief-card-recommend-sub">適合：輕度、初次使用者，觀察身體反應與效果穩定度</p>
                    </div>
                </li>
                
                <li class="iief-card" id="iiefCard8">
                    <h4 class="iief-card-header"><span class="iief-card-score">8–11 分</span>中度功能障礙</h4>
                    <p class="iief-card-desc">▶ 勃起穩定性與硬度明顯受影響</p>
                    <p class="iief-card-desc">▶ 建議進一步了解改善方式與使用策略，建議選擇穩定改善的方案</p>
                    <div class="iief-card-recommend">
                        <p class="iief-card-recommend-title">建議方案：<a href="/goods/16" class="iief-card-recommend-text">穩定改善方案</span><span class="iief-card-recommend-box">標準配置組6盒<svg class="arrowicon" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg></a></p>
                        <p class="iief-card-recommend-sub">適合：中度狀況、希望提升成功率者，注重效果連續性與心理安全感</p>
                    </div>
                </li>

                <li class="iief-card" id="iiefCard5">
                    <h4 class="iief-card-header"><span class="iief-card-score">5–7 分</span>重度功能障礙</h4>
                    <p class="iief-card-desc">▶ 勃起困難已影響性生活品質</p>
                    <p class="iief-card-desc">▶ 通常需要較完整的改善規劃，需要較長時間的穩定調整，建議選擇長期方案</p>
                    <div class="iief-card-recommend">
                        <p class="iief-card-recommend-title">建議方案：<a href="/goods/19" class="iief-card-recommend-text">完整調整方案</span><span class="iief-card-recommend-box">長期規劃組12盒<svg class="arrowicon" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg></a></p>
                        <p class="iief-card-recommend-sub">適合：長期困擾、重度狀況，需要建立穩定硬度與恢復自信</p>
                    </div>
                </li>
            </ol>
        </div>
    </section>
    <section class="step">
        <h2 class="sec-title">簡單三步您即將擁有更好的性生活</h2>
        <div class="step-check">
            <svg class="check" viewBox="0 0 1024 1024"><use href="#icon-righticon"></use></svg>
            <svg class="check" viewBox="0 0 1024 1024"><use href="#icon-righticon"></use></svg>
            <svg class="check" viewBox="0 0 1024 1024"><use href="#icon-righticon"></use></svg>
        </div>

        <ol class="step-content">
            <li class="step-content-item">
                <h3 class="step-content-item-title"><span class="num">1.</span>訂購適合您的犀利士Cialis組合方案</h3>
                <p class="step-sub">提前備好所需<br>避免需要時卻來不及訂購</p>
                <img src="/static/img/step1.webp" loading="lazy" decoding="async" width="1024" height="1024" alt="線上訂購犀利士Cialis步驟1">
                <div class="down-box">
                    <svg class="downarrow-icon" viewBox="0 0 1024 1024"><use href="#icon-downarrow-icon"></use></svg>
                </div>
            </li>
            <li class="step-content-item">
                <h3 class="step-content-item-title"><span class="num">2.</span>隱密包裝 全程安心</h3>
                <p class="step-sub">素色紙盒包裝外無敏感字樣<br>取件全程安心</p>
                <img src="/static/img/step2.webp" loading="lazy" decoding="async" width="1024" height="1024" alt="線上訂購犀利士Cialis步驟2">
                <div class="down-box">
                    <svg class="downarrow-icon" viewBox="0 0 1024 1024"><use href="#icon-downarrow-icon"></use></svg>
                </div>
            </li>
            <li class="step-content-item">
                <h3 class="step-content-item-title"><span class="num">3.</span>與伴侶享受美好時光</h3>
                <p class="step-sub">不再有勃起焦慮<br>恢復滿意表現</p>
                <img src="/static/img/step3.webp" loading="lazy" decoding="async" width="1024" height="1024" alt="線上訂購犀利士Cialis步驟3">
            </li>
        </ol>
    </section>

    <section class="footer-buy">
        <div class="footer-left">
            <img src="{{ storage_url($product->m_img?:$product->img) }}" loading="auto" decoding="async" alt="{{ $product->name }}">
            <p class="green-title">禮來犀利士CialisCialis<span>{{ $product->name }} {{ $product->quantity }}盒<span></p>
            <p class="red-price"><span class="twd">NT$</span>{{ number_format(round($product->price)) }}</p>
        </div>
        <a class="main-btn" href="{{ url('shopping/'.$product->id) }}">立即訂購<svg class="btn-icon buy-icon" viewBox="0 0 1055 1024"><use href="#icon-buyicon"></use></svg>
            @if($product->quantity >= 4)
                <div class="discount">
                    <span class="discount-content">還有免運哦</span>
                </div>
            @endif 
        </a>
    </section>

    @include('components.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item"><a href="' . url('product') . '">訂購犀利士</a></li><li class="breadcrumb__item">犀利士 20mg ' . $product->quantity . '盒</li>'])
@endsection

