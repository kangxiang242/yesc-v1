
<div class="broadside-row" data-track-block="web_component_hot_articles">
    <div class="broadside">
        <div class="broadside-header">
            <p class="broadside-title">熱門内容</p>
        </div>
        <div class="broadside-content">
            @foreach($article as $item)
                <div class="broadside-news-item">
                    <a href="{{ url($item->cate->uri.'/'.$item->id) }}.html" >{{ $item->title }}</a>
                </div>
            @endforeach
        </div>
    </div>

</div>
