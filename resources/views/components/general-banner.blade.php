<section class="general-banner">

    <div class="slogan-box">
        <h2 class="h2-text">{!! $global_banner->title !!}</h2>
        <p>{{ $global_banner->desc }}</p>
    </div>

    <svg class="line"
        viewBox="0 0 300 200"
        preserveAspectRatio="none"
        xmlns="http://www.w3.org/2000/svg">

        <defs>
            <linearGradient id="aurora-gradient-v2" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" class="aurora-stop-start"/>
                <stop offset="100%" class="aurora-stop-end"/>
            </linearGradient>
        </defs>

        <path
            d="M0,50 C20,50 80,140 150,120 S250,100 300,120"
            class="inner-line"
            pathLength="1"
            stroke-dasharray="1"
            stroke-dashoffset="0"
            fill="none"
        />

        <path
            d="M0,200
            L0,50
            C20,50 80,140 150,120
            S250,100 300,120
            L300,200
            Z"
            class="inner-fill"
            opacity="1"
            fill="url(#aurora-gradient-v2)"
        />
    </svg>
    <div class="small-card">
        <div class="small-card-product">
            <img class="small-card-product-img" src="{{ storage_url($random_product->img) }}" alt="">
            <div class="small-card-product-content">
                <h3>犀利士Cialis<sup>®</sup> {{ $random_product->name }}</h3>
                <p>"{{ $random_product->subname }}"</p>
            </div>
        </div>
        <a href="{{ url('goods/'.$random_product->id) }}" class="main-btn">瞭解詳情<svg class="arrowicon" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg></a>
    </div>
    @if($global_banner->img)
    <img class="general-banner-img" src="{{ $global_banner->img }}" sizes="(max-width: 768px) 100%, 500px" width="500" height="293" loading="auto" decoding="async" crossorigin="anonymous" alt="犀利士Cialis特點">
    @endif
</section>
