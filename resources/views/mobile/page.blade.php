@extends('mobile.layout')

@section('track-init')
<script>Track.init({ platform: 'mobile', page_type: 'cms', cms_uri: @json(request()->segment(1)) });</script>
@endsection

@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ release_asset('/static/mobile/css/article-desc.css') }}"/>
    <style>
        .duanluo{
            margin-bottom: 1rem;
        }
        .acticle-content{
            margin-top: 0;
        }
        .acticle-content img {
            width: 4.2rem;
        }
        .acticle-content p{
            font-size: 0.28rem;
        }
        .acticle-title h1{
            font-size: 0.48rem;
            font-weight: 500;
            color: rgba(43,27,31,1);
            line-height: 0.6rem;

            text-align: center;
        }
    </style>
@stop

@section('content')

    <div class="row">
        <div class="breadcrumb-box">
            <ul class="breadcrumb">
                <li><a href="/">首頁</a></li>
                <li class="active">{{ $page->title }}</li>
            </ul>
        </div>
    </div>

    <div class="below">
        <div class="acticle-title">
            <h1>{{ $page->title }}</h1>
        </div>


        <div class="acticle-content" style="border: none" data-track-block="m_cms_content">
            {!! $page->content !!}
        </div>


    </div>

@endsection
