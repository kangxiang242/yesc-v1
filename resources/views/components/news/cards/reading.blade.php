{{-- 備份：請改用 news-card.blade.php --}}
@props([
    'item',
    'titleTag' => 'h3',
    'contentTag' => 'article',
])

<li class="reading-card">
    <div class="card-liquid"></div>
    <div class="card-shine"></div>
    <div class="card-glow"></div>
    <{{ $contentTag }} class="card-content">
        <div class="card-image">
            <img
                src="{{ storage_url($item->thumbnail('800')) }}"
                sizes="(max-width: 768px) 100%, 800px"
                width="800"
                height="400"
                decoding="async"
                loading="lazy"
                fetchpriority="low"
                alt="{{ $item->title }}">
        </div>
        <div class="card-text">
            <a class="card-badge" href="{{ url($item->cate->uri) }}">{{ $item->cate->name }}</a>
            <{{ $titleTag }} class="card-title">{{ $item->title }}</{{ $titleTag }}>
            <p class="card-description">{{ Str::limit(strip_tags($item->content), 60) }}</p>
            <div class="more-box">
                <div class="views">
                    <svg class="viewicon" viewBox="0 0 1024 1024"><use href="#icon-viewicon"></use></svg>
                    {{ $item->real_read_num }}
                </div>
                <a href="{{ route('news.show',[$item->cate->uri,$item->id]) }}" class="morebtn">閱讀全文<svg class="arrowicon" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg></a>
            </div>
        </div>
    </{{ $contentTag }}>
</li>
