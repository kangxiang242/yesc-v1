@extends('web.layout')

@section('track-init')
<script>Track.init({ platform: 'web', page_type: 'news_list', category_uri: @json(request()->segment(1)) });</script>
@endsection

@section('style')
    @parent
@stop

@section('script')
    @parent
    <script>
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

    </script>
@stop

@section('landing_style')
    @vite(['resources/scss/landing.scss'])
@stop
@section('body-class', 'page-news')

@section('content')
    @include('components.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item">'.($cate->name ?? '').'</li>'])
    @if(isset($page) && $page)<h1 class="page-title">{{ $page->title }}</h1>@endif
    {{--@if(isset($page) && !empty($page->title))
        <header class="page-header">
            <h1 class="page-header-title">{{ $page->title }}</h1>
            @if(isset($page->desc) && !empty($page->desc))
                <p class="page-header-description">{{ $page->desc }}</p>
            @endif
        </header>
    @endif--}}

    {{-- 主题分类区块（从标签表读取，仅第一页显示） --}}
    @if(isset($topicsTags) && !empty($topicsTags) && ($news->currentPage() === 1))
        @php
            $wrapClass = 'news-wrap';
        @endphp
        <section class="topics">
            <h2 class="visually-hidden">主題分類</h2>
            @foreach($topicsTags as $tag)
                
                <section class="topic">
                    <h3 class="sec-title">{{ $tag['name'] }}</h3>
                    <p class="topic-content">{{ $tag['description'] }}</p>
                    @if(isset($tag['articles']) && count($tag['articles']))
                        <x-news.sections.related-list
                            :items="$tag['articles']"
                            :wrap-in-section="false"
                            list-class="{{ $wrapClass }}"
                            title-tag="h4"
                        />
                    @else
                        <ul>
                            <li>暂无文章</li>
                        </ul>
                    @endif
                </section>
            @endforeach
            
            @if(isset($pageContent) && !empty($pageContent))
                {!! $pageContent !!}
            @endif
        </section>
    @endif

    <!-- @php
        $isEffectMode = request()->is('effect*');
        $topNews = $news->first();
    @endphp
    @if($topNews)
        <article class="top-news">
            <a href="{{ route('news.show',[$topNews->cate->uri,$topNews->id]) }}" class="top-news__media" aria-label="{{ $topNews->title }}">
                <img
                    src="{{ storage_url($topNews->thumbnail('1200')) }}"
                    sizes="(max-width: 768px) 100vw, 50vw"
                    width="1200"
                    height="720"
                    decoding="async"
                    loading="auto"
                    alt="{{ $topNews->title }}">
            </a>
            <div class="top-news__content">
                <div class="top-news__labels">
                    <span class="top-news__label top-news__label--featured">精選閱讀</span>
                    <span class="top-news__label">{{ $topNews->cate->name }}</span>
                </div>
                <h2 class="top-news__title">{{ $topNews->title }}</h2>
                <p class="top-news__desc">{{ Str::limit(strip_tags($topNews->content), 160) }}</p>
                <a href="{{ route('news.show',[$topNews->cate->uri,$topNews->id]) }}" class="top-news__cta">閱讀全文<svg class="arrowicon" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg></a>
            </div>
            
        </article>
    @endif -->
    <section class="news-list">
        <h2 class="sec-title">更多閱讀</h2>
        @if(isset($filterTags) && count($filterTags) > 0)
            <div class="tag-filter">
                <a href="{{ url()->current() }}" class="tag-filter__btn{{ !$tagSlug ? ' is-active' : '' }}">全部</a>
                @foreach($filterTags as $tag)
                    <a href="{{ url()->current() }}?tag={{ $tag['slug'] }}" class="tag-filter__btn{{ $tagSlug === $tag['slug'] ? ' is-active' : '' }}">
                        {{ $tag['name'] }}
                        <span class="tag-filter__count">({{ $tag['count'] }})</span>
                    </a>
                @endforeach
            </div>
        @endif
        <x-news.sections.related-list
            :items="$news"
            :wrap-in-section="false"
            title-tag="h3"
        />

        <div class="list-pagination">
            {!! $news->links('vendor.pagination.bootstrap-5') !!}
        </div>
    </section>
    <div class="team-box">
        <img src="/static/img/team.webp" decoding="async" loading="lazy" alt="犀利士專業醫師團隊">
        <div class="team-text">
            <p class="team-title">犀利士專業醫師團隊</p>
            <p class="team-description">犀利士醫師團隊擁有豐富的臨床經驗，將專業的醫學知識與生活健康知識結合，分享相關的保健資訊與犀利士使用心得，並為您提供最專業的諮詢服務。</p>
            <p class="team-description">本網站內容由犀利士醫師編輯團隊負責整理與審核，專注於男性健康與ED藥物資訊，所有內容均基於公開醫學文獻（如 PubMed、FDA 指南）整理，並優先採用最新資訊，由內部編輯進行交叉審核，以確保所有內容皆具備準確性與時效性。若您發現任何錯誤，歡迎聯絡我們並指出。</p>
            <p class="team-description">醫療審閱聲明：本網站內容僅供健康資訊與衛教參考使用，並不構成任何形式之醫療診斷或治療建議，亦無法取代專業醫師之臨床判斷。如您有任何症狀、用藥需求或潛在疑慮，請務必諮詢合格醫師或醫療專業人員，以獲得安全之醫療建議。</p>
        </div>
    </div>
@endsection
