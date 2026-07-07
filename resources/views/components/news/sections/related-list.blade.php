@props([
    'items' => [],
    'title' => null,
    'sectionClass' => null,
    'wrapInSection' => true,
    'titleTag' => 'h3',
    'listClass' => 'news-wrap',
])

@if($wrapInSection)
<section @if($sectionClass) class="{{ $sectionClass }}" @endif>
    @if(!empty($title))
        <h2 class="sec-title">{{ $title }}</h2>
    @endif
@endif

<ul class="{{ $listClass }}">
    @foreach($items as $item)
        <x-news.cards.news-card :item="$item" root-tag="li" :title-tag="$titleTag" />
    @endforeach
</ul>

@if($wrapInSection)
</section>
@endif
