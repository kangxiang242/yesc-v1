@extends('web.layout')

@section('track-init')
<script>Track.init({ platform: 'web', page_type: 'cms', cms_uri: @json(request()->segment(1)) });</script>
@endsection

@section('style')
    @parent
@stop

@section('landing_style')
    @vite(['resources/scss/landing.scss'])
@stop


@section('script')
    @parent
    <script>
        (() => {
            const content = document.getElementById('spageContent');
            const list = document.querySelector('.summary-list');
            if (!content || !list) return;

            const articleSummary = document.getElementById('articleSummary');
            const summaryWrap = document.querySelector('.summary-fixed');
            const summaryMask = document.querySelector('.summary-mask');
            const summarySwitch = document.querySelector('.summary-switch');
            const summaryTitle = document.querySelector('.summary-title');
            const mainHeader = document.querySelector('.main-header');
            const defaultSummaryTitle = '閱讀導覽';
            let summaryTitleFadeTimer = 0;

            const headings = Array.from(content.querySelectorAll('h2'));
            if (!headings.length) return;
            content.querySelectorAll('h2').forEach((heading) => heading.classList.add('sec-title'));

            const links = list.querySelectorAll('a');
            const headerOffset = 100;

            function getActiveTitleText() {
                const activeLink = list.querySelector('a.active');
                if (!activeLink) return '';
                return activeLink.textContent.trim();
            }

            function isMobileSummaryMode() {
                return !!summarySwitch && window.getComputedStyle(summarySwitch).display !== 'none';
            }

            function isSummaryStickyActive() {
                if (!summaryWrap || !isMobileSummaryMode()) return false;
                const stickyTop = parseFloat(window.getComputedStyle(summaryWrap).top) || 0;
                const rect = summaryWrap.getBoundingClientRect();
                const tolerance = 1;
                const isSticky = window.pageYOffset > 0 && rect.top <= stickyTop + tolerance;
                const isVisible = rect.bottom > 0 && rect.top < window.innerHeight;
                return isSticky && isVisible;
            }

            function isSummaryExpanded() {
                return !!summaryWrap && summaryWrap.classList.contains('is-expanded');
            }

            function setSummaryTitle(nextText, useStickyStyle) {
                if (!summaryTitle) return;
                const currentText = summaryTitle.textContent || '';
                const currentSticky = summaryTitle.classList.contains('title-sticky');

                if (currentText === nextText && currentSticky === useStickyStyle) return;

                if (summaryTitleFadeTimer) {
                    clearTimeout(summaryTitleFadeTimer);
                    summaryTitleFadeTimer = 0;
                }

                summaryTitle.classList.add('is-fade-in');
                summaryTitle.textContent = nextText;
                summaryTitle.classList.toggle('title-sticky', useStickyStyle);
                summaryTitleFadeTimer = window.setTimeout(() => {
                    summaryTitle.classList.remove('is-fade-in');
                    summaryTitleFadeTimer = 0;
                }, 180);
            }

            function updateSummaryStickyState() {
                if (!articleSummary) return;

                if (!isMobileSummaryMode()) {
                    articleSummary.classList.remove('is-top');
                    document.body.classList.remove('summary-open');
                    return;
                }

                const stickyActive = isSummaryStickyActive();
                articleSummary.classList.toggle('is-top', stickyActive);
                document.body.classList.toggle('summary-open', isSummaryExpanded() && stickyActive);
                if (mainHeader && stickyActive) {
                    mainHeader.classList.remove('shadow');
                }
            }

            function updateReadingProgress() {
                if (!mainHeader || !content) return;

                const contentRect = content.getBoundingClientRect();
                const contentTop = contentRect.top + window.pageYOffset;
                const contentBottom = contentTop + contentRect.height;
                const headerHeight = mainHeader ? mainHeader.offsetHeight : 60;
                const readPosition = window.pageYOffset + headerHeight;
                const total = contentBottom - contentTop;

                let progress = 0;
                if (total > 0) {
                    progress = ((readPosition - contentTop) / total) * 100;
                } else {
                    progress = 100;
                }

                progress = Math.min(100, Math.max(0, progress));
                mainHeader.style.setProperty('--news-read-progress', String(progress / 100));
            }

            function updateSummaryTitle() {
                if (!summaryWrap || !summaryTitle || !isMobileSummaryMode()) {
                    if (summaryTitle) {
                        setSummaryTitle(defaultSummaryTitle, false);
                    }
                    return;
                }

                if (summaryWrap.classList.contains('is-collapsed')) {
                    if (!isSummaryStickyActive()) {
                        setSummaryTitle(defaultSummaryTitle, false);
                        return;
                    }
                    setSummaryTitle(getActiveTitleText() || defaultSummaryTitle, true);
                    return;
                }

                setSummaryTitle(defaultSummaryTitle, false);
            }

            function setSummaryCollapsed(collapsed) {
                if (!summaryWrap) return;
                const expanded = !collapsed;

                summaryWrap.classList.toggle('is-collapsed', collapsed);
                summaryWrap.classList.toggle('is-expanded', expanded);

                if (summarySwitch) {
                    summarySwitch.classList.toggle('is-on', expanded);
                    summarySwitch.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                }

                updateSummaryTitle();
                updateSummaryStickyState();
            }

            let lastMobileMode = isMobileSummaryMode();
            function syncSummaryByViewport() {
                if (!summaryWrap) return;
                const currentMobileMode = isMobileSummaryMode();

                if (currentMobileMode !== lastMobileMode) {
                    setSummaryCollapsed(currentMobileMode);
                    lastMobileMode = currentMobileMode;
                    updateSummaryStickyState();
                    return;
                }

                if (!currentMobileMode) {
                    setSummaryCollapsed(false);
                    updateSummaryStickyState();
                    return;
                }

                updateSummaryTitle();
                updateSummaryStickyState();
            }

            function updateVisibleBySection() {
                const viewTop = window.pageYOffset + headerOffset;
                const viewBottom = window.pageYOffset + window.innerHeight;

                headings.forEach((heading, i) => {
                    const id = heading.id;
                    if (!id) return;

                    const rect = heading.getBoundingClientRect();
                    const sectionTop = rect.top + window.pageYOffset;
                    const sectionBottom = i + 1 < headings.length
                        ? headings[i + 1].getBoundingClientRect().top + window.pageYOffset
                        : content.getBoundingClientRect().bottom + window.pageYOffset;

                    const visible = sectionTop < viewBottom && sectionBottom > viewTop;
                    links.forEach(link => {
                        const href = link.getAttribute('href');
                        const linkId = href && href.startsWith('#') ? href.slice(1) : '';
                        if (linkId === id) link.classList.toggle('active', visible);
                    });
                });

                updateSummaryTitle();
            }

            let tick = 0;
            function onScrollOrResize() {
                if (tick) return;
                tick = requestAnimationFrame(() => {
                    updateVisibleBySection();
                    updateSummaryStickyState();
                    updateReadingProgress();
                    tick = 0;
                });
            }

            window.addEventListener('scroll', onScrollOrResize, { passive: true });
            window.addEventListener('resize', () => {
                onScrollOrResize();
                syncSummaryByViewport();
            });

            updateVisibleBySection();
            syncSummaryByViewport();
            updateReadingProgress();

            if (summarySwitch) {
                summarySwitch.addEventListener('click', () => {
                    if (!isMobileSummaryMode()) return;
                    const willCollapse = !summaryWrap || !summaryWrap.classList.contains('is-collapsed');
                    setSummaryCollapsed(willCollapse);
                });
            }

            if (summaryMask) {
                summaryMask.addEventListener('click', () => {
                    if (!isMobileSummaryMode()) return;
                    if (summaryWrap && summaryWrap.classList.contains('is-collapsed')) return;
                    setSummaryCollapsed(true);
                });
            }

            window.addEventListener('keydown', (e) => {
                if (e.key !== 'Escape') return;
                if (!isMobileSummaryMode() || !isSummaryExpanded()) return;
                setSummaryCollapsed(true);
            });

            window.addEventListener('popstate', () => {
                if (!isMobileSummaryMode() || !isSummaryExpanded()) return;
                setSummaryCollapsed(true);
            });

            list.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link) return;

                const href = link.getAttribute('href');
                const target = href && href.startsWith('#') ? document.querySelector(href) : null;
                if (!target) return;

                e.preventDefault();
                const top = target.getBoundingClientRect().top + window.pageYOffset - headerOffset;
                window.scrollTo({ top, behavior: 'smooth' });
                if (history.pushState) history.pushState(null, '', href);

                if (isMobileSummaryMode() && summaryWrap && !summaryWrap.classList.contains('is-collapsed')) {
                    setSummaryCollapsed(true);
                }
            });
        })();
    </script>

    @if(request()->is('promise'))
    <script>
        (() => {
            if (!window.matchMedia('(max-width: 768px)').matches) return;
            const cards = document.querySelectorAll('.below-card');
            if (!cards.length) return;

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

                cards.forEach(card => card.classList.remove('focus'));
                if (closestCard) {
                    closestCard.classList.add('focus');
                }
            }

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

            updateActiveCard();
        })();
    </script>
    @endif

@stop

@section('body-class')
    page-spage{{ request()->is('promise') ? ' page-promise' : '' }}
@endsection
@section('content')
    @if(request()->is('promise'))
        {{-- Promise 頁：標題浮在封面上，麵包屑在封面下方 --}}
        <header class="article-header cover-header">
            <h1 class="page-header-title">犀利士承諾</h1>
        </header>
        <div class="below-cover">
        @include('components.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item">'.$page->title.'</li>'])
    @else
        @include('components.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item">'.$page->title.'</li>'])
    @endif

    <article class="article">
        @if(!request()->is('promise'))
        <header class="article-header">
            <h1 class="page-header-title">{{ $page->title }}</h1>
            @if(!empty($page->desc))
                <p class="page-header-description">{{ $page->desc }}</p>
            @endif
        </header>
        @endif

        @if(!request()->is('promise'))
        <div class="summary-fixed is-collapsed">
            <nav class="article-summary" id="articleSummary">
                <div class="summary-header">
                    <p class="summary-title">閱讀導覽</p>
                    <button class="summary-switch" type="button" aria-label="切換閱讀導覽" aria-expanded="false">
                        <span class="summary-switch__face summary-switch__face--on"><svg class="summary-switch__icon summary-switch__icon--collapse" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg>收起</span>
                        <span class="summary-switch__face summary-switch__face--off"><svg class="summary-switch__icon" viewBox="0 0 1024 1024"><use href="#icon-summary-expand"></use></svg>展開</span>
                    </button>
                </div>
                <ol class="summary-list">
                    @foreach ($toc as $item)
                        <li class="summary-item summary-h2">
                            <a href="#{{ $item['id'] }}">
                                {{ $item['title'] }}<svg class="arrowicon" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg>
                            </a>

                            @if (!empty($item['children']))
                                <ol class="summary-sublist">
                                    @foreach ($item['children'] as $child)
                                        <li class="summary-item summary-h3">
                                            <a href="#{{ $child['id'] }}">
                                                {{ $child['title'] }}<svg class="arrowicon" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg>
                                            </a>
                                        </li>
                                    @endforeach
                                </ol>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
            <button type="button" class="summary-mask" aria-label="收起閱讀導覽"></button>
        </div>
        @endif

        @if(!request()->is('promise'))
        <section class="article-content" id="spageContent" data-track-scroll-target data-track-section-view data-track-section="cms.content" data-track-section-label="CMS正文">
            {!! $page->content !!}
        </section>

        <footer class="team-box">
            <img src="/static/img/team.webp" decoding="async" loading="lazy" alt="犀利士專業醫師團隊">
            <div class="team-text">
                <p class="team-title">犀利士專業醫師團隊</p>
                <p class="team-description">犀利士醫師團隊擁有豐富的臨床經驗，將專業的醫學知識與生活健康知識結合，分享相關的保健資訊與犀利士使用心得，並為您提供最專業的諮詢服務。</p>
                <p class="team-description">本網站內容由犀利士醫師編輯團隊負責整理與審核，專注於男性健康與ED藥物資訊，所有內容均基於公開醫學文獻（如 PubMed、FDA 指南）整理，並優先採用最新資訊，由內部編輯進行交叉審核，以確保所有內容皆具備準確性與時效性。若您發現任何錯誤，歡迎聯絡我們並指出。</p>
                <p class="team-description">醫療審閱聲明：本網站內容僅供健康資訊與衛教參考使用，並不構成任何形式之醫療診斷或治療建議，亦無法取代專業醫師之臨床判斷。如您有任何症狀、用藥需求或潛在疑慮，請務必諮詢合格醫師或醫療專業人員，以獲得安全之醫療建議。</p>
            </div>
        </footer>
        @endif

        @if(isset($page->topics_data) && !empty($page->topics_data))
        <section class="news-topic-sections" aria-label="{{ $page->topics_title ?? '主题分类' }}">
            @if(isset($page->topics_title) && !empty($page->topics_title))
                <h2 class="sec-title">{{ $page->topics_title }}</h2>
            @endif
            @foreach($page->topics_data as $topic)
                <section class="news-topic-card" @if(isset($topic['tag'])) data-tag="{{ $topic['tag'] }}" @endif>
                    <h2>{{ $topic['title'] }}</h2>
                    <p>{{ $topic['description'] }}</p>
                    @if(isset($topic['articles']) && count($topic['articles']))
                        <ul>
                            @foreach($topic['articles'] as $article)
                                <li>
                                    <a href="{{ route('news.show', [$article->cate->uri, $article->id]) }}">
                                        {{ Str::limit($article->title, 30) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <ul>
                            <li>暂无文章</li>
                        </ul>
                    @endif
                </section>
            @endforeach
        </section>
        @endif

    </article>

    <!-- 底部内容区块 -->
    @if(!request()->is('promise') && isset($page->bottom_html) && !empty($page->bottom_html))
        <section class="page-bottom-content">
            {!! $page->bottom_html !!}
        </section>
    @endif

    @if(!request()->is('promise') && isset($faqs) && count($faqs))
        @include('components.qa', ['faqs' => $faqs])
    @endif

    @yield('page-extra')

    @if(request()->is('promise'))
        <ul class="promise-icon">
            <li class="below-card">
                <div class="content">
                    <div class="content-icon"><svg class="promiseicon" viewBox="0 0 1024 1024"><use href="#icon-promiseicon-1"></use></svg></div>
                    <h2 class="heading"><a href="/">犀利士Cialis</a>品質保證</h2>
                    <p class="para">我們的犀利士Cialis產品是禮來公司生產的，保證全部100%為原廠正品，質量有保證。商品售出前都會先經過詳細檢查完好才會出貨。並小心細緻的包裝商品，避免商品在運輸過程有所破損。如果您於收到商品時，有損毀而無法使用，請與本站的客服人員聯絡，我們會立即為您更換同款新品。</p>
                </div>
            </li>
            <li class="below-card">
                <div class="content">
                    <div class="content-icon"><svg class="promiseicon" viewBox="0 0 1024 1024"><use href="#icon-promiseicon-2"></use></svg></div>
                    <h2 class="heading">犀利士Cialis商品享有7日鑑賞期(包含假日)</h2>
                    <p class="para">鑑賞期係供您參考、觀賞、品鑑、比較，請保持 /原商品包裝/商品/說明書/標籤/ 的整體完整，缺一恕無法為您辦理退換貨。所以，消費者購買犀利士Cialis產品若不確定是否使用時，請勿拆開犀利士Cialis包裝封套，因其性質上無法回復原狀，若拆開包裝封套鑑賞期將無法適用。</p>
                </div>
            </li>
            <li class="below-card">
                <div class="content">
                    <div class="content-icon"><svg class="promiseicon" viewBox="0 0 1024 1024"><use href="#icon-promiseicon-3"></use></svg></div>
                    <h2 class="heading">犀利士Cialis隱秘包裝</h2>
                    <p class="para">我們的犀利士Cialis商品都會有精美的包裝盒包裝，從表面完全看不出是什麼產品，包括運送聯單上的公司名稱僅填寫"XX公司" 內容物欄也僅填寫"禮品" 字樣，這樣可以避免客戶收貨時的尷尬局面。我們保證內容物只有我們與您知道。</p>
                </div>
            </li>
            <li class="below-card">
                <div class="content">
                    <div class="content-icon"><svg class="promiseicon" viewBox="0 0 1024 1024"><use href="#icon-promiseicon-4"></use></svg></div>
                    <h2 class="heading">犀利士Cialis安全保密</h2>
                    <p class="para">由於本站出售的犀利士Cialis商品具有特殊性，本站將絕對保護顧客"個人隱私"，這是我們最基本的承諾!本站尊重顧客的資料和私隱權。承諾在遵守個人資料(私隱)條例的規定時，將完全符合國際認可的保障個人資料私隱(準則)，並確保公司職員遵守最嚴格的安全和保密準則。</p>
                </div>
            </li>
            <li class="below-card">
                <div class="content">
                    <div class="content-icon"><svg class="promiseicon" viewBox="0 0 1024 1024"><use href="#icon-promiseicon-5"></use></svg></div>
                    <h2 class="heading">犀利士Cialis付款方式</h2>
                    <p class="para">由於現在的詐欺手法層出不窮，近來各大入口網站、購物及拍賣網站皆發生類似新聞事件詐欺手法，因此，為維護顧客的消費安全；本站付款方式一律使用貨到付款，請安心購物。</p>
                </div>
            </li>
            <li class="below-card">
                <div class="content">
                    <div class="content-icon"><svg class="promiseicon" viewBox="0 0 1445 1024"><use href="#icon-promiseicon-6"></use></svg></div>
                    <h2 class="heading">犀利士Cialis產品價格與運費說明</h2>
                    <p class="para">因為我們的產品犀利士Cialis除了產品進價成本以外，還有國際快遞空運運費、關稅、商檢費用等成本，所以部份產品犀利士Cialis售價會比原產地高一些，這點請大家要多多包涵。我們的犀利士Cialis定價盡量跟您直接向國外訂購加上運費後的價格差不多，有些甚至更便宜！</p>
                </div>
            </li>
        </ul>
        </div>{{-- /.below-cover --}}
    @endif

@endsection
