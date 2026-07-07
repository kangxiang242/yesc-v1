@props([
    'title',
    'items' => [],
    'titleTag' => 'h3',
    'contentTag' => 'article',
    'listClass' => 'news-wrap',
])

<section class="reading">
    <h2 class="sec-title">{{ $title }}</h2>
    <ul class="{{ $listClass }}">
        @foreach($items as $item)
            <x-news.cards.news-card
                :item="$item"
                :title-tag="$titleTag"
                root-tag="li"
            />
        @endforeach
    </ul>
</section>
