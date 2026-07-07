@props([
    'item',
    'titleTag' => 'h3',
    'rootTag' => 'li',
    'rootClass' => 'news-card',
])

<{{ $rootTag }} class="{{ $rootClass }}">
    <div class="card-img">
        <p class="card-badge">{{ $item->cate->name }}</p>
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
</{{ $rootTag }}>
