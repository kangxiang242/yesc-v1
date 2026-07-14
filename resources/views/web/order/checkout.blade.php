@extends('web.layout')
@php
    $freight_where = \App\Services\ConfigService::get('freight_where',0);
    $freight_price = \App\Services\ConfigService::get('freight',0);

    $delivery_type_all = \App\Services\ConfigService::get('delivery_type',[]);
    if($delivery_type_all){
        $delivery_type_all = json_decode(\App\Services\ConfigService::get('delivery_type',[]),true);
    }
@endphp
@section('style')
    @parent
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex">
@stop




@section('title','快速結賬-'.$goods->name)
@section('body-class', 'page-checkout')

@section('script')
    @parent
    <script src="{{ asset('static/js/sweetalert2.js') }}"></script>
    {{-- <script src="{{ asset('static/js/xarea.js') }}?ver={{ get_setting('asset_version') }}"></script> --}}
    <style>
    /* 门店列表样式 */
    .form-store {
        margin-top: 0;
    }
    .store-item {
        display: block;
        margin-bottom: 12px;
        cursor: pointer;
    }
    .store-item:last-child {
        margin-bottom: 0;
    }
    .store-item input[type="radio"] {
        display: none;
    }
    .store-content {
        display: block;
    }
    .store-main {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border: 1px solid #C4C4C4;
        border-radius: 8px;
        gap: 12px;
        transition: all 0.2s ease;
    }
    .store-main .sevenicon {
        width: 40px;
        height: auto;
        flex-shrink: 0;
    }
    .store-info {
        flex: 1;
    }
    .store-name {
        font-size: 15px;
        font-weight: 500;
        color: #333;
        margin: 0 0 4px 0;
    }
    .store-address {
        font-size: 13px;
        color: #999;
        margin: 0;
    }
    .store-tips {
        display: none;
        margin: 8px 0 0 0;
        padding: 8px 12px;
        font-size: 13px;
        color: #4bb400;
        background: #f0fff0;
        border-radius: 6px;
    }
    .store-tips svg {
        vertical-align: middle;
        margin-right: 6px;
    }
    </style>
    <script>
        // 711门店获取接口 - 使用本地代理API解决CORS问题
        var apiBaseUrl = '/api/regionstore/proxy';
        var currentOrderType = 1;

        $(document).ready(function() {
            // 配送方式切换
            $('input[name="order_type"]').change(function() {
                currentOrderType = parseInt($(this).val());
                if (currentOrderType === 1) {
                    // 7-11 超商取货
                    $('#form-address-row').hide();
                    if ($('#street').val()) {
                        $('#form-store-row').show();
                    }
                } else {
                    // 黑猫宅配
                    $('#form-store-row').hide();
                    $('#form-address-row').show();
                }
            });

            // 初始化：默认7-11，隐藏地址输入
            $('#form-address-row').hide();

            // 加载县市（不传参数）
            $.get(apiBaseUrl, function(res) {
                if (res.code === 1 && res.data) {
                    res.data.forEach(function(item) {
                        $('#city').append('<option value="' + item.id + '">' + item.name + '</option>');
                    });
                }
            });

            // 县市联动地区
            $('#city').change(function() {
                var cityId = $(this).val();
                var cityName = $(this).find('option:selected').text(); // 使用名称而不是ID
                $('#city-hidden').val(cityName);
                $('#county').html('<option value="0">載入中...</option>').prop('disabled', true);
                $('#street').html('<option value="0">選擇路段</option>');
                $('#county-hidden').val('');
                $('#street-hidden').val('');
                $('#form-store-row').html('');

                if (cityId) {
                    $.get(apiBaseUrl + '?city_id=' + cityId, function(res) {
                        if (res.code === 1 && res.data) {
                            var options = '<option value="0">選擇地區</option>';
                            res.data.forEach(function(item) {
                                options += '<option value="' + item.id + '">' + item.name + '</option>';
                            });
                            $('#county').html(options).prop('disabled', false);
                        } else {
                            $('#county').html('<option value="0">選擇地區</option>').prop('disabled', false);
                        }
                    }).fail(function() {
                        $('#county').html('<option value="0">選擇地區</option>').prop('disabled', false);
                    });
                } else {
                    $('#county').html('<option value="0">選擇地區</option>').prop('disabled', false);
                }
            });

            // 地区联动路段
            $('#county').change(function() {
                var countyId = $(this).val();
                var countyName = $(this).find('option:selected').text(); // 使用名称
                var cityId = $('#city').val();
                $('#county-hidden').val(countyName);
                $('#street').html('<option value="0">載入中...</option>').prop('disabled', true);
                $('#street-hidden').val('');
                $('#form-store-row').html('');

                if (countyId && cityId) {
                    $.get(apiBaseUrl + '?city_id=' + cityId + '&district_id=' + countyId, function(res) {
                        if (res.code === 1 && res.data) {
                            var options = '<option value="0">選擇路段</option>';
                            res.data.forEach(function(item) {
                                options += '<option value="' + item.id + '">' + item.name + '</option>';
                            });
                            $('#street').html(options).prop('disabled', false);
                        } else {
                            $('#street').html('<option value="0">選擇路段</option>').prop('disabled', false);
                        }
                    }).fail(function() {
                        $('#street').html('<option value="0">選擇路段</option>').prop('disabled', false);
                    });
                } else {
                    $('#street').html('<option value="0">選擇路段</option>').prop('disabled', false);
                }
            });

            // 路段联动门店（仅超商取货时显示）
            $('#street').change(function() {
                var streetId = $(this).val();
                var streetName = $(this).find('option:selected').text(); // 使用名称
                var cityId = $('#city').val();
                var countyId = $('#county').val();
                $('#street-hidden').val(streetName);
                $('#form-store-row').html('');

                // 只有超商取货时才加载门店
                if (streetId && cityId && countyId && currentOrderType === 1) {
                    // 显示加载中
                    $('#street').prop('disabled', true);
                    $('#form-store-row').html('<p class="form-title" style="margin-top: 20px;">選擇門市</p><div style="text-align: center; padding: 20px; color: #999;">載入中...</div>');
                    $('#form-store-row').show();

                    $.get(apiBaseUrl + '?city_id=' + cityId + '&district_id=' + countyId + '&road_id=' + streetId, function(res) {
                        $('#street').prop('disabled', false);
                        if (res.code === 1 && res.data) {
                            // 计算送达日期（后天）
                            var today = new Date();
                            today.setDate(today.getDate() + 2);
                            var month = today.getMonth() + 1;
                            var day = today.getDate();
                            var dateStr = month + '月' + day + '日';

                            var storeHtml = '<p class="form-title" style="margin-top: 20px;">選擇門市</p>';
                            res.data.forEach(function(item) {
                                var storeName = item.store_name || item.name || '';
                                var address = item.address || '';
                                storeHtml += '<label class="store-item" for="store-' + item.id + '">' +
                                    '<input type="radio" name="store_id" value="' + item.id + '" id="store-' + item.id + '">' +
                                    '<div class="store-content">' +
                                    '<div class="store-main">' +
                                    '<svg class="sevenicon" viewBox="0 0 272.68729 257.44435"><use href="#icon-sevenicon-1"></use></svg>' +
                                    '<div class="store-info">' +
                                    '<p class="store-name">' + storeName + ' 門市</p>' +
                                    '<p class="store-address">' + address + '</p>' +
                                    '</div></div>' +
                                    '<p class="store-tips">' +
                                    '<svg viewBox="0 0 1024 1024" style="width:18px;height:18px;"><path d="M512 0a512 512 0 1 0 0 1024A512 512 0 0 0 512 0z" fill="#4bb400"/><path d="M433.6 723.9c-13.3 0-26-5.3-35.4-14.6L266 577c-19.5-19.5-19.5-51.2 0-70.7s51.2-19.5 70.7 0l96.9 96.9 228.1-228.1c19.5-19.5 51.2-19.5 70.7 0s19.5 51.2 0 70.7L469 709.3c-9.4 9.3-22.1 14.6-35.4 14.6z" fill="#FFF"/></svg>' +
                                    '預計最快後天（' + dateStr + '）送達 ' + storeName + ' 門市</p>' +
                                    '</div></label>';
                            });
                            $('#form-store-row').html(storeHtml);
                            $('#form-store-row').show();

                            // 门店选择事件
                            $('.store-item input').change(function() {
                                $('.store-main').css('border-color', '#C4C4C4').css('background', '#fff');
                                $('.store-tips').hide();
                                if ($(this).is(':checked')) {
                                    $(this).closest('.store-item').find('.store-main').css('border-color', '#4bb400').css('background', '#fbfffa');
                                    $(this).closest('.store-item').find('.store-tips').show();
                                }
                            });
                        } else {
                            $('#form-store-row').html('<p class="form-title" style="margin-top: 20px;">選擇門市</p><div style="text-align: center; padding: 20px; color: #999;">暫無門市</div>');
                            $('#form-store-row').show();
                        }
                    }).fail(function() {
                        $('#street').prop('disabled', false);
                        $('#form-store-row').html('<p class="form-title" style="margin-top: 20px;">選擇門市</p><div style="text-align: center; padding: 20px; color: #999;">載入失敗，請重試</div>');
                        $('#form-store-row').show();
                    });
                }
            });
        });
    </script>
    <script src="{{ asset('static/js/FormHelper.js') }}"></script>
    <script src="{{ asset('static/js/price-animator.js') }}"></script>
    <script id="CHECKOUT-M-1">

        var freight_where = parseInt('{{ $freight_where }}');

        var freight_price = parseInt('{{ $freight_price }}');

        var fpPromise = FingerprintJS.load();
        fpPromise.then(fp => fp.get()).then(
            function (result) {
                if (result.visitorId){
                    var code = result.visitorId;
                    $('input[name="fingerprint_token"]').val(code);

                }
            }
        )

    </script>
    <script>
        const formRules = {
            rules: {
                name: {
                    type: "required",
                        messages: { required: "請填寫收貨人名稱"}
                },
                phone: {
                    type: "phone|required",
                        messages: { required: "請填寫電話號碼", phone: "電話號碼格式不正確" }
                },
                email: {
                    type: "email|required",
                        messages: { required: "請填寫郵箱", email: "郵箱格式不正確" }
                },
                order_type: {
                    type: "required|number",
                        messages: { required: "請選擇配送方式"}
                },
                city_id: {
                    type: "required",
                        messages: { required: "請選擇縣市"}
                },
                county_id: {
                    type: "required",
                        messages: { required: "請選擇地區"}
                },
                street_id: {
                    type: "required",
                        messages: { required: "請選擇路段"}
                },
                goods_id: {
                    type: "required|number",
                        messages: { required: "產品數據出錯，請刷新重試"}
                }
            },
        }
        let order_form = document.querySelector("#order-form");
        order_form.addEventListener("submit", e => {
            e.preventDefault();
            FormHelper.submit("#order-form", formRules);
        });
        setInterval(function () {
            FormHelper.validate(order_form, new FormData(order_form),formRules).then(errors => {
                if(!errors.length){
                    $('.checkout-btn').addClass('ready');
                }
            });
        },1000)



    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const splitAnimatedText = (selector = '.js-split-label') => {
                document.querySelectorAll(selector).forEach((element) => {
                    if (element.dataset.splitDone === '1') return;
                    const step = parseInt(element.dataset.splitStep || '50', 10);
                    const text = (element.textContent || '').replace(/\s+/g, '');
                    if (!text) return;

                    const fragment = document.createDocumentFragment();
                    Array.from(text).forEach((char, index) => {
                        const span = document.createElement('span');
                        span.style.transitionDelay = `${index * step}ms`;
                        span.textContent = char;
                        fragment.appendChild(span);
                    });

                    element.textContent = '';
                    element.appendChild(fragment);
                    element.dataset.splitDone = '1';
                });
            };

            splitAnimatedText();

            // 初始化价格动画和安全扫描动画


            // Phone 输入框格式化
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                function formatTaiwanPhone(value) {
                    const digits = value.replace(/\D/g, '').slice(0, 10);
                    if (digits.length <= 4) {
                        return digits;
                    }
                    if (digits.length <= 7) {
                        return digits.replace(/^(\d{4})(\d+)/, '$1 $2');
                    }
                    return digits.replace(/^(\d{4})(\d{3})(\d+)/, '$1 $2 $3');
                }

                function getCursorPositionAfterFormat(oldValue, newValue, cursor) {
                    const digitsBeforeCursor = oldValue
                        .slice(0, cursor)
                        .replace(/\D/g, '')
                        .length;
                    let count = 0;
                    for (let i = 0; i < newValue.length; i++) {
                        if (/\d/.test(newValue[i])) count++;
                        if (count === digitsBeforeCursor) {
                            return i + 1;
                        }
                    }
                    return newValue.length;
                }

                phoneInput.addEventListener('input', (e) => {
                    const oldValue = e.target.value;
                    const cursor = e.target.selectionStart;
                    const formatted = formatTaiwanPhone(oldValue);
                    const newCursor = getCursorPositionAfterFormat(oldValue, formatted, cursor);
                    e.target.value = formatted;
                    e.target.setSelectionRange(newCursor, newCursor);
                });

                phoneInput.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    const digits = pastedText.replace(/\D/g, '').slice(0, 10);
                    const formatted = formatTaiwanPhone(digits);
                    phoneInput.value = formatted;
                });
            }

            // Email 输入框自动完成
            const emailInput = document.getElementById('email');
            const emailHelper = document.getElementById('email-helper');

            if (emailInput) {
                const isAndroid = /Android/i.test(navigator.userAgent);

                const typoMap = {
                    'gmial.com': 'gmail.com',
                    'gamil.com': 'gmail.com',
                    'gnail.com': 'gmail.com',
                    'hotnail.com': 'hotmail.com',
                    'outllok.com': 'outlook.com'
                };

                function hideHelper() {
                    if (emailHelper) {
                        emailHelper.hidden = true;
                        emailHelper.textContent = '';
                    }
                }

                function showHelper(text) {
                    if (emailHelper) {
                        emailHelper.textContent = text;
                        emailHelper.hidden = false;
                    }
                }

                emailInput.addEventListener('input', () => {
                    const value = emailInput.value;
                    const atPos = value.indexOf('@');

                    if (atPos === -1) {
                        hideHelper();
                        return;
                    }

                    if (!isAndroid || !emailHelper) return;

                    const name = value.slice(0, atPos);
                    const domain = value.slice(atPos + 1).toLowerCase();

                    if (domain.length > 3) {
                        hideHelper();
                        return;
                    }

                    if ('gmail.com'.startsWith(domain)) {
                        showHelper(`${name}@gmail.com`);
                    } else {
                        hideHelper();
                    }
                });

                emailInput.addEventListener('blur', () => {
                    if (!emailHelper) return;
                    
                    const value = emailInput.value.trim();
                    const parts = value.split('@');

                    if (parts.length !== 2) return;

                    const [name, domain] = parts;
                    const fixed = typoMap[domain];

                    if (fixed) {
                        showHelper(`${name}@${fixed}`);
                    }
                });

                if (emailHelper) {
                    emailHelper.addEventListener('click', () => {
                        emailInput.value = emailHelper.textContent;
                        hideHelper();
                        emailInput.focus();
                    });
                }
            }

            // 所有输入框的 has-value 类管理
            const inputs = document.querySelectorAll('#name, #phone, #email, #address');

            inputs.forEach(input => {
                const wrap = input.closest('.form-input');
                if (!wrap) return;

                function syncHint() {
                    const hasValue = input.value.trim().length > 0;
                    wrap.classList.toggle('has-value', hasValue);
                }

                input.addEventListener('input', syncHint);
                input.addEventListener('blur', syncHint);
                input.addEventListener('change', syncHint);

                // 初始化状态
                syncHint();
            });
        });
    </script>
@stop

@section('footer-menu')

    <div class="footer-menu">
        <div class="shop-price">
            <p class="goods-title">{{ $goods->name }}</p>
            <p class="red-price"><span style="font-size: 0.22rem; font-weight: 700; margin-right: 0.1rem;">訂單總額：NT$</span><span id="foot-order-price">{{ number_format(round($goods->price>=$freight_where?$goods->price:$goods->price+$freight_price)) }}</span></p>
        </div>
        <div class="shop-buy">
            <button class="form-btn" onclick="$('#order-form').submit();">
                <svg class="checkouticon" viewBox="0 0 1024 1024"><use href="#icon-checkouticon"></use></svg>提交訂單
            </button>
        </div>
    </div>
    <div id="cover"></div>
@stop

@section('content')
    <h1 style="font-size: clamp(20px, 2vw, 32px); text-align: center;">安全結帳</h1>
    <form class="checkout-container" method="POST" action="{{ url('order') }}" id="order-form" >
        {{ csrf_field() }}
        <input type="hidden" value="{{ $form_token }}" name="form_token">
        <input type="hidden" value="{{ $goods->id }}" name="goods_id">
        <input type="hidden" value="" name="timezone">
        <input type="hidden" value="" name="fingerprint_token">

        <div class="card">
            <span class="imgicon" role="img" aria-label="原裝進口"></span>
            <p class="form-title">訂購內容：</p>
            <div class="goods">
                <div class="img-wrap">
                    <img class="goods-img" src="{{ storage_url($goods->img) }}" alt="{{ $goods->name }}">
                </div>
                <div class="info">
                    <p class="goods-title"><span>禮來犀利士Cialis<sup>®</sup> 100mg</span>{{ $goods->name }}{{ $goods->quantity }}盒</p>
                    
                    <p class="sub-title">• 4錠/盒 共{{ $goods->quantity }}盒（原裝進口）</p>
                    <p class="sub-title">• 隱密包裝</p>
                </div>
            </div>
            @include('components.secret')
            <dl class="order-summary">
                <dt>商品原價</dt>
                <dd><span class="twd">NT$</span><span id="goods-price">{{ number_format(round($goods->market_price)) }}</span></dd>

                <dt>官網優惠</dt>
                <dd>
                    <span class="twd">— NT$</span><span id="discount-price">{{ number_format(round($goods->market_price-$goods->price)) }}</span>
                    <p class="discount-sub" data-market-price="{{ round($goods->market_price) }}" data-price="@if($goods->price<$freight_where){{ round($goods->price+$freight_price) }}@else{{ round($goods->price) }}@endif">(為您優惠<span class="descount-num">0</span>%)</p>
                </dd>

                <dt>運費@if($goods->quantity < 3)<span class="grep">（訂購4盒以上免運費）</span>@endif</p></dt>
                <dd>
                    <span id="freight-price">
                        @if($goods->price<$freight_where)
                            <span class="twd">NT$</span>{{ round($freight_price) }}
                        @else
                            <span class="twd">NT$</span>0
                        @endif
                    </span>
                </dd>

                <dt>訂單總額</dt>
                <dd>
                    <div class="price-box" data-market-price="{{ round($goods->market_price) }}" data-price="@if($goods->price<$freight_where){{ round($goods->price+$freight_price) }}@else{{ round($goods->price) }}@endif">
                        <span class="twd">NT$</span><span class="price-number" id="order-price">@if($goods->price<$freight_where){{ number_format(round($goods->price+$freight_price)) }}@else{{ number_format(round($goods->price)) }}@endif
                        </span>
                    </div>
                </dd>

            </div>

        </dl>

        <div class="card">
            <p class="form-title">配送訊息：</p>
            <div class="data-group">
                <div class="form-item">
                    <svg class="formicon" viewBox="0 0 1024 1024"><use href="#icon-formicon-name"></use></svg>
                    <div class="form-input">
                        <input type="text" id="name" name="name" autocomplete="off"  placeholder="請輸入收貨人姓名" required>
                        <label class="js-split-label" data-split-step="50">請問如何稱呼您</label>
                        <p class="hint">請輸入收貨人名字</p>
                    </div>
                    <svg class="safeicon" viewBox="0 0 1024 1024"><use href="#icon-safeicon"></use></svg>
                </div>
                <div class="form-item">
                    <svg class="formicon" viewBox="0 0 1024 1024"><use href="#icon-formicon-phone"></use></svg>
                    <div class="form-input">
                        <input type="tel" id="phone" name="phone" inputmode="tel" autocomplete="tel" pattern="09\d{2}\s\d{3}\s\d{3}" maxlength="12" placeholder="09** *** ***" required>
                        <label class="js-split-label" data-split-step="50">請輸入收貨人電話號碼</label>
                        <p class="hint">09** *** ***</p>
                    </div>
                    <svg class="safeicon" viewBox="0 0 1024 1024"><use href="#icon-safeicon"></use></svg>
                </div>
                <div class="form-item">
                    <svg class="formicon" viewBox="0 0 1024 1024"><use href="#icon-formicon-email"></use></svg>
                    <div class="form-input">
                        <input type="email" id="email" name="email" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false"  autocorrect="off" placeholder="********@gmail.com" enterkeyhint="done" required>
                        <label class="js-split-label" data-split-step="50">請輸入電子信箱</label>
                        <p class="hint">********@gmail.com</p>
                    </div>
                    <svg class="safeicon" viewBox="0 0 1024 1024"><use href="#icon-safeicon"></use></svg>
                </div>
            </div>

            <p class="form-title">配送與付款方式：</p>
            <div class="radio-box">
                <div class="form-radio">
                    <input type="radio" id="order-type-1" name="order_type" value="1" checked>
                    <label class="radio-label" for="order-type-1">
                        <svg class="sevenicon" viewBox="0 0 272.68729 257.44435"><use href="#icon-sevenicon-1"></use></svg>
                        <span class="text">7-Eleven便利店</br>取貨付款</span>
                    </label>
                </div>
                <div class="form-radio">
                    <input type="radio" id="order-type-0" name="order_type" value="0">
                    <label class="radio-label" for="order-type-0">
                        <svg class="sevenicon" style="transform: scale(1.15);" viewBox="0 0 1548 1123"><use href="#icon-sevenicon-2"></use></svg>
                        <span class="text">黑貓宅配到府</br>貨到付款</span>
                    </label>
                </div>
            </div>

            <p class="form-title" id="order-type-title">配送至</p>
            <div class="form-group">
                <div class="form-select">
                    <div class="select-box" id="load-1">
                        <select name="city_id" id="city">
                            <option value="0">選擇縣市</option>
                        </select>
                        <svg class="select-icon" viewBox="0 0 1280 1024"><use href="#icon-select-icon"></use></svg>
                    </div>

                    <div class="select-box" id="load-2">
                        <select name="county_id" id="county">
                            <option value="0">選擇地區</option>
                        </select>
                        <svg class="select-icon" viewBox="0 0 1280 1024"><use href="#icon-select-icon"></use></svg>
                    </div>

                    <div class="select-box" id="load-3">
                        <select name="street_id" id="street">
                            <option value="0">選擇路段</option>
                        </select>
                        <svg class="select-icon" viewBox="0 0 1280 1024"><use href="#icon-select-icon"></use></svg>
                    </div>
                    <input type="hidden" name="city" id="city-hidden" value="">
                    <input type="hidden" name="county" id="county-hidden" value="">
                    <input type="hidden" name="street" id="street-hidden" value="">

                </div>
                <div class="form-item form-address" id="form-address-row">
                    <svg class="formicon" viewBox="0 0 1024 1024"><use href="#icon-formicon-address"></use></svg>
                    <div class="form-input">
                    <input type="text" id="address" name="address" autocomplete="off" placeholder="請輸入詳細收貨地址">
                        <label>
                            <span style="transition-delay:0ms">請</span>
                            <span style="transition-delay:50ms">輸</span>
                            <span style="transition-delay:100ms">入</span>
                            <span style="transition-delay:150ms">詳</span>
                            <span style="transition-delay:200ms">細</span>
                            <span style="transition-delay:300ms">收</span>
                            <span style="transition-delay:350ms">貨</span>
                            <span style="transition-delay:400ms">地</span>
                            <span style="transition-delay:450ms">址</span>
                        </label>
                        <p class="hint">請輸入詳細收貨地址</p>
                    </div>
                    <svg class="safeicon" viewBox="0 0 1024 1024"><use href="#icon-safeicon"></use></svg>
                </div>
                <div class="form-store" id="form-store-row">
                </div>
            </div>

            <p class="form-title">訂單備註</p>
            <textarea class="form-textarea" name="remarks" placeholder="（選填）"></textarea>
        </div>
        <button class="checkout-btn">
            <svg class="checkouticon" viewBox="0 0 1024 1024"><use href="#icon-checkouticon"></use></svg>提交訂單
        </button>
    </form>
    
    <div class="security-scan-overlay">
        <svg class="pro-icon" viewBox="0 0 1024 1024"><use href="#icon-pro-icon"></use></svg>
        <p class="security-scan-text">安全環境檢測中</p>
        <p class="security-scan-status">正在掃描結帳環境...</p>
        <div class="security-scan-progress">
            <div class="security-scan-progress-bar"></div>
        </div>
        
    </div>
@endsection




