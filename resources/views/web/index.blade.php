@extends('web.layout')

@section('script')
    @parent
    <script src="{{ assetv('static/js/price-animator.js') }}"></script>
    <script>
        var bannerTextExitMs = 800;
        var bannerExitTimer = null;
        var bannerTextInitialShown = false;

        function showActiveBannerText(textId) {
            if (!textId) {
                return;
            }
            var incoming = document.getElementById(textId);
            if (!incoming) {
                return;
            }

            if (bannerExitTimer) {
                window.clearTimeout(bannerExitTimer);
                bannerExitTimer = null;
            }

            var blocks = document.querySelectorAll('.text-effect');
            var outgoing = null;
            var i;
            for (i = 0; i < blocks.length; i++) {
                var b = blocks[i];
                if (b.classList.contains('splitting') && !b.classList.contains('is-exiting') && b !== incoming) {
                    outgoing = b;
                    break;
                }
            }

            for (i = 0; i < blocks.length; i++) {
                var block = blocks[i];
                if (block === incoming) {
                    continue;
                }
                if (outgoing && block === outgoing) {
                    block.classList.add('is-exiting');
                } else {
                    block.classList.remove('splitting', 'is-exiting');
                }
            }

            incoming.classList.remove('is-exiting');

            if (outgoing) {
                if (!outgoing.classList.contains('splitting')) {
                    outgoing.classList.add('splitting');
                }

                bannerExitTimer = window.setTimeout(function() {
                    outgoing.classList.remove('splitting', 'is-exiting');
                    if (outgoing.id === 'text-banner-0') {
                        outgoing.classList.remove('text-effect--static');
                    }
                    incoming.classList.add('splitting');
                    bannerExitTimer = null;
                }, bannerTextExitMs);
            } else if (
                incoming.id === 'text-banner-0' &&
                incoming.classList.contains('splitting') &&
                bannerTextInitialShown
            ) {
                return;
            } else {
                incoming.classList.add('splitting');
            }
        }

        window.showActiveBannerText = showActiveBannerText;

        function bootHomeBannerText() {
            var first = document.getElementById('text-banner-0');
            if (first) {
                first.classList.add('splitting');
                bannerTextInitialShown = true;
            }
        }

        window.bootHomeBannerText = bootHomeBannerText;

        function initHeroVideoCarousel() {
            var autoplayDelayMs = 8000;
            var transitionMs = 1000;
            var currentIndex = 0;
            var autoTimer = null;
            var isTransitioning = false;
            var suspended = false;

            var carousel = document.getElementById('hero-video-carousel');
            if (!carousel) {
                return;
            }

            var slides = Array.prototype.slice.call(carousel.querySelectorAll('.hero-slide'));
            var textBlocks = document.querySelectorAll('.text-effect-wrap .text-effect');
            if (!slides.length) {
                return;
            }

            function syncBannerSlideAria(activeIndex) {
                Array.prototype.forEach.call(slides, function(slide, idx) {
                    var isActive = idx === activeIndex;
                    slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
                });
                Array.prototype.forEach.call(textBlocks, function(block, idx) {
                    block.setAttribute('aria-hidden', idx === activeIndex ? 'false' : 'true');
                });
            }

            function pauseHeroVideoCarousel() {
                if (suspended) {
                    return;
                }
                suspended = true;
                window.clearTimeout(autoTimer);
                autoTimer = null;
            }

            function resumeHeroVideoCarousel() {
                if (!suspended) {
                    return;
                }
                suspended = false;
                scheduleNext();
            }

            window.pauseHeroVideoCarousel = pauseHeroVideoCarousel;
            window.resumeHeroVideoCarousel = resumeHeroVideoCarousel;

            function activate(index) {
                Array.prototype.forEach.call(slides, function(slide, idx) {
                    slide.classList.toggle('is-active', idx === index);
                });
                syncBannerSlideAria(index);
                var textId = slides[index].getAttribute('data-bind-text');
                if (typeof window.showActiveBannerText === 'function') {
                    window.showActiveBannerText(textId);
                }
            }

            function goTo(nextIndex) {
                if (suspended || isTransitioning || nextIndex === currentIndex) {
                    return;
                }
                var outgoing = slides[currentIndex];
                var incoming = slides[nextIndex];

                isTransitioning = true;
                outgoing.classList.add('is-outgoing');
                incoming.classList.add('is-active');
                syncBannerSlideAria(nextIndex);

                if (typeof window.showActiveBannerText === 'function') {
                    window.showActiveBannerText(incoming.getAttribute('data-bind-text'));
                }

                window.setTimeout(function() {
                    outgoing.classList.remove('is-active', 'is-outgoing');
                    currentIndex = nextIndex;
                    isTransitioning = false;
                }, transitionMs);
            }

            function scheduleNext() {
                window.clearTimeout(autoTimer);
                autoTimer = null;
                if (suspended) {
                    return;
                }
                autoTimer = window.setTimeout(function tick() {
                    if (suspended) {
                        return;
                    }
                    goTo((currentIndex + 1) % slides.length);
                    if (!suspended) {
                        autoTimer = window.setTimeout(tick, autoplayDelayMs);
                    }
                }, autoplayDelayMs);
            }

            activate(0);
            scheduleNext();
        }

        function startHomeBannerTextAndHero() {
            bootHomeBannerText();
            initHeroVideoCarousel();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startHomeBannerTextAndHero);
        } else {
            startHomeBannerTextAndHero();
        }

        window.addEventListener('load', function onBannerLoadFallback() {
            var first = document.getElementById('text-banner-0');
            if (first && !first.classList.contains('splitting')) {
                bootHomeBannerText();
            }
        });
    </script>

    <script>
        (function() {
            var isMobile = window.matchMedia('(max-width: 768px)').matches;

            var coreItems = $('.index-banner .core-item');
            var currentIndex = 0;
            var autoRotateTimer = null;
            var clickTimer = null;
            var hoveredIndex = null;
            var isPaused = false;

            // 移除所有动画类
            function removeAllAnimate() {
                coreItems.removeClass('core-item--animate');
            }

            // 添加动画类到指定索引
            function addAnimate(index) {
                removeAllAnimate();
                coreItems.eq(index).addClass('core-item--animate');
                currentIndex = index;
            }

            // 切换到下一个
            function nextBox() {
                currentIndex = (currentIndex + 1) % coreItems.length;
                addAnimate(currentIndex);
            }

            // 开始自动轮播
            function startAutoRotate() {
                if (autoRotateTimer) {
                    clearInterval(autoRotateTimer);
                }
                isPaused = false;
                autoRotateTimer = setInterval(function() {
                    if (!isPaused) {
                        nextBox();
                    }
                }, 8000);
            }

            // 停止自动轮播
            function stopAutoRotate() {
                if (autoRotateTimer) {
                    clearInterval(autoRotateTimer);
                    autoRotateTimer = null;
                }
            }

            // PC端处理
            if (!isMobile) {
                coreItems.each(function(index) {
                    var $box = $(this);

                    // 鼠标悬停
                    $box.on('mouseenter', function() {
                        isPaused = true;
                        stopAutoRotate();
                        removeAllAnimate();
                        $box.addClass('core-item--animate');
                        hoveredIndex = index;
                        currentIndex = index;
                    });

                    // 鼠标离开
                    $box.on('mouseleave', function() {
                        hoveredIndex = null;
                        // 从当前hover的继续轮播
                        startAutoRotate();
                    });
                });
            } else {
                // 移动端处理
                coreItems.each(function(index) {
                    var $box = $(this);

                    $box.on('click', function() {
                        // 清除之前的点击定时器
                        if (clickTimer) {
                            clearTimeout(clickTimer);
                        }

                        // 停止自动轮播
                        stopAutoRotate();
                        removeAllAnimate();
                        $box.addClass('core-item--animate');
                        currentIndex = index;

                        // 8秒后继续轮播
                        clickTimer = setTimeout(function() {
                            startAutoRotate();
                        }, 8000);
                    });
                });
            }

            // 初始化：开始自动轮播
            $(document).ready(function() {
                // 先给第一个添加动画类
                addAnimate(0);
                // 3秒后开始轮播
                setTimeout(function() {
                    startAutoRotate();
                }, 3000);
            });
        })();
    </script>

    <script>
        if (window.matchMedia('(max-width: 768px)').matches) {
            const cards = document.querySelectorAll('.news-card');

            function updateActiveCard() {
                let viewportCenter = window.innerHeight / 2;
                let closestCard = null;
                let closestDistance = Infinity;

                cards.forEach(card => {
                    const rect = card.getBoundingClientRect();
                    const cardCenter = rect.top + rect.height / 2;
                    const distance = Math.abs(cardCenter - viewportCenter);

                    if (distance < closestDistance) {
                        closestDistance = distance;
                        closestCard = card;
                    }
                });

                // 清除所有 active
                cards.forEach(card => card.classList.remove('active'));

                // 設置視窗中心最近的那張
                if (closestCard) {
                    closestCard.classList.add('active');
                }
            }

            // 建議用 throttle / requestAnimationFrame 以防過度觸發
            let ticking = false;
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    requestAnimationFrame(() => {
                        updateActiveCard();
                        ticking = false;
                    });
                    ticking = true;
                }
            });

            // 進入頁面也先跑一次
            updateActiveCard();
        }

    </script>

    <script>
        (() => {
            // 1️⃣ 要监听的所有元素
            const observeItems = document.querySelectorAll(
                '.mon, svg.line, svg.progress'
            );
            if (!observeItems.length) return;

            // 2️⃣ 防止重复触发（一次性）
            const processed = new WeakSet();

            // 3️⃣ Observer
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    const el = entry.target;
                    if (processed.has(el)) return;

                    // ---- A. 普通 HTML 元素：加 .now ----
                    if (el.classList.contains('mon')) {
                        el.classList.add('now');
                    }

                    // ---- B. SVG：触发 SMIL 动画 ----
                    if (el.tagName.toLowerCase() === 'svg' && el.classList.contains('line')) {
                        const lineDraw = el.querySelector('#lineDraw');
                        const fillFade = el.querySelector('#fillFade');
                        const dotMove  = el.querySelector('#dotMove');

                        if (lineDraw) lineDraw.beginElement();
                        if (fillFade) fillFade.beginElement();
                        if (dotMove)  dotMove.beginElement();

                        // 脉冲动画（全部一起）
                        el.querySelectorAll('.line-dot2 animate, .line-dot3 animate')
                        .forEach(anim => anim.beginElement());

                        const decorationCircle = el.parentElement?.querySelector('.decoration-circle');
                        if (decorationCircle) decorationCircle.classList.add('now');
                    }

                    // ---- C. 进度条 SVG：触发 SMIL 动画 ----
                    if (el.tagName.toLowerCase() === 'svg' && el.classList.contains('progress')) {
                        // 触发路径动画
                        const pathAnimate = el.querySelector('.arc-path animate');
                        if (pathAnimate) pathAnimate.beginElement();

                        // 触发圆点移动动画
                        const dotAnimateMotion = el.querySelector('animateMotion');
                        if (dotAnimateMotion) dotAnimateMotion.beginElement();

                        // 给相邻的 decoration-circle 添加 now
                        const decorationCircle = el.parentElement?.querySelector('.decoration-circle');
                        if (decorationCircle) decorationCircle.classList.add('now');
                    }

                    processed.add(el);
                });
            }, {
                root: null,
                threshold: 0.5   // ≈ 原本 triggerOffsetPercent = 0
            });

            // 4️⃣ 统一 observe
            observeItems.forEach(el => observer.observe(el));
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('canvas360');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        const img = new Image();
        const SPRITE_SRC = '/static/img/3600.webp'; // 10200 × 600 sprite

        const TOTAL_FRAMES = 17;

        // =======================
        // 🔧 可調節參數（只改這裡）
        // =======================
        const PARAMS = {
            // 阻尼（追 target 的速度）
            damping: 0.1,               // 建議 0.08 ~ 0.12
            enableDamping: true,

            // Scroll 映射倍數（一般 1）
            scrollMultiplier: 1,

            // Scroll 進度映射區間（以 viewport 高度為基準）
            // 例：start=0.2 表示 canvas 上緣進入視窗 20% 開始轉
            //     end=0.9   表示到視窗 90% 時轉完一圈
            scrollStartOffset: 0.1,     // 0 ~ 1
            scrollEndOffset: 0.5,       // 0 ~ 1，需 > start

            // 向下滾動是否反向旋轉
            invertScroll: false
        };

        let currentFrame = 0;
        let targetFrame = 0;

        // =======================
        // Resize（DPR 安全）
        // =======================
        function resize() {
            const dpr = window.devicePixelRatio || 1;
            const rect = canvas.getBoundingClientRect();

            canvas.width  = rect.width * dpr;
            canvas.height = rect.height * dpr;

            canvas.style.width  = rect.width + 'px';
            canvas.style.height = rect.height + 'px';

            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            render();
            onScroll(); // resize 後立即重算一次，避免跳幀
        }

        window.addEventListener('resize', resize);

        // =======================
        // Render（contain，不裁切）
        // =======================
        function render() {
            if (!img.complete) return;

            const frameW = img.width / TOTAL_FRAMES; // 600
            const frameH = img.height;               // 600

            const index = Math.round(currentFrame);
            const sx = index * frameW;

            const cssW = canvas.clientWidth;
            const cssH = canvas.clientHeight;

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            const scale = Math.min(cssW / frameW, cssH / frameH);

            const dw = frameW * scale;
            const dh = frameH * scale;
            const dx = (cssW - dw) / 2;
            const dy = (cssH - dh) / 2;

            ctx.drawImage(img, sx, 0, frameW, frameH, dx, dy, dw, dh);
        }

        // =======================
        // Scroll → Frame（只靠 scroll）
        // =======================
        function onScroll() {
            const rect = canvas.getBoundingClientRect();
            const vh = window.innerHeight || 1;

            // 轉為「canvas 上緣在視窗中的位置比例」
            // rect.top = 0 表示上緣貼齊視窗上方
            // rect.top = vh 表示上緣在視窗下方剛要進來
            const t = 1 - (rect.top / vh); // 粗略進場進度（可超出 0~1）

            // 映射到你指定的區間內
            const start = PARAMS.scrollStartOffset;
            const end = PARAMS.scrollEndOffset;
            const denom = Math.max(0.0001, (end - start));

            let progress = (t - start) / denom; // 轉成 0~1 的進度
            progress = Math.min(Math.max(progress, 0), 1);

            if (PARAMS.invertScroll) progress = 1 - progress;

            targetFrame =
            progress * (TOTAL_FRAMES - 1) * PARAMS.scrollMultiplier;

            targetFrame = Math.max(0, Math.min(TOTAL_FRAMES - 1, targetFrame));
        }

        window.addEventListener('scroll', onScroll, { passive: true });

        // =======================
        // Animation Loop（平滑）
        // =======================
        function animate() {
            if (PARAMS.enableDamping) {
            currentFrame += (targetFrame - currentFrame) * PARAMS.damping;
            } else {
            currentFrame = targetFrame;
            }

            render();
            requestAnimationFrame(animate);
        }

        // =======================
        // Init
        // =======================
        let initialized = false;
        function init() {
            if (initialized) return;
            initialized = true;
            resize();
            onScroll();
            animate();
        }

        img.addEventListener('load', init);

        function startSpriteLoad() {
            if (img.src) return;
            img.src = SPRITE_SRC;
            if (img.complete) init();
        }

        // 進入可視範圍前才載入 360 sprite，降低首頁初始負擔
        if ('IntersectionObserver' in window) {
            const spriteObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    startSpriteLoad();
                    observer.unobserve(entry.target);
                });
            }, {
                root: null,
                rootMargin: '200px',
                threshold: 0.01
            });

            spriteObserver.observe(canvas);
        } else {
            startSpriteLoad();
        }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bgBox = document.getElementById('index-banner-bg-box');
            if (!bgBox) return;

            const imgs = bgBox.querySelectorAll('.index-top-banner-bg-img');
            if (!imgs.length) return;

            let currentIndex = 0;
            let intervalId = null;

            function loadBannerImage(img) {
                if (!img || !img.dataset || !img.dataset.src || img.src) return;
                img.src = img.dataset.src;
            }

            function showImg(index) {
                loadBannerImage(imgs[index]);
                imgs.forEach((img, i) => {
                    img.style.opacity = i === index ? '1' : '0';
                });
            }

            function startBgSlideshow() {
                if (intervalId) return;
                // 進入可視區後預載下一張，減少切換時閃動
                loadBannerImage(imgs[(currentIndex + 1) % imgs.length]);
                intervalId = setInterval(() => {
                    currentIndex = (currentIndex + 1) % imgs.length;
                    showImg(currentIndex);
                    loadBannerImage(imgs[(currentIndex + 1) % imgs.length]);
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

            bgObserver.observe(bgBox);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.querySelector('.product-sec-title');
            if (el) {
                var text = el.textContent;
                el.innerHTML = text.split('').map(function (ch) {
                    return '<span>' + ch + '</span>';
                }).join('');
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            const ul = document.getElementById("brand-slogan");
            if (!ul) return;

            const lis = ul.querySelectorAll("li");
            if (!lis.length) return;

            // === 生成動畫容器 ===
            const effect = document.createElement("div");
            effect.id = "slogan-effect";
            ul.after(effect);

            const row1 = [];
            const row2 = [];

            lis.forEach(li => {

                const clone = li.cloneNode(true);

                // 找到第一個包含空格的文字節點
                const walker = document.createTreeWalker(
                    clone,
                    NodeFilter.SHOW_TEXT,
                    {
                        acceptNode(node) {
                            return node.nodeValue.includes(" ")
                                ? NodeFilter.FILTER_ACCEPT
                                : NodeFilter.FILTER_SKIP;
                        }
                    }
                );

                const splitNode = walker.nextNode();
                if (!splitNode) return;

                const raw = splitNode.nodeValue;
                const index = raw.indexOf(" ");

                const leftText = raw.slice(0, index);
                const rightText = raw.slice(index + 1);

                // 保留左半段
                splitNode.nodeValue = leftText;

                // === 第一排 ===
                const firstHTML = clone.innerHTML.trim();

                // === 第二排 ===
                const frag = document.createDocumentFragment();

                if (rightText) {
                    frag.appendChild(document.createTextNode(rightText));
                }

                let parent = splitNode.parentNode;
                let next = splitNode.nextSibling;

                while (next) {
                    const temp = next.nextSibling;
                    frag.appendChild(next);
                    next = temp;
                }

                while (parent && parent !== clone) {
                    let sibling = parent.nextSibling;
                    while (sibling) {
                        const temp = sibling.nextSibling;
                        frag.appendChild(sibling);
                        sibling = temp;
                    }
                    parent = parent.parentNode;
                }

                const tempDiv = document.createElement("div");
                tempDiv.appendChild(frag);
                const secondHTML = tempDiv.innerHTML.trim();

                row1.push(`<span class="word">${firstHTML}</span>`);
                row2.push(`<span class="word">${secondHTML}</span>`);
            });

            // 無縫
            if (row1.length) {
                row1.push(row1[0]);
                row2.push(row2[0]);
            }

            effect.innerHTML = `
                <div class="words">${row1.join("")}</div>
                <div class="words">${row2.join("")}</div>
            `;

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const videos = document.querySelectorAll('.js-lazy-video');
            if (!videos.length) return;

            const loadAndPlay = (video) => {

                if (!video.dataset.src) return;

                if (video.dataset.loaded !== '1') {
                    video.src = video.dataset.src;
                    video.load();
                    video.dataset.loaded = '1';
                }

                if (video.paused) {
                    video.play().catch((error) => {
                        console.log('Video play error:', error);
                    });
                }
            };

            if (!('IntersectionObserver' in window)) {
                videos.forEach((video) => loadAndPlay(video));
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    const video = entry.target;
                    if (entry.isIntersecting) {
                        loadAndPlay(video);
                    } else if (!video.paused) {
                        video.pause();
                    }
                });
            }, {
                root: null,
                rootMargin: '200px 0px',
                threshold: 0.15
            });

            videos.forEach((video) => observer.observe(video));
        });
    </script>

    <script>
        (function() {
            if (window.matchMedia('(max-width: 1024px)').matches) return;

            const box = document.getElementById('index-banner-bg-box');
            const scaleEl = box && box.querySelector('.index-top-banner-bg__scale');
            const flipEl = box && box.querySelector('.index-top-banner-bg__flip');
            const product = document.querySelector('.product');
            const docfinger = box && box.querySelector('.docfinger');
            if (!box || !scaleEl || !flipEl) return;

            const SWITCH_SCALE = 0.2;
            /** 從起點開始，滑動多少 px 之後切換成藥丸（而不是用「還剩多少」計算） */
            const SWITCH_SCROLL_PX = 400;
            const PILL_MIN_SCALE = 0.015;
            const PILL_SCALE_RANGE = 300;
            /** 翻轉對稱軸：'Y' = 左右翻，'X' = 上下翻，'diagonal' = -45° 對角線（左上→右下） */
            const FLIP_AXIS = 'diagonal';
            let ticking = false;

            function getDocumentTop(el) {
                var top = 0;
                while (el) {
                    top += el.offsetTop;
                    el = el.offsetParent;
                }
                return top;
            }
            function refreshStartY() {
                startY = Math.max(0, getDocumentTop(box) - window.innerHeight);
            }
            var startY = 0;
            refreshStartY();

            function getScrollY() {
                var lenis = window.__scrollFx && window.__scrollFx.lenis;
                if (lenis && typeof lenis.scroll === 'number' && !Number.isNaN(lenis.scroll)) {
                    return lenis.scroll;
                }
                return window.scrollY || window.pageYOffset || 0;
            }

            function updateScale() {
                const scrollY = getScrollY();
                const vh = window.innerHeight;
                // 用 box 在文件中的固定位置算起點，sticky 時才不會讓 startY 跟著跑、progress 才能累積
                const endY = startY + SWITCH_SCROLL_PX;
                const range = Math.max(1, endY - startY);
                let progress = (scrollY - startY) / range;
                progress = Math.max(0, Math.min(1, progress));
                var scale;
                var showPill = scrollY >= endY;
                var progress2 = 0;
                if (!showPill) {
                    scale = 1 - progress * (1 - SWITCH_SCALE);
                } else {
                    progress2 = Math.min(1, (scrollY - endY) / PILL_SCALE_RANGE);
                    scale = SWITCH_SCALE - progress2 * (SWITCH_SCALE - PILL_MIN_SCALE);
                }
                box.style.transform = '';
                scaleEl.style.transform = 'scale(' + scale + ')';
                flipEl.setAttribute('data-flip-axis', FLIP_AXIS);
                if (showPill) {
                    flipEl.classList.add('is-pill');
                    box.classList.add('is-pill');
                } else {
                    flipEl.classList.remove('is-pill');
                    box.classList.remove('is-pill');
                }
                var showFinger = showPill && progress2 >= 1;
                if (showFinger) {
                    box.classList.add('is-doctor-visible');
                } else {
                    box.classList.remove('is-doctor-visible');
                }
                ticking = false;
            }

            function onScroll() {
                if (ticking) return;
                ticking = true;
                requestAnimationFrame(updateScale);
            }

            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', function() {
                refreshStartY();
                onScroll();
            });
            requestAnimationFrame(updateScale);
        })();
    </script>

    {{-- text-erise 文字漸顯：暫時停用
    <script>
        (function() {
            function wrapErise() {
                document.querySelectorAll('p.text-erise').forEach(function(p) {
                    if (p.querySelector('.erise-inner')) return;
                    var html = p.innerHTML;
                    var inner = document.createElement('span');
                    inner.className = 'erise-inner';
                    var eriser = document.createElement('span');
                    eriser.className = 'eriser';
                    eriser.innerHTML = html;
                    inner.appendChild(eriser);
                    p.appendChild(inner);
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', wrapErise);
            } else {
                wrapErise();
            }
        })();
    </script>
    --}}
@stop
@section('body-class', 'page-home')
@section('body-data-page', 'home')
@section('breadcrumb')@show
@section('content')
    @php
    $pageIndexSprite = asset('static/svg/page-index.svg');
    @endphp

    <section class="index-banner">
        <h1><span class="brand">禮來犀利士<sup>®</sup></span>長效陪伴 讓每一次都自信從容</h1>
        <ul class="text-effect-wrap" role="list" aria-label="犀利士產品特點說明">
            <li class="text-effect text-effect--static" id="text-banner-0" role="group" aria-labelledby="text-banner-0-title" aria-hidden="false">
                <strong class="text-effect-p1" id="text-banner-0-title">長達 36小時 藥效</strong>
                <p class="text-effect-p2"><svg class="tickicon" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-tickicon"></use></svg>禮來經典原廠藥，專注長效研發</p>
                <p class="text-effect-p3"><svg class="tickicon" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-tickicon"></use></svg>有效時間領先傳統短效壯陽藥9倍</p>
                <a href="/product" class="main-btn">立即體驗<svg class="btn-icon buy-icon" aria-hidden="true"><use href="#icon-buyicon"></use></svg></a>
            </li>
            <li class="text-effect" id="text-banner-1" role="group" aria-labelledby="text-banner-1-title" aria-hidden="true">
                <strong class="text-effect-p1" id="text-banner-1-title">每日 5mg 保養</strong>
                <p class="text-effect-p2"><svg class="tickicon" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-tickicon"></use></svg>首款FDA核准於治療ED與攝護腺肥大</p>
                <p class="text-effect-p3"><svg class="tickicon" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-tickicon"></use></svg>泌尿科臨床醫師首選長效ED治療方案</p>
                <a href="/product" class="main-btn">查看組合方案<svg class="btn-icon buy-icon" aria-hidden="true"><use href="#icon-buyicon"></use></svg></a>
            </li>
            <li class="text-effect" id="text-banner-2" role="group" aria-labelledby="text-banner-2-title" aria-hidden="true">
                <strong class="text-effect-p1" id="text-banner-2-title">全球上市 23年</strong>
                <p class="text-effect-p2"><svg class="tickicon" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-tickicon"></use></svg>累積大量臨床使用經驗與安全數據</p>
                <p class="text-effect-p3"><svg class="tickicon" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-tickicon"></use></svg>長期受到國際醫療體系廣泛應用</p>
                <a href="/product" class="main-btn">立即訂購<svg class="btn-icon buy-icon" aria-hidden="true"><use href="#icon-buyicon"></use></svg></a>
            </li>
            <li class="text-effect" id="text-banner-3" role="group" aria-labelledby="text-banner-3-title" aria-hidden="true">
                <strong class="text-effect-p1" id="text-banner-3-title">高達 98% 滿意度</strong>
                <p class="text-effect-p2"><svg class="tickicon" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-tickicon"></use></svg>幫助全球數千萬患者重拾自信</p>
                <p class="text-effect-p3"><svg class="tickicon" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-tickicon"></use></svg>更穩定持久的表現讓伴侶滿足感大幅提升</p>
                <a href="/product" class="main-btn">重拾自信表現<svg class="btn-icon buy-icon" aria-hidden="true"><use href="#icon-buyicon"></use></svg></a>
            </li>
        </ul>
        <div class="hero-carousel" id="hero-video-carousel" role="region" aria-roledescription="carousel" aria-label="犀利士產品特點輪播">
            <img
                class="hero-slide is-active"
                src="{{ asset('static/img/indexbg.webp') }}"
                width="1920"
                height="1080"
                alt="禮來犀利士長達36小時藥效"
                aria-roledescription="slide"
                aria-label="1 / 4：長達 36小時 藥效"
                aria-labelledby="text-banner-0-title"
                aria-describedby="text-banner-0"
                data-bind-text="text-banner-0"
                aria-hidden="false"
                decoding="async"
                fetchpriority="high"
            >
            <img
                class="hero-slide"
                src="{{ asset('static/img/indexbg2.webp') }}"
                width="1920"
                height="1080"
                alt="禮來犀利士每日5mg保養方案"
                aria-roledescription="slide"
                aria-label="2 / 4：每日 5mg 保養"
                aria-labelledby="text-banner-1-title"
                aria-describedby="text-banner-1"
                data-bind-text="text-banner-1"
                aria-hidden="true"
                decoding="async"
            >
            <img
                class="hero-slide"
                src="{{ asset('static/img/indexbg3.webp') }}"
                width="1920"
                height="1080"
                alt="禮來犀利士全球上市23年臨床經驗"
                aria-roledescription="slide"
                aria-label="3 / 4：全球上市 23年"
                aria-labelledby="text-banner-2-title"
                aria-describedby="text-banner-2"
                data-bind-text="text-banner-2"
                aria-hidden="true"
                decoding="async"
            >
            <img
                class="hero-slide"
                src="{{ asset('static/img/indexbg4.webp') }}"
                width="1920"
                height="1080"
                alt="禮來犀利士高達98%使用者滿意度"
                aria-roledescription="slide"
                aria-label="4 / 4：高達 98% 滿意度"
                aria-labelledby="text-banner-3-title"
                aria-describedby="text-banner-3"
                data-bind-text="text-banner-3"
                aria-hidden="true"
                decoding="async"
            >
        </div>
        @include('components.core-sec', ['variant' => 'hero'])
        <a href="#compare" class="scroll-down" aria-label="下滑查看更多犀利士資訊" title="更多犀利士資訊"><span></span><span></span><span></span>更多犀利士資訊</a>
    </section>
    

    <section class="compare" id="compare">
        <h2 class="sec-title">犀利士與短效壯陽藥效果對比</h2>
        <table class="table-container">
            <thead class="table-top">
                <tr class="header-row">
                    <th class="col-width">犀利士與短效壯陽藥效果對比</th>
                    <th class="table-column-header highlight header-top">
                        犀利士
                    </th>
                    <th class="table-column-header">短效壯陽藥</th>
                </tr>
            </thead>
            <tbody class="table-box">
                {{--<tr class="table-row">
                    <td class="col-item">改善勃起障礙</td>
                    <td class="table-cell highlight"><span class="visually-hidden">相同效果</span><svg class="compare-tick-icon"><use href="#icon-comparetickicon"/></svg></td>
                    <td class="table-cell"><span class="visually-hidden">相同效果</span><svg class="compare-tick-icon"><use href="#icon-comparetickicon"/></svg></td>
                </tr>
                <tr class="table-row">
                    <td class="col-item">提升勃起硬度</td>
                    <td class="table-cell highlight"><span class="visually-hidden">相同效果</span><svg class="compare-tick-icon"><use href="#icon-comparetickicon"/></svg></td>
                    <td class="table-cell"><span class="visually-hidden">相同效果</span><svg class="compare-tick-icon"><use href="#icon-comparetickicon"/></svg></td>
                </tr>
                <tr class="table-row">
                    <td class="col-item">穩定維持硬度</td>
                    <td class="table-cell highlight"><span class="visually-hidden">相同效果</span><svg class="compare-tick-icon"><use href="#icon-comparetickicon"/></svg></td>
                    <td class="table-cell"><span class="visually-hidden">相同效果</span><svg class="compare-tick-icon"><use href="#icon-comparetickicon"/></svg></td>
                </tr>
                <tr class="table-row">
                    <td class="col-item">延長勃起時間</td>
                    <td class="table-cell highlight"><span class="visually-hidden">相同效果</span><svg class="compare-tick-icon"><use href="#icon-comparetickicon"/></svg></td>
                    <td class="table-cell"><span class="visually-hidden">相同效果</span><svg class="compare-tick-icon"><use href="#icon-comparetickicon"/></svg></td>
                </tr>
                <tr class="table-row">
                    <td class="col-item">改善早洩</td>
                    <td class="table-cell highlight"><span class="visually-hidden">相同效果</span><svg class="compare-tick-icon"><use href="#icon-comparetickicon"/></svg></td>
                    <td class="table-cell"><span class="visually-hidden">相同效果</span><svg class="compare-tick-icon"><use href="#icon-comparetickicon"/></svg></td>
                </tr>
                --}}
                <tr class="table-row">
                    <td class="col-item">藥效持續時間</td>
                    <td class="table-cell highlight">最長可達36小時</td>
                    <td class="table-cell">約4–6小時</td>
                </tr>
                <tr class="table-row">
                    <td class="col-item">何時服用</td>
                    <td class="table-cell highlight">性愛前30分鐘</td>
                    <td class="table-cell">性愛前30～60分鐘</td>
                </tr>
                <tr class="table-row">
                    <td class="col-item">飲食影響起效</td>
                    <td class="table-cell highlight">完全不受干擾</td>
                    <td class="table-cell">高油脂飲食會延遲起效</td>
                </tr>
                <tr class="table-row">
                    <td class="col-item">性愛節奏</td>
                    <td class="table-cell highlight">自然無需等待</td>
                    <td class="table-cell">提前服藥</td>
                </tr>
                <tr class="table-row">
                    <td class="col-item">時間焦慮</td>
                    <td class="table-cell highlight">完全無壓力</td>
                    <td class="table-cell">計時焦慮</td>
                </tr>
                <tr class="table-row">
                    <td class="col-item">週末約會</td>
                    <td class="table-cell highlight">一粒即可</td>
                    <td class="table-cell">需多備藥</td>
                </tr>
                <tr class="table-row">
                    <td class="col-item">每日保養</td>
                    <td class="table-cell highlight">5mg每日方案</td>
                    <td class="table-cell">不建議每日服用</td>
                </tr>
                <tr class="table-row">
                    <td class="col-item">適合族群</td>
                    <td class="table-cell highlight">穩定性生活、長期管理</td>
                    <td class="table-cell">單次、臨時型需求</td>
                </tr>
                
                <tr class="table-row">
                    <td class="col-item"></td>
                    <td class="table-cell highlight"></td>
                    <td class="table-cell"></td>
                </tr>
            </tbody>
        </table>
        <div class="pbanner mon">
            <div class="pbanner-inner">
                <div class="process-line">
                    <svg class="progress" viewBox="0 0 200 110" width="240" height="132" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <defs>
                            <linearGradient id="arcGradient" x1="0%" y1="0%" x2="100%" y2="0%" gradientUnits="userSpaceOnUse">
                                <stop class="arc-stop1" offset="0%"/>
                                <stop class="arc-stop2" offset="50%"/>
                                <stop class="arc-stop3" offset="100%"/>
                            </linearGradient>
                            <filter id="dotGlow" x="-50%" y="-50%" width="200%" height="200%">
                                <feGaussianBlur stdDeviation="2" result="blur"/>
                                <feMerge>
                                    <feMergeNode in="blur"/>
                                    <feMergeNode in="SourceGraphic"/>
                                </feMerge>
                            </filter>
                        </defs>

                        <path id="arcPath" d="M10 100 A90 90 0 0 1 190 100" fill="none" stroke="none" pathLength="100"/>
                        <path d="M10 100 A90 90 0 0 1 190 100" fill="none" class="arcbg"/>
                        <path d="M10 100 A90 90 0 1 1 190 100" fill="none" pathLength="100" class="arc-path">
                        <animate
                            attributeName="stroke-dashoffset"
                            from="100"
                            to="5"
                            dur="3s"
                            fill="freeze"
                            calcMode="spline"
                            keyTimes="0;1"
                            begin="indefinite"
                            keySplines="0.4 0 0.2 1"
                        />
                        </path>

                        <g filter="url(#dotGlow)">
                            <circle class="arc-dot" r="8" cx="0" cy="0"/>
                            <animateMotion
                                dur="3s"
                                fill="freeze"
                                keyPoints="0;0.95"
                                keyTimes="0;1"
                                calcMode="spline"
                                keySplines="0.4 0 0.2 1"
                            >
                                <mpath href="#arcPath" xlink:href="#arcPath"/>
                            </animateMotion>
                        </g>

                    </svg>
                    {{--<div class="decoration-circle"></div>--}}
                </div>
                <div class="pbanner-text">
                    <h3 class="progress-title mon">
                        <strong class="highlight">
                            <span class="highlight-number">97</span><span class="highlight-percent">%</span>
                        </strong>
                        男士服用犀利士後表示<span>對性生活掌控更加從容</span>
                    </h3>
                    <p class="progress-text mon">大多數男士表示在服用犀利士期間，不再被表現壓力打亂親密節奏，伴侶的每次性生活整體感受效果明顯提升</p>
                    {{--<a href="/product" class="main-btn mon">查看全部 犀利士組合方案<img src="/static/img/btnpill.webp" decoding="async" loading="lazy" alt="犀利士Cialis組合方案按鈕圖示"></a>--}}
                    <p class="progress-sub mon">*基於1000名顧客服用犀利士超過三個月後的回饋數據整理，非即時效果，依個人體質有所差異。</p>
                </div>
            </div>
            <div class="pb-bg">
                <img src="/static/img/product-banner.webp" decoding="async" loading="lazy" alt="犀利士Cialis提升男性自信心">
            </div>

        </div>
    </section>
    <section class="product watermark">
        <h2 class="product-sec-title sec-title">犀利士線上訂購</h2>
        {{--@foreach($groups as $group)
            <div class="box-container">
                <div class="shop-hero guide">
                    <h3 class="shopt">{{ $group['title'] }}</h3>
                    <span class="shopt-sub">（滑動或點擊可查看更多）</span>
                    <div class="switch">
                        <div class="prev">
                            <svg class="arrowicon" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg>
                        </div>
                        <div class="next">
                            <svg class="arrowicon" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg>
                        </div>
                    </div>
                </div>
                <div class="shop-list watermark">
                    <ol class="product-list">
                        @foreach($group['items'] as $goods)
                            <li class="product-card-item">
                                <div class="product-card product-card-show">
                                    <a class="product-card-link" href="{{ url('goods/'.$goods->id) }}">
                                        <span class="original-label" aria-label="原裝進口">原裝進口</span>
                                        <div class="goods-label-sec">
                                            <p class="goods-label">100%隱密包裝</p>
                                        </div>

                                        <img class="product-card-img" src="{{ storage_url($goods->m_img?:$goods->img) }}" decoding="async" loading="auto" alt="禮來犀利士Cialis 20mg {{ $goods->name }}">
                                    </a>
                                    <div class="product-card-info">
                                        <h4 class="goods-title">犀利士Cialis 20mg
                                            <strong class="box-count">{{ $goods->name }}<span class="box-num">{{ $goods->quantity }}</span>盒</strong>
                                        </h4>
                                        <p class="goods-subname">"{{ $goods->subname }}"</p>

                                        <a class="main-btn" href="{{ url('goods/'.$goods->id) }}">查看犀利士Cialis{{ $goods->quantity }}盒方案
                                            <svg class="btn-icon buy-icon" viewBox="0 0 1055 1024"><use href="#icon-buyicon"></use></svg>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                    @include('components.secret')
                </div>
            </div>
        @endforeach--}}

        @php
            $homeFeaturedIds = [11, 12, 14, 18];
            $homeFeaturedProducts = collect($homeFeaturedIds)
                ->map(fn ($id) => $products->firstWhere('id', $id))
                ->filter();
        @endphp
        <p class="product-content">選擇適合自己的犀利士組合方案，無論是每日長期保養或按需使用，都能享受長達 36 小時的長效陪伴體驗。</p>
        <p class="product-content">現貨供應、限時訂購優惠、全台免運，保證100%隱密包裝配送，讓你購買更安心。</p>
        @include('components.core-sec')

        <ol class="product-list">
            @foreach($homeFeaturedProducts as $goods)
                <li class="product-card product-card-show{{ $goods->id === 14 ? ' product-card--featured' : '' }}">
                    <a class="product-card-link" href="{{ url('goods/'.$goods->id) }}">
                        <span class="original-label" aria-label="原裝進口">原裝進口</span>
                        <div class="goods-label-sec">
                            <p class="goods-label">100%隱密包裝</p>
                        </div>

                        <img class="product-card-img" src="{{ storage_url($goods->m_img ?: $goods->img) }}" decoding="async" loading="auto" alt="禮來犀利士Cialis 20mg {{ $goods->name }}">
                    </a>
                    <h3 class="goods-title">犀利士Cialis 20mg
                        <strong class="box-count">{{ $goods->name }}<span class="box-num">{{ $goods->quantity }}</span>盒</strong>
                    </h3>
                    <div class="price-box" data-market-price="{{ round($goods->market_price) }}" data-price="{{ round($goods->price) }}">
                        <div class="mk-price">
                            <p class="grey-price">NT$ {{ number_format(round($goods->market_price)) }}</p>
                            <div class="discount-box">
                                <p class="discount">-<span class="descount-num">{{ $goods->discount_percent }}</span>%</p>
                            </div>
                        </div>

                        <p class="red-price"><span class="twd">NT$</span><span class="price-number">{{ number_format(round($goods->market_price)) }}</span></p>
                    </div>
                    <ul class="tags">
                        @foreach($goods->label_tags as $label)
                            <li class="tag-item">
                                <span class="tick"><svg class="tickicon" viewBox="0 0 1024 1024"><use href="#icon-tickicon"></use></svg></span>
                                <p class="tag-text">{{ $label }}</p>
                            </li>
                        @endforeach
                    </ul>
                    <a class="main-btn {{ $goods->id === 14 ? 'main-btn--featured' : 'main-btn--outline' }}" href="{{ url('goods/'.$goods->id) }}">立即訂購 {{ $goods->name }}
                        <svg class="btn-icon buy-icon" viewBox="0 0 1055 1024"><use href="#icon-buyicon"></use></svg>
                        @if(in_array($goods->id, [14, 18]) && $goods->quantity >= 4)
                            <div class="discount">
                                <span class="discount-content">免運</span>
                            </div>
                        @endif
                    </a>
                </li>
            @endforeach
        </ol>
        @include('components.secret')

    </section>
    <section class="intro mon">
        <h2 class="intro-title"><span class="intro-title-label">犀利士上市28年</span><span class="sec-title">至今仍為勃起障礙臨床長效治療首選方案</span></h2>
        @include('components.rice-scroll')
        <div class="intro-wrap">
            <p class="intro-content">
                犀利士（學名：Tadalafil，他達拉非）是經 FDA 核准用於治療男性<strong>勃起功能障礙（ED）</strong>的口服處方藥物，也是目前最具代表性的長效型 PDE5 抑制劑之一。其活性成分他達拉非不只是單純助勃，更被視為能延長男性性能力「反應窗口」的重要藥物。
            </p>
            <p class="intro-content">
                他達拉非屬於 PDE5 抑制劑，透過促進一氧化氮（NO）訊號作用，幫助陰莖海綿體血管放鬆並增加血流量，使男性在性刺激下更容易達到並維持穩定勃起。與傳統短效型壯陽藥相比，犀利士最大的特點在於藥效持續時間長，可提供長達約 36 小時的作用時間，因此也被稱為「週末丸」。
            </p>
            <p class="intro-content">
                自 1998 年上市以來，犀利士Cialis已累積 28 年臨床應用經驗，憑藉其作用時間長、自然度高、不易中斷親密節奏等特性，在台灣男性族群中長期被視為經典<strong>長效型壯陽藥代表</strong>、<strong>醫師推薦</strong>治療勃起功能障礙的<strong>一線長效型藥物</strong>。
            </p>
                
            <p class="intro-content">許多男性選擇犀利士，不只是改善硬度，更是希望擺脫時間壓力與表現焦慮，重新找回性生活主導權與親密關係的自在感。但請注意：犀利士不是春藥，本身不會直接刺激性慾，它只是幫助身體恢復正常勃起反應能力。
            </p>
            <p class="intro-content">
                選購犀利士時，應注意原廠包裝、防偽標籤與藥品來源安全性，避免購買來路不明產品。線上訂購前可參閱<a href="/promise" class="inner-link" title="犀利士正品訂購指南">犀利士正品訂購指南</a>。正確使用犀利士，有助於改善勃起硬度、持久度與性生活穩定性，尤其對於因緊張、壓力或擔心失敗而產生的心理性陽痿，往往能帶來更穩定的信心支持。
            </p>
            <img class="introimg" src="{{ asset('static/img/introimg.png') }}" alt="醫師展示犀利士" loading="lazy" decoding="async">
        </div>
        <div class="feature-list">
            <div class="feature-item mon">
                <div class="feature-icon">
                    <svg class="icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><use href="{{ $pageIndexSprite }}#icon-feature-1"/></svg>
                </div>
                <div class="feature-text">
                    <h3 class="feature-item-title">有效改善勃起功能障礙</h3>
                    <p class="feature-item-desc">“多年臨床經驗證實犀利士Cialis有效率高達95%以上，台灣、美國FDA與歐盟EMA已核准用於臨床治療”</p>
                    <p class="expert">——泌尿科臨床醫師 Dr.Johnson</p>
                </div>
                <div class="feature-img">
                    <img src="/static/img/d1.webp" decoding="async" loading="lazy" alt="有效改善勃起功能障礙">
                </div>
            </div>
            <div class="feature-item mon">
                <div class="feature-icon">
                    <svg class="icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><use href="{{ $pageIndexSprite }}#icon-feature-2"/></svg>
                </div>
                <div class="feature-text">
                    <h3 class="feature-item-title">犀利士Cialis顯著提升勃起硬度</h3>
                    <p class="feature-item-desc">“性刺激下提高陰莖充血量，使勃起狀態更加飽滿、硬度更加明顯，改善硬度不足或不夠紮實的問題”</p>
                    <p class="expert">——泌尿科臨床醫師 Dr.Brown</p>
                </div>
                <div class="feature-img">
                    <img src="/static/img/d2.webp" decoding="async" loading="lazy" alt="犀利士Cialis顯著提升勃起硬度">
                </div>
            </div>
            <div class="feature-item mon">
                <div class="feature-icon">
                    <svg class="icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><use href="{{ $pageIndexSprite }}#icon-feature-3"/></svg>
                </div>
                <div class="feature-text">
                    <h3 class="feature-item-title">穩定維持勃起硬度</h3>
                    <p class="feature-item-desc">“犀利士Cialis有助減少性愛中硬度下降或疲軟的情況，讓整個過程穩定維持堅挺，不必反覆擔心狀態變化”</p>
                    <p class="expert">——泌尿科臨床醫師 Dr.Harris</p>
                </div>
                <div class="feature-img">
                    <img src="/static/img/d3.webp" decoding="async" loading="lazy" alt="穩定維持勃起硬度">
                </div>
            </div>
            <div class="feature-item mon">
                <div class="feature-icon">
                    <svg class="icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><use href="{{ $pageIndexSprite }}#icon-feature-4"/></svg>
                </div>
                <div class="feature-text">
                    <h3 class="feature-item-title">可大幅延長勃起時間</h3>
                    <p class="feature-item-desc">“在性刺激的前提下，服用犀利士Cialis可大幅延長勃起的持續時間，使性愛過程更從容、不受時間壓力影響”</p>
                    <p class="expert">——泌尿科臨床醫師 Dr.Willson</p>
                </div>
                <div class="feature-img">
                    <img src="/static/img/d4.webp" decoding="async" loading="lazy" alt="可大幅延長勃起時間">
                </div>
            </div>
            <div class="feature-item mon">
                <div class="feature-icon">
                    <svg class="icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><use href="{{ $pageIndexSprite }}#icon-feature-5"/></svg>
                </div>
                <div class="feature-text">
                    <h3 class="feature-item-title">可提升射精控制能力</h3>
                    <p class="feature-item-desc">“部分使用者在穩定勃起後，性愛過程中射精控制感獲得明顯有效控制，對伴隨型早洩具有正向幫助”</p>
                    <p class="expert">——泌尿科臨床醫師 Dr.Reed</p>
                </div>
                <div class="feature-img">
                    <img src="/static/img/d5.webp" decoding="async" loading="lazy" alt="可提升射精控制能力">
                </div>
            </div>
            <div class="feature-item mon">
                <div class="feature-icon">
                    <svg class="icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><use href="{{ $pageIndexSprite }}#icon-feature-6"/></svg>
                </div>
                <div class="feature-text">
                    <h3 class="feature-item-title">勃起效果自然反應</h3>
                    <p class="feature-item-desc">“服用犀利士Cialis後不會在無性刺激下強制勃起，符合正常生理反應，是臨床治療指引中安全用藥原則之一”</p>
                    <p class="expert">——泌尿科臨床醫師 Dr.Davis</p>
                </div>
                <div class="feature-img">
                    <img src="/static/img/d6.webp" decoding="async" loading="lazy" alt="勃起效果自然反應">
                </div>
            </div>
            <div class="feature-item mon">
                <div class="feature-icon">
                    <svg class="icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><use href="{{ $pageIndexSprite }}#icon-feature-7"/></svg>
                </div>
                <div class="feature-text">
                    <h3 class="feature-item-title">起效時間最快14分鐘</h3>
                    <p class="feature-item-desc">“在部分情況下最快14分鐘即可出現反應，一般30分鐘開始感受到作用，犀利士Cialis起效時間清楚可預期”</p>
                    <p class="expert">——泌尿科臨床醫師 Dr.Martin</p>
                </div>
                <div class="feature-img">
                    <img src="/static/img/d7.webp" decoding="async" loading="lazy" alt="起效時間最快14分鐘">
                </div>
            </div>
            <div class="feature-item mon">
                <div class="feature-icon">
                    <svg class="icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><use href="{{ $pageIndexSprite }}#icon-feature-8"/></svg>
                </div>
                <div class="feature-text">
                    <h3 class="feature-item-title">犀利士Cialis藥效4～6小時</h3>
                    <p class="feature-item-desc">“犀利士Cialis作用時間約4～6小時，之後隨身體自然代謝，不會長時間殘留於體內，適合大部分即時使用需求”</p>
                    <p class="expert">——泌尿科臨床醫師 Dr.Smith</p>
                </div>
                <div class="feature-img">
                    <img src="/static/img/d8.webp" decoding="async" loading="lazy" alt="作用時間4～6小時">
                </div>
            </div>
            <div class="feature-item mon">
                <div class="feature-icon">
                    <svg class="icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><use href="{{ $pageIndexSprite }}#icon-feature-9"/></svg>
                </div>
                <div class="feature-text">
                    <h3 class="feature-item-title">預防高山肺水腫風險</h3>
                    <p class="feature-item-desc">“大量研究指出，服用犀利士Cialis在高海拔環境中可改善肺循環負擔，作為預防高山肺水腫的輔助考量之一”</p>
                    <p class="expert">——泌尿科臨床醫師 Dr.Adams</p>
                </div>
                <div class="feature-img">
                    <img src="/static/img/d9.webp" decoding="async" loading="lazy" alt="預防高山肺水腫風險">
                </div>
            </div>
        </div>
    </section>

    <section class="usage">
        <h2 class="sec-title">醫師建議用法</h2>
        <p class="usage-content">為什麼有些人抱怨吃了犀利士Cialis卻沒效果？往往是因為「使用方式錯誤」。犀利士Cialis並不是吃了就會自動勃起，事實上，犀利士Cialis需要「外部引信」，也就是需要性刺激配合才能發揮作用。掌握以下醫師建議的幾點，是確保藥效100%發揮的關鍵：</p>
        <ol class="usage-list">
            <li class="usage-item">
                <h3 class="usage-item-title">情緒先行</h3>
                <p><img class="usage-img" src="/static/img/usage1.webp" decoding="async" loading="lazy" alt="情緒先行">若處於高度焦慮、壓力狀態下或對性行為有「厭惡情緒」，即使服用犀利士Cialis也可能影響效果。請保持在放鬆、無壓力的環境下服用，藥效才能與副交感神經協同工作。</p>

            </li>
            <li class="usage-item">
                <h3 class="usage-item-title">時機掌握</h3>
                <p><img class="usage-img" src="/static/img/usage2.webp" decoding="async" loading="lazy" alt="時機掌握">性行為前 30～45 分鐘服用，讓血中藥物濃度達到峰值。</p>

            </li>
            <li class="usage-item">
                <h3 class="usage-item-title">飲食注意</h3>
                <p><img class="usage-img" src="/static/img/usage3.webp" decoding="async" loading="lazy" alt="飲食注意">空腹是關鍵，建議在飯後兩小時服用或避免高油脂飲食。油脂會嚴重阻礙西地那非的吸收速率，會將藥效巔峰推遲 60 分鐘以上，導致你會錯過最佳時機。</p>
            </li>
            <li class="usage-item">
                <h3 class="usage-item-title">前戲協同</h3>
                <p><img class="usage-img" src="/static/img/usage4.webp" decoding="async" loading="lazy" alt="前戲協同">服藥後 30～45 分鐘，與伴侶配合投入前戲調情會更快進入狀態。適度視覺或觸覺的性刺激，是啟動 PDE5 抑制劑機轉的必要條件。</p>
            </li>
        </ol>
        @if($effectReading->isNotEmpty())
            <section class="further-reading">
                <h3 class="snd-title">延伸閱讀</h3>
                <x-news.sections.related-list
                    :items="$effectReading"
                    :wrap-in-section="false"
                />
                <p class="further-reading__more-wrap">
                    <a href="/effect" class="main-btn further-reading__more">更多使用心得</a>
                </p>
            </section>
        @endif
    </section>


    <section class="work">
        <h2 class="sec-title">犀利士助勃機轉</h2>
        <ol class="work-wrap">
            <li class="work-item mon">
                <video data-src="/static/mobile/video/work1.mp4" preload="none" autoplay muted loop playsinline webkit-playsinline class="work-video js-lazy-video" title="犀利士Cialis抑制PDE5酵素過程演示" aria-label="動畫展示犀利士Cialis如何與PDE5酵素結合"></video>
                <div class="work-text">
                    <h3 class="work-title">第一步：精準抑制PDE5磷酸二酯酶</h3>
                    <p class="work-desc">犀利士Cialis治療勃起功能障礙 (ED) 的核心機轉在於競爭性抑制 PDE5 第五型磷酸二酯酶，防止其分解擴張血管的重要物質 cGMP，從而維持平滑肌放鬆狀態。</p>
                </div>
                <div class="down-box">
                    <svg class="downarrow-icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><use href="#icon-downarrow-icon"/></svg>
                </div>
            </li>
            <li class="work-item mon">
                <video data-src="/static/mobile/video/work2.mp4" preload="none" autoplay muted loop playsinline webkit-playsinline class="work-video js-lazy-video"></video>
                <div class="work-text">
                    <h3 class="work-title">第二步：促進海綿體血液循環</h3>
                    <p class="work-desc">當 PDE5 活性降低後，海綿體平滑肌放鬆，使血液能更大量、順暢地流向陰莖，這是達成堅挺勃起的關鍵基礎。</p>
                </div>
                <div class="down-box">
                    <svg class="downarrow-icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><use href="#icon-downarrow-icon"/></svg>
                </div>
            </li>
            <li class="work-item mon">
                <video data-src="/static/mobile/video/work3.mp4" preload="none" autoplay muted loop playsinline webkit-playsinline class="work-video js-lazy-video"></video>
                <div class="work-text">
                    <h3 class="work-title">第三步：穩定硬度並持久維持</h3>
                    <p class="work-desc">在性刺激輔助下，增加的血流量讓陰莖達到飽滿硬度，有助於在整個性愛過程中穩定維持狀態，讓性生活表現更穩定、更滿意。</p>
                </div>
            </li>
        </ol>
    </section>
    <section class="safe-medication">
        <h2 class="sec-title">犀利士用藥安全</h2>
        <section class="side-effects">
            <h3 class="snd-title">犀利士常見副作用</h3>
            <p class="side-effects-content">藥物皆有雙面性，雖然犀利士Cialis在全球臨床使用超過二十年，其安全性已獲得廣泛證實，但仍有可能出現副作用。PDE5 抑制劑的副作用可以說是血管壁的「試金石」，出現少量不適或明顯感受到副作用通常代表藥物已生效：身體正在適應血管擴張。了解<a class="inner-link" href="/sideeffects" title="犀利士Cialis副作用">犀利士Cialis副作用</a>能讓你更從容地掌控藥效，避免產生不必要的恐慌。以下是可能出現的副作用與應對措施：</p>
            <div class="side-effects-content-wrap">
                <article class="side-effect-card side-effect-card--level1">
                    <header class="side-effect-card__head">
                        <h4 class="side-effect-card__title">輕微反應</h4>
                        <p class="side-effect-card__subtitle">生理調節信號·常見於初次用藥</p>
                    </header>
                    <div class="side-effect-card__symptoms">
                        <p class="side-effect-card__label">症狀</p>
                        <ul class="side-effect-card__symptoms-list">
                            <li>臉部潮紅</li>
                            <li>短暫頭痛</li>
                            <li>鼻塞</li>
                            <li>胃部灼熱感</li>
                            <li>消化不良</li>
                        </ul>
                    </div>
                    <div class="side-effect-card__action">
                        <p class="side-effect-card__action-title">應對措施</p>
                        <p class="side-effect-card__action-text">這是血管擴張的正常現象。建議<strong>飲用 300-500cc 溫開水</strong>稀釋血液濃度，加速藥物代謝，並採取坐姿休息。若有輕微頭痛，通常在 1-2 小時內會隨著血液濃度降低而消失。</p>
                    </div>
                </article>
                <article class="side-effect-card side-effect-card--level2">
                    <header class="side-effect-card__head">
                        <h4 class="side-effect-card__title">中等反應</h4>
                        <p class="side-effect-card__subtitle">感官功能波動·發生率極低</p>
                    </header>
                    <div class="side-effect-card__symptoms">
                        <p class="side-effect-card__label">症狀</p>
                        <ul class="side-effect-card__symptoms-list">
                            <li>藍綠色偏色視覺（看物體帶藍光）</li>
                            <li>肌肉痠痛（背部或四肢）</li>
                            <li>心跳明顯加快</li>
                        </ul>
                    </div>
                    <div class="side-effect-card__action">
                        <p class="side-effect-card__action-title">應對措施</p>
                        <p class="side-effect-card__action-text">立即停止性行為，更換至通風良好的環境。<strong>深呼吸並保持情緒平穩</strong>，避免攝取咖啡因或酒精。若偏色情況持續超過 2 小時，下次服藥前請務必減半劑量並諮詢醫師。</p>
                    </div>
                </article>
                <article class="side-effect-card side-effect-card--level3">
                    <header class="side-effect-card__head">
                        <h4 class="side-effect-card__title">嚴重反應</h4>
                        <p class="side-effect-card__subtitle">生理紅線警告·必須立即就醫</p>
                    </header>
                    <div class="side-effect-card__symptoms">
                        <p class="side-effect-card__label">症狀</p>
                        <ul class="side-effect-card__symptoms-list">
                            <li>陰莖異常勃起（超過4小時且劇痛）</li>
                            <li>突然失明</li>
                            <li>聽力喪失</li>
                            <li>胸痛</li>
                            <li>呼吸困難</li>
                        </ul>
                    </div>
                    <div class="side-effect-card__action side-effect-card__action--urgent">
                        <p class="side-effect-card__action-title">應對措施</p>
                        <p class="side-effect-card__action-text"><strong>立即前往急診急救。</strong>異常勃起若不處理會導致海綿體永久性組織壞死。請清楚告知急診醫師你服用的藥物名稱與劑量，以利醫師進行正確且專業的臨床減壓處理。</p>
                    </div>
                </article>
            </div>
        </section>
        <section class="contraindications">
            <h3 class="snd-title">服用禁忌</h3>
            <h4 class="contraindications-title">絕對禁忌：嚴禁併用心臟病藥物（硝酸鹽類）</h4>
            <p class="contraindications-content">這是用藥安全的第一原則。犀利士Cialis絕對禁止與任何形式的硝酸鹽藥物（如：救心、舌下錠、硝化甘油）同時使用。兩者併用會導致全身血管劇烈擴張，引發致命性的低血壓休克。若你有心血管病史，服藥前必須經由醫師評估。</p>
            <h4 class="contraindications-title">嚴重藥物交互作用：血液稀釋劑的風險</h4>
            <p class="contraindications-content">犀利士Cialis具有擴張血管的特性，若你正在服用血液稀釋劑（如抗凝血劑）或降血壓藥物，兩者產生的交互作用會顯著增加內出血或嚴重頭暈的風險。「先診斷，後用藥」是保障安全的唯一途徑。</p>
            <h4 class="contraindications-title">每日劑量控制：避免過量與長期依賴</h4>
            <p class="contraindications-content">犀利士Cialis的單日最大劑量建議為 100mg。長期濫用會超過安全劑量，雖然短期內可能無感，但會加速心血管系統的耐受性，增加心肌梗塞、中風以及非動脈性視神經損傷的風險。性生活表現不應以透支健康為代價。</p>
            <h4 class="contraindications-title">生活方式協同：藥物只是輔助</h4>
            <p class="contraindications-content">勃起功能是血管健康的指標。除了依賴犀利士Cialis（CIALIS），您應致力於修復血管內皮，如：減肥、減少鹽分攝取與規律的有氧訓練。健康的身體能讓藥物在更低劑量下，發揮更穩定的硬度表現。</p>
            <p class="contraindications-content">每個人的身體狀況各異且壯陽藥選擇眾多，建議多與專業醫師溝通，在安全與效果之間找到最精準的平衡點。更多服用禁忌與用藥安全資訊，可前往<a class="inner-link" href="/contraindications" title="犀利士Cialis禁忌與注意事項">犀利士Cialis禁忌與注意事項</a>詳細了解。</p>
        </section>
        @if($sideeffects->isNotEmpty())
            <section class="further-reading">
                <h3 class="snd-title">延伸閱讀</h3>
                <x-news.sections.related-list
                    :items="$sideeffects"
                    :wrap-in-section="false"
                />
                <p class="further-reading__more-wrap">
                    <a href="/sideeffects" class="main-btn further-reading__more">更多犀利士副作用閱讀</a>
                </p>
            </section>
        @endif
    </section>
    <section class="health health-home mon">
        <h2 class="sec-title">兩性健康</h2>
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
        @if($healthReading->isNotEmpty())
            <ul class="news-wrap">
                @foreach($healthReading as $item)
                    <x-news.cards.news-card :item="$item" />
                @endforeach
            </ul>
            <p class="health-home__more-wrap">
                <a href="/health" class="main-btn health-home__more">更多兩性健康</a>
            </p>
        @endif
    </section>
    {{--@include('components.qa', ['faqs' => $faqs])--}}
@endsection
