@extends('web.layout')

@section('robots', 'noindex,nofollow')

@section('track-init')
<script>Track.init({ platform: 'web', page_type: 'order_verify' });</script>
@endsection

@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ release_asset('static/less/order.css')}}"/>
    <style>
        .verify-wrap {
            max-width: 500px;
            margin: 60px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .verify-wrap h3 {
            font-size: 18px;
            text-align: center;
            margin-bottom: 8px;
            color: #333;
        }
        .verify-wrap .desc {
            font-size: 13px;
            text-align: center;
            color: #999;
            margin-bottom: 24px;
        }
        .verify-wrap .form-group {
            margin-bottom: 16px;
        }
        .verify-wrap .form-group label {
            display: block;
            font-size: 14px;
            color: #666;
            margin-bottom: 6px;
        }
        .verify-wrap .form-group input {
            width: 100%;
            height: 44px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 0 12px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .verify-wrap .form-group input:focus {
            border-color: #3490e3;
            outline: none;
        }
        .verify-wrap .or-divider {
            text-align: center;
            color: #ccc;
            font-size: 13px;
            margin: 12px 0;
            position: relative;
        }
        .verify-wrap .or-divider::before,
        .verify-wrap .or-divider::after {
            content: '';
            display: inline-block;
            width: 40%;
            height: 1px;
            background: #eee;
            vertical-align: middle;
        }
        .verify-wrap .or-divider span {
            display: inline-block;
            padding: 0 10px;
        }
        .verify-btn {
            display: block;
            width: 100%;
            height: 44px;
            background: #3490e3;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
        .verify-btn:hover {
            background: #2878c7;
        }
        .verify-error {
            color: #e63434;
            font-size: 13px;
            text-align: center;
            margin-top: 10px;
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
    <div class="container" style="padding-bottom: 100px">
        <div class="wrapper">
            <div class="verify-wrap">
                <h3>訂單查詢驗證</h3>
                <p class="desc">為保護您的個資，請輸入電話末3碼或Email末4碼進行驗證</p>
                <p style="text-align:center;font-size:14px;color:#666;margin-bottom:20px;">
                    訂單號：<strong>{{ $order->no }}</strong>
                </p>

                <div class="form-group">
                    <label>電話號碼末 3 碼</label>
                    <input type="text" name="phone_last3" maxlength="3" placeholder="請輸入電話末3碼">
                </div>

                <div class="or-divider"><span>或</span></div>

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
