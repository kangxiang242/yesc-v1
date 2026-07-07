
<div class="row" data-track-block="m_component_banner">

    <!-- Swiper -->
    <div class="banner swiper-container" id="banner">
        <div class="swiper-wrapper">
            @foreach($banner->m_img as $img)
            <div class="swiper-slide"><a href="{{ $banner->href?url($banner->href):"javascript:;" }}" data-track="m_banner_click" data-track-zone="content" data-banner-id="{{ $banner->id ?? '' }}"><img src="{{ asset_upload($img) }}" alt="{{ $banner->alt?:"威而鋼" }}" /></a></div>
            @endforeach

        </div>

    </div>

</div>
