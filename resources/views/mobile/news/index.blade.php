@extends('mobile.layout')

@section('track-init')
<script>Track.init({ platform: 'mobile', page_type: 'news_list', category_uri: @json(request()->segment(1)) });</script>
@endsection

@section('style')
    @parent
    @if($cate->id == 1)
        <link rel="stylesheet" type="text/css" href="{{ release_asset('/static/mobile/css/effect.css') }}"/>
    @elseif($cate->id == 2)
        <link rel="stylesheet" type="text/css" href="{{ release_asset('/static/mobile/css/untoward.css') }}"/>
    @else
        <link rel="stylesheet" type="text/css" href="{{ release_asset('/static/mobile/css/question.css') }}"/>
    @endif

    <link rel="stylesheet" type="text/css" href="{{ release_asset('/static/mobile/css/paginate.css') }}"/>
    <style>
        .mescroll{
            margin-top: 0.5rem;
            /*position: absolute;*/
            top: 5.5rem;
            bottom: 0;
            /*height: 9rem;*/
            max-width: 768px;
            z-index: 999999;
        }
    </style>
@stop

@section('script')
    @parent

@stop




@section('content')

    <div class="row">
        <div class="breadcrumb-box">
            <ul class="breadcrumb">
                <li><a href="/">首頁</a></li>
                <li class="active">{{ $cate->name }}</li>
            </ul>
        </div>
    </div>
    @if($cate->id == 1)
        <div class="row" >
            <div class="news-row" data-track-block="m_nl_list">

                @foreach($news as $item)
                    <div class="news-box clearfix">
                        <div class="new-img"><a href="{{ url($cate->uri.'/'.$item->id) }}.html"><img src="{{ asset_upload($item->img) }}" alt="{{ $item->title }}"></a></div>
                        <div class="new-text"><a href="{{ url($cate->uri.'/'.$item->id) }}.html"><p class="main-title">{{ $item->title }}</p></a></div>
                        <div class="new-button"><a href="{{ url($cate->uri.'/'.$item->id) }}.html">查看全文 ></a></div>
                    </div>
                @endforeach

            </div>
        </div>
    @elseif($cate->id == 2)
        <div class="row untoward" >
            <div class="article-row" data-track-block="m_nl_article_grid">

                @foreach($news as $item)
                    <div class="item clearfix">
                        <div class="text"> <p><a href="{{ url($cate->uri.'/'.$item->id) }}.html">{{ $item->title }}</a></p> </div>
                        <div class="article-img"><a href="{{ url($cate->uri.'/'.$item->id) }}.html"> <img src="{{ asset_upload($item->img) }}" alt="{{ $item->title }}"></a> </div>
                        <div class="date-time"><span>{{ $item->release_at->format('Y-m-d') }}</span></div>
                    </div>
                @endforeach
            </div>

        </div>
    @else
        <div class="row question" data-track-block="m_nl_faq">
            <div class="question-row">

                @foreach($news as $item)
                    <div class="item clearfix">
                        <div class="title">
                            <p><a href="{{ url($cate->uri.'/'.$item->id) }}.html">{{ $item->title }}</a></p>
                        </div>
                        <div class="text">
                            <p><a href="{{ url($cate->uri.'/'.$item->id) }}.html">{{ $item->brief?\Illuminate\Support\Str::limit($item->brief,80):\Illuminate\Support\Str::limit(strip_tags($item->content),80) }}</a></p>
                        </div>
                        <div class="question-img">
                            <a href="{{ url($cate->uri.'/'.$item->id) }}.html"><img src="{{ asset_upload($item->img) }}" alt="{{ $item->title }}"></a>
                        </div>
                        <div class="question-button">
                            <a href="{{ url($cate->uri.'/'.$item->id) }}.html"><button>閱讀全文</button></a>
                        </div>
                    </div>
                @endforeach

            </div>


        </div>
    @endif


    <div class="row clearfix" style="text-align: center;">
        {!! $news->links() !!}
    </div>



@endsection
