@extends('mobile.layout')

@section('track-init')
<script>Track.init({ platform: 'mobile', page_type: 'message' });</script>
@endsection

@section('style')
    @parent

    <link rel="stylesheet" type="text/css" href="{{ release_asset('/static/mobile/css/check.css') }}"/>

@stop

@section('script')
    @parent
    <script src="{{ asset('static/js/jquery.contip.js') }}"></script>
    <script src="{{ asset('static/js/sweetalert2.js') }}"></script>
    <script src="{{ release_asset('static/js/api.js')}}"></script>
@stop



@section('content')

    <div class="row">
        <div class="breadcrumb-box">
            <ul class="breadcrumb">
                <li><a href="/">首頁</a></li>
                <li class="active">聯繫我們</li>
            </ul>
        </div>
    </div>
    <div class="row " data-track-block="m_msg_form">
        <form method="post" action="" onsubmit="return messageStore()" id="message-form">
            {!! csrf_field() !!}

            <div class="box-query">
                <div class="form-row">
                    <label>暱稱:</label>
                    <input type="text" name="name" value="" autocomplete="off" placeholder="請輸入暱稱">
                </div>

                <div class="form-row">
                    <label>郵箱:</label>
                    <input type="email" name="email" value="" autocomplete="off" placeholder="請輸入郵箱">
                </div>

                <div class="form-row">
                    <textarea  name="content" rows="" cols="" autocomplete="off" placeholder="請輸入您的留言或提出意見,我們將盡快回復!"></textarea>
                </div>

                <div class="form-row text-align-center">
                    <button class="form-btn" type="submit">確認提交</button>
                </div>


            </div>
        </form>
    </div>

@endsection
