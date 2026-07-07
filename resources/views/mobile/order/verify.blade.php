@extends('mobile.layout')

@section('robots', 'noindex,nofollow')

@section('track-init')
<script>Track.init({ platform: 'mobile', page_type: 'order_verify' });</script>
@endsection

@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ release_asset('static/less/order.css')}}"/>
    <style>
        .verify-wrap {
            margin: 0.4rem 0.3rem;
            padding: 0.3rem;
            background: #fff;
            border-radius: 0.12rem;
        }
        .verify-wrap h3 {
            font-size: 0.28rem;
            text-align: center;
            margin-bottom: 0.1rem;
            color: #333;
        }
        .verify-wrap .desc {
            font-size: 0.22rem;
            text-align: center;
            color: #999;
            margin-bottom: 0.3rem;
        }
        .verify-wrap .order-no {
            text-align: center;
            font-size: 0.24rem;
            color: #666;
            margin-bottom: 0.3rem;
        }
        .verify-wrap .form-group {
            margin-bottom: 0.2rem;
        }
        .verify-wrap .form-group label {
            display: block;
            font-size: 0.24rem;
            color: #666;
            margin-bottom: 0.08rem;
        }
        .verify-wrap .form-group input {
            width: 100%;
            height: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 0.08rem;
            padding: 0 0.2rem;
            font-size: 0.28rem;
            box-sizing: border-box;
        }
        .verify-wrap .or-divider {
            text-align: center;
            color: #ccc;
            font-size: 0.22rem;
            margin: 0.15rem 0;
        }
        .verify-btn {
            display: block;
            width: 100%;
            height: 0.8rem;
            background: #3490e3;
            color: #fff;
            border: none;
            border-radius: 0.08rem;
            font-size: 0.28rem;
            margin-top: 0.3rem;
        }
        .verify-error {
            color: #e63434;
            font-size: 0.22rem;
            text-align: center;
            margin-top: 0.12rem;
            display: none;
        }
    </style>
@stop

@section('title','訂單驗證')

@section('script')
    @parent
    <script src="{{ release_asset('static/js/sweetalert2.js')}}"></script>
    <script>
        function doVerify() {
            var no = '{{ $order->no }}';
            var phoneLast3 = $('input[name="phone_last3"]').val();
            var emailLast4 = $('input[name="email_last4"]').val();

            if (!phoneLast3 && !emailLast4) {
                $('.verify-error').text('請輸入電話末3碼或Email末4碼').show();
                return false;
            }

            $.ajax({
                type: 'POST',
                url: '{{ url("order/verify") }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    no: no,
                    phone_last3: phoneLast3,
                    email_last4: emailLast4
                },
                dataType: 'json',
                success: function(res) {
                    if (res.code === 200) {
                        window.location.href = res.jump;
                    } else {
                        $('.verify-error').text(res.msg).show();
                    }
                },
                error: function() {
                    $('.verify-error').text('驗證失敗，請稍後重試').show();
                }
            });
            return false;
        }
    </script>
@stop

@section('content')
    <div class="container">
        <div class="wrapper">
            <div class="verify-wrap">
                <h3>訂單查詢驗證</h3>
                <p class="desc">為保護您的個資，請輸入電話末3碼或Email末4碼進行驗證</p>
                <p class="order-no">訂單號：<strong>{{ $order->no }}</strong></p>

                <div class="form-group">
                    <label>電話號碼末 3 碼</label>
                    <input type="text" name="phone_last3" maxlength="3" placeholder="請輸入電話末3碼">
                </div>

                <div class="or-divider">或</div>

                <div class="form-group">
                    <label>Email 末 4 碼</label>
                    <input type="text" name="email_last4" maxlength="4" placeholder="請輸入Email末4碼">
                </div>

                <button class="verify-btn" onclick="return doVerify()">驗證</button>
                <div class="verify-error"></div>
            </div>
        </div>
    </div>
@endsection
