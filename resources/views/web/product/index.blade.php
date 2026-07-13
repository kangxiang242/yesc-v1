@extends('web.layout')

@section('style')
    @parent
@stop

@section('landing_style')
    @vite(['resources/scss/home.scss', 'resources/js/app.js'])
@stop

@section('script')
    @parent
    <script src="{{ assetv('static/js/price-animator.js') }}"></script>
    <script>
        // 計算 flow-wrap 的高度：從第一個 box-container 的 shopt 到最後一個 box-container 的 shopt
        function calculateFlowWrapHeight() {
            const boxContainers = document.querySelectorAll('.box-shop .group-box');
            const flowWrap = document.querySelector('.flow-wrap');
            
            if (!flowWrap || boxContainers.length < 2) {
                return;
            }
            
            const lastIndex = boxContainers.length - 1;
            const firstShopt = boxContainers[0]?.querySelector('.shopt');
            const lastShopt = boxContainers[lastIndex]?.querySelector('.shopt');
            
            if (!firstShopt || !lastShopt) {
                return;
            }
            
            const firstShoptRect = firstShopt.getBoundingClientRect();
            const lastShoptRect = lastShopt.getBoundingClientRect();
            
            const height = lastShoptRect.top - firstShoptRect.top;
            
            // 使用 CSS 變量設置 flow-wrap 的高度
            flowWrap.style.setProperty('--flow-height', height + 'px');
        }
        
        // 頁面加載完成後執行（等待 DOM 和可能的動態內容加載）
        function initFlowWrap() {
            // 使用 setTimeout 確保所有內容都已渲染
            setTimeout(() => {
                calculateFlowWrapHeight();
            }, 100);
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFlowWrap);
        } else {
            initFlowWrap();
        }
        
        // 窗口大小改變時重新計算
        window.addEventListener('resize', calculateFlowWrapHeight);
    </script>

@stop

@section('body-class', 'page-product')
@section('content')
    @include('components.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item">訂購犀利士Cialis</li>'])
    <h1 class="page-title">犀利士線上訂購</h1>
    {{--<header class="page-header">
        <h1 class="page-header-title">犀利士最新價格與組合方案</h1>
        <p class="page-header-description">犀利士最新價格參考與組合推薦、線上訂購最高可節省65%</p>
    </header>--}}
   

    @foreach($groups as $group)
        <section class="box-container">
            <div class="product-area">
                <h2 class="shopt guide">{{ $group['title'] }}</h2>
                <p class="shopt-desc">{{ $group['des'] }}</p>
                @include('components.core-sec')
                <ol class="product-list watermark">
                    @foreach($group['items'] as $item)
                        <li class="product-card">
                            <a class="product-card-link" href="{{ url('goods/'.$item->id) }}">
                                <span class="original-label" aria-label="原裝進口">原裝進口</span>
                                
                                <img class="product-card-img" src="{{ storage_url($item->m_img?:$item->img) }}" loading="auto" decoding="async" width="380" height="260" alt="{{ $item->name }}">
                                <div class="preview-box">
                                    <img class="preview-img" src="/static/img/pill-prev1.webp" loading="auto" decoding="async" width="100" height="100" alt="犀利士Cialis藥錠外觀">
                                    <img class="preview-img" src="/static/img/pill-prev2.webp" loading="auto" decoding="async" width="100" height="100" alt="犀利士Cialis包裝">
                                    <img class="preview-img" src="/static/img/pill-prev3.webp" loading="auto" decoding="async" width="100" height="100" alt="犀利士Cialis原廠標籤">
                                </div>
                            </a>
                            <div class="product-card-info">
                                <h3 class="goods-title">犀利士Cialis 20mg
                                    <strong class="box-count">{{ $item->name }}<span class="box-num">{{ $item->quantity }}</span>盒</strong>
                                </h3>
                                {{--<ul class="goods-label-sec">
                                    <li class="goods-label">原廠正品</li>
                                    <li class="goods-label">現貨供應</li>
                                    <li class="goods-label">隱密包裝</li>
                                </ul>--}}
                                <p class="goods-subname">"{{ $item->subname }}"</p>
                                {{--<dl class="sub-sec">
                                    <dt class="sub-title">美國進口</dt>
                                    <dd class="sub-content">原廠標籤</dd>
                                    <dt class="sub-title">數量規格</dt>
                                    <dd class="sub-content">4顆/盒</dd>
                                    <dt class="sub-title">保質期限</dt>
                                    <dd class="sub-content">60個月</dd>
                                </dl>--}}

                                <div class="price-box" data-market-price="{{ round($item->market_price) }}" data-price="{{ round($item->price) }}">
                                    <div class="mk-price">
                                        <p class="grey-price">NT$ {{ number_format(round($item->market_price)) }}</p>
                                        <div class="discount-box">
                                            <p class="discount">-<span class="descount-num">{{ $item->discount_percent }}</span>%</p>
                                        </div>
                                    </div>
                                    
                                    <p class="red-price"><span class="twd">NT$</span><span class="price-number">{{ number_format(round($item->market_price)) }}</span></p>
                                </div>
                                <a class="main-btn" href="{{ url('goods/'.$item->id) }}">查看詳情<svg class="btn-icon buy-icon" viewBox="0 0 1055 1024"><use href="#icon-buyicon"></use></svg>
                                    @if($item->quantity >= 4)
                                        <div class="discount">
                                            <span class="discount-content">免運</span>
                                        </div>
                                    @endif 
                                </a>

                            </div>
                            {{--@include('components.secret')--}}
                        </li>
                    @endforeach
                </ol>
                @include('components.secret')
            </div>
        </section>
    @endforeach

    {{-- FAQ 常见问题 --}}
    @include('components.qa', ['faqs' => $faqs, 'headingLevel' => 3])

    {{--<section class="page-content">
        <h2 class="sec-title">關於犀利士Cialis價格</h2>
        <p class="sec-content">
        犀利士Cialis價格會因數量組合與與供應來源而有所差異，市面通常有單盒購買與多盒組合多種選擇。
        </p>
        <p class="sec-content">一般而言，多盒組合在平均每盒成本上較具優勢，訂購數量越多越節省預算，適合已有使用經驗或規律性需求者；而單盒或少量組合則較適合首次嘗試或偶爾性需求族群。在比較犀利士Cialis價格時，應同時考量性生活頻率、備用需求與訂購頻率。
        </p>
        <h3 class="sec-subtitle">為什麼犀利士Cialis 100mg 價格存在差異？</h3>
        <ul class="sec-list">
            <li><strong>劑量與包裝：</strong>標準 100mg 四錠裝與多盒經濟裝的單位成本不同。</li>
            <li><strong>物流與關稅：</strong>正品犀利士Cialis需經過嚴格商檢與國際空運，這些成本確保了藥效與安全。</li>
            <li><strong>通路安全性：</strong>官方授權通路的報價包含防偽驗證與隱私配送服務。</li>
        </ul>
        <p class="sec-content">
        此外，犀利士Cialis價格亦可能受到供應通路、運費與包裝方式影響。選擇組合方案時，除參考價格外，也應留意產品來源說明與配送方式，確保取得流程透明且來源清楚。本平台獲官方授權，提供透明可靠的犀利士Cialis原廠正品線上通路，價格已包含國際空運運費、關稅、商檢費等、另免費為你提供隱密包裝，讓您安心訂購。
        </p>
        <h3 class="sec-subtitle">如何選擇最划算的犀利士Cialis購買組合？</h3>
        <p class="sec-content">針對不同需求，我們建議參考以下選購思路：</p>
        <p class="sec-content">1. <strong>試用者：</strong>建議購買<a class="inner-link" href="{{ url('goods/1') }}" title="犀利士Cialis一盒">犀利士Cialis一盒</a>試用，以評估身體對 100mg 劑量的反應。</p>
        <p class="sec-content">2. <strong>長期使用者：</strong>選擇 3盒或<a class="inner-link" href="{{ url('goods/6') }}" title="犀利士Cialis六盒">犀利士Cialis六盒</a>以上的組合，單錠價格可降低 20%-35%，是預算最大化的最佳選擇。</p>
        <p class="sec-content">
        實際選擇何種盒數組合，仍建議依個人使用情況或醫療評估結果為準，不以單純價格高低作為唯一判斷依據。
        </p>
    </section>--}}

    @php
        $productItems = collect($groups)
            ->flatMap(function ($group) {
                return collect($group['items'] ?? []);
            })
            ->values();

        $productListSchema = [
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "name" => "犀利士Cialis價格與組合方案",
            "itemListElement" => $productItems->map(function ($item, $index) {
                $detailUrl = url('goods/' . $item->id);
                $imageUrl = storage_url($item->m_img ?: $item->img);
                $salePrice = number_format((float) $item->price, 0, '.', '');
                $listPrice = number_format((float) ($item->market_price ?? $item->price), 0, '.', '');

                return [
                    "@type" => "ListItem",
                    "position" => $index + 1,
                    "url" => $detailUrl,
                    "item" => [
                        "@type" => "Product",
                        "@id" => $detailUrl . "#product",
                        "name" => "禮來犀利士Cialis 20mg {$item->name}{$item->quantity}盒",
                        "image" => [$imageUrl],
                        "sku" => (string) $item->id,
                        "description" => strip_tags($item->subname ?: $item->name),
                        "brand" => [
                            "@type" => "Brand",
                            "name" => "Lilly"
                        ],
                        "offers" => [
                            "@type" => "Offer",
                            "url" => $detailUrl,
                            "priceCurrency" => "TWD",
                            "price" => $salePrice,
                            "priceSpecification" => [
                                [
                                    "@type" => "UnitPriceSpecification",
                                    "priceType" => "https://schema.org/ListPrice",
                                    "priceCurrency" => "TWD",
                                    "price" => $listPrice,
                                ],
                                [
                                    "@type" => "UnitPriceSpecification",
                                    "priceType" => "https://schema.org/SalePrice",
                                    "priceCurrency" => "TWD",
                                    "price" => $salePrice,
                                ],
                            ],
                            "itemCondition" => "https://schema.org/NewCondition"
                        ]
                    ]
                ];
            })->toArray(),
        ];

    @endphp

    @push('schema')
        <script type="application/ld+json">
            {!! json_encode($productListSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endpush

@endsection
