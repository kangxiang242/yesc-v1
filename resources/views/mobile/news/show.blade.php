@extends('mobile.layout')

@section('track-init')
<script>Track.init({ platform: 'mobile', page_type: 'news_detail', article_id: {{ $news->id }}, category_uri: @json(request()->segment(1)) });</script>
@endsection
@if($news->seo_title)
    @section('title', $news->seo_title)
@else
    @section('title', $news->title)
@endif

@if($news->seo_keyword)
    @section('keywords', $news->seo_keyword)
@endif

@if($news->seo_description)
    @section('description', $news->seo_description)
@endif

@section('og-title', $news->seo_title ?: $news->title)
@section('og-image', asset_upload($news->img))

@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ release_asset('static/mobile/css/article-desc.css') }}"/>
    <style>
        .acticle-content p{
            margin-bottom: 0.2rem!important;
        }
    </style>
@stop

@section('script')
    @parent

    {{-- Article Schema --}}
    <x-schema.article :article="$news" />

    {{-- Breadcrumb Schema --}}
    <x-schema.breadcrumb :items="[['name' => '首頁', 'url' => '/'], ['name' => $news->cate->name, 'url' => url($news->cate->uri)], ['name' => $news->title, 'url' => url($news->cate->uri.'/'.$news->id).'.html']]" />

    {{-- FAQPage JSON-LD --}}
    @php
        $articleFaqs = \App\Models\Faq::where('article_id', $news->id)->orderBy('sort', 'desc')->get();
        $cateFaqs = \App\Models\Faq::where('article_cate_id', $news->article_cate_id)->whereNull('article_id')->orderBy('sort', 'desc')->get();
        $faqs = $articleFaqs->merge($cateFaqs)->unique('id');
    @endphp
    <x-schema.faq-page :faqs="$faqs" />

@stop




@section('content')
    <div class="row">
        <div class="breadcrumb-box">
            <ul class="breadcrumb">
                <li><a href="/">首頁</a></li>
                <li><a href="{{ url($news->cate->uri) }}">{{ $news->cate->name }}</a></li>
                <li class="active">{{ \Illuminate\Support\Str::limit($news->title,25) }}</li>
            </ul>
        </div>
    </div>

        <div class="below">
        <div class="acticle-title">
            <h1>{{ $news->title }}</h1>
        </div>

        {{-- Answer-first 首屏摘要 --}}
        <x-article-summary :brief="$news->brief" />

        <div class="acticle-content" data-track-block="m_nd_content">
            {!! $news->content !!}
        </div>

        {{-- E-E-A-T 內容責任鏈 --}}
        <x-article-eeat :news="$news" />

        {{--<div class="row tag">
            {foreach name="hottag" item="ht"}
            <a href="/tag/{$ht->at_tagid}.html" class="tagItem" title="{$ht->tag->tag_name}">{$ht->tag->tag_name}</a>
            {/foreach}

        </div>--}}

        <div class="row last-next">
            <div class="last-next-news">
                <button type="button" class="last-next-button">上一篇</button>
                <a class="last-next-title" href="{{ url($prev->cate->uri.'/'.$prev->id) }}.html" >{{ $prev->title }}</a>
            </div>
            <div class="last-next-news">
                <button type="button" class="last-next-button">下一篇</button>
                <a class="last-next-title" href="{{ url($next->cate->uri.'/'.$next->id) }}.html" >{{ $next->title }}</a>
            </div>
        </div>

        <div data-track-block="m_nd_related">
        <div class="row">
            <div class="header-title clearfix">
                <div class="l-line"></div>
                <div class="shopping">
                    <p>最新內容</p>
                </div>
            </div>
            <div class="related">

                @foreach($newNews as $item)
                <div class="news">

                    <div class="text-box">
                        <p><a href="{{ url($item->cate->uri.'/'.$item->id) }}.html">{{ $item->title }}</a></p>
                    </div>
                    <div class="img-box">
                        <p><a href="{{ url($item->cate->uri.'/'.$item->id) }}.html"><img src="{{ asset_upload($item->img) }}" alt="{{ $item->title }}"></a></p>
                    </div>
                    <div class="clearfix"></div>
                </div>
                @endforeach

            </div>
        </div>

        <div class="row">
            <div class="header-title clearfix">
                <div class="l-line"></div>
                <div class="shopping">
                    <p>相關閱讀</p>
                </div>
            </div>
            <div class="related">
                @foreach($top as $item)
                    <div class="news">

                        <div class="text-box">
                            <p><a href="{{ url($item->cate->uri.'/'.$item->id) }}.html">{{ $item->title }}</a></p>
                        </div>
                        <div class="img-box">
                            <p><a href="{{ url($item->cate->uri.'/'.$item->id) }}.html"><img src="{{ asset_upload($item->img) }}" alt="{{ $item->title }}"></a></p>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                @endforeach
            </div>
        </div>
        </div>

    </div>






@endsection
