{{-- 備份：請改用 news-card.blade.php --}}
@props([
    'item',
    'titleTag' => 'h3',
    'rootTag' => 'li',
    'rootClass' => 'health-card',
    'viewIcon' => 'sprite',
])

<{{ $rootTag }} class="{{ $rootClass }}">
    <div class="card-img">
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
    <div class="bottom">
        <div class="card-text">
            <{{ $titleTag }} class="card-title">{{ $item->title }}</{{ $titleTag }}>
            <p class="card-description">{{ Str::limit(strip_tags($item->content), 60) }}</p>
            <div class="more-box">
                <div class="views">
                    @if($viewIcon === 'inline')
                        <svg t="1765509541614" class="viewicon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="27101" width="200" height="200"><path d="M272.384 921.6V376.2688c0-21.6576-15.57504-38.50752-33.8176-38.50752H136.22272c-18.688 0-33.8176 18.28864-33.8176 38.50752V921.6H272.384z m216.704 0V142.35136c0-21.66272-15.57504-39.95136-33.8176-39.95136H352.9216c-18.688 0-33.8176 18.28864-33.8176 39.95136V921.6h169.984z m216.25344 0v-344.62208c0-21.66272-15.56992-39.95136-33.8176-39.95136h-101.89824c-18.688 0-33.8176 18.28864-33.8176 39.95136V921.6h169.53344zM921.6 921.6V376.2688c0-21.6576-15.57504-38.50752-33.8176-38.50752h-101.89824c-18.688 0-33.8176 18.28864-33.8176 38.50752V921.6H921.6z" fill="currentColor" p-id="27102"></path></svg>
                    @else
                        <svg class="viewicon" viewBox="0 0 1024 1024"><use href="#icon-viewicon"></use></svg>
                    @endif
                    {{ $item->real_read_num }}
                </div>
                <a href="{{ route('news.show',[$item->cate->uri,$item->id]) }}" class="morebtn">閱讀全文<svg class="arrowicon" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg></a>
            </div>
        </div>
    </div>
</{{ $rootTag }}>
