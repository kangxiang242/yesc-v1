@extends('mobile.layout')

@section('track-init')
<script>Track.init({ platform: 'mobile', page_type: 'order_check' });</script>
@endsection

@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ release_asset('/static/mobile/css/check.css') }}"/>
@stop

@section('script')
    @parent
    <script src="{{ asset('static/js/jquery.contip.js') }}"></script>
    <script src="{{ asset('static/js/sweetalert2.js') }}"></script>
    <script src="{{ asset('static/js/api.js') }}"></script>

@stop



@section('content')


    <div class="row">
        <div class="breadcrumb-box">
            <ul class="breadcrumb">
                <li><a href="/">首頁</a></li>
                <li class="active">訂單查詢</li>
            </ul>
        </div>
    </div>

    <div class="row">

        <div class="box-query" data-track-block="m_oc_form">
            <form action="" id="check-form" method="post" onsubmit="return orderCheck()">
                {{ csrf_field() }}
                <div class="">

                    <div class="form-row">
                        <label>郵箱:</label>
                        <input type="email" name="email" value="" autocomplete="off" placeholder="請輸入訂購者郵箱">
                    </div>
                    <div class="form-row">
                        <label>電話:</label>
                        <input type="number" pattern="[0-9]*" name="phone" autocomplete="off" value="" placeholder="請輸入訂購者電話">
                    </div>


                    <div class="form-row check-botton text-align-center">
                        <p>為了保障您的個人隱私及安全，所有資料都會以密碼進行保護</p>
                        <button class="form-btn" type="submit">確認提交</button>
                    </div>
                </div>

                <div class="form-desc" data-track-block="m_oc_tips">
                    <div class="desc-box">
                        <p>我們可以幫您:</p>
                        <p>1.查詢您購買的商品明細</p>
                        <p>2.獲得客服人員對該訂單的處理情況</p>
                        <p>3.查詢該訂單的確實出貨的處理情況</p>
                        <p>4.您的個人資料並不會顯示，只對訂單資料作回應</p>
                    </div>
                </div>

                <div class="clearfix"></div>
            </form>
        </div>

    </div>


@endsection
