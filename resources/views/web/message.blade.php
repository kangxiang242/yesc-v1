@extends('web.layout')

@section('track-init')
<script>Track.init({ platform: 'web', page_type: 'message' });</script>
@endsection

@section('style')
    @parent
@stop

@section('script')
    @parent
    <script src="{{ release_asset('static/js/sweetalert2.js') }}"></script>
    <script src="{{ release_asset('static/js/FormHelper.js') }}"></script>
    <script>
        document.querySelector("#message-form").addEventListener("submit", e => {
            e.preventDefault();
            FormHelper.submit("#message-form", {
                onValidateFail: function (errors) {
                    if (typeof Track !== 'undefined' && errors && errors.length) {
                        Track.validationError(errors[0].field || 'unknown');
                    }
                },
                onSuccess: function (data) {
                    if (typeof Track !== 'undefined') {
                        Track.messageSubmit({ status: 'success' });
                    }
                    if (data && data.redirect) {
                        window.location.href = data.redirect;
                    } else if (data && data.status === 'success') {
                        Swal.fire({ icon: 'success', text: data.message || '操作成功', timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', text: (data && data.message) || '操作失败', timer: 2000, showConfirmButton: false });
                    }
                },
                onError: function (err) {
                    if (typeof Track !== 'undefined') {
                        Track.messageSubmitError({ error_code: (err && err.message) || 'server_error' });
                    }
                    Swal.fire({ icon: 'error', text: (err && err.message) || '服務器錯誤', timer: 1500, showConfirmButton: false });
                },
                rules: {
                    name: {
                        type: "required",
                        messages: { required: "請填寫您的昵稱" }
                    },
                    email: {
                        type: "email|required",
                        messages: { required: "請填寫郵箱", email: "郵箱格式不正確" }
                    },
                    content: {
                        type: "required",
                        messages: { required: "請填寫留言內容" }
                    },
                },
            });
        });

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

            const emailInput = document.getElementById('email');
            const emailHelper = document.getElementById('email-helper');

            if (emailInput && emailHelper) {
                const isAndroid = /Android/i.test(navigator.userAgent);

                const typoMap = {
                    'gmial.com': 'gmail.com',
                    'gamil.com': 'gmail.com',
                    'gnail.com': 'gmail.com',
                    'hotnail.com': 'hotmail.com',
                    'outllok.com': 'outlook.com'
                };

                function hideHelper() {
                    emailHelper.hidden = true;
                    emailHelper.textContent = '';
                }

                function showHelper(text) {
                    emailHelper.textContent = text;
                    emailHelper.hidden = false;
                }

                emailInput.addEventListener('input', () => {
                    const value = emailInput.value;
                    const atPos = value.indexOf('@');

                    if (atPos === -1) {
                        hideHelper();
                        return;
                    }

                    if (!isAndroid) return;

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
                    const value = emailInput.value.trim();
                    const parts = value.split('@');

                    if (parts.length !== 2) return;

                    const [name, domain] = parts;
                    const fixed = typoMap[domain];

                    if (fixed) {
                        showHelper(`${name}@${fixed}`);
                    }
                });

                emailHelper.addEventListener('click', () => {
                    emailInput.value = emailHelper.textContent;
                    hideHelper();
                    emailInput.focus();
                });
            }

            const inputs = document.querySelectorAll('#email');

            inputs.forEach(input => {
                const wrap = input.closest('.form-input');
                if (!wrap) return;

                function syncHint() {
                    const hasValue = input.value.trim().length > 0;
                    wrap.classList.toggle('has-value', hasValue);
                    console.log(input.id, 'has-value:', hasValue);
                }

                input.addEventListener('input', syncHint);
                input.addEventListener('blur', syncHint);

                syncHint();
            });
        });
    </script>
@stop

@section('body-class', 'page-form')

@section('content')

    @include('components.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item">線上客服</li>'])
    {{--<header class="page-header">
        <h1 class="page-header-title">線上客服</h1>
        <p class="page-header-description">如有任何疑問、諮詢需求，皆可與官網專業醫師客服團隊線上聯絡取得協助。</p>
        <p class="page-header-description">*無需實名 對話內容保密不對外公開 專業客服快速回覆*</p>
    </header>--}}

    <form method="post" action="" id="message-form">
        {!! csrf_field() !!}
        <h1 class="page-title">線上客服</h1>
        <div class="data-group">
            <textarea class="form-textarea" name="content" rows="4" required minlength="10" maxlength="600" autocomplete="off" autocapitalize="none" autocorrect="off" spellcheck="false" inputmode="text" enterkeyhint="done" placeholder="我想補充訂單備註／想修改訂單地址..."></textarea>

            <div class="form-item">
                <svg class="formicon" viewBox="0 0 1024 1024"><use href="#icon-formicon-user"></use></svg>
                <div class="form-input">
                    <input type="text" id="name" name="name" autocomplete="name" autocapitalize="words" spellcheck="false" autocorrect="off" placeholder="您的昵稱" enterkeyhint="next" required>
                    <label class="js-split-label" data-split-step="50">請留下您的昵稱</label>
                    <p class="hint">您的稱呼</p>
                </div>
            </div>

            <div class="form-item">
                <svg t="1765935415924" class="formicon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="159398" width="200" height="200"><path d="M1024 768c0 22.4-6.2 43.4-16.4 61.6L684.4 467.8 1004.2 188c12.4 19.8 19.8 42.8 19.8 68L1024 768 1024 768 1024 768zM512 533.6 957 144.2c-18.2-10-38.8-16.2-61-16.2L128 128c-22.2 0-42.8 6.2-61 16.2L512 533.6 512 533.6 512 533.6zM636.2 510l-103 90.2c-6 5.2-13.6 7.8-21 7.8-7.6 0-15-2.6-21-7.8L388 510 60.6 876.4c19.6 12.2 42.6 19.8 67.4 19.8l768.2 0c24.8 0 47.8-7.4 67.4-19.8L636.2 510 636.2 510 636.2 510zM19.8 188C7.4 207.8 0 230.8 0 256l0 512c0 22.4 6.2 43.4 16.4 61.6l323.4-362L19.8 188 19.8 188 19.8 188zM19.8 188" p-id="159399" fill="currentColor"></path></svg>
                <div class="form-input">
                    <input type="email" id="email" name="email" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false"  autocorrect="off" placeholder="********@gmail.com" enterkeyhint="done" required>
                    <label class="js-split-label" data-split-step="50">請留下您的電子郵箱</label>
                    <p class="hint">********@gmail.com</p>
                </div>
            </div>

        </div>

        <button class="form-btn main-btn" type="submit" data-track="message.submit" data-observer="留言-確認送出" data-track-section="message.form" data-track-zone="content"><svg class="btn-icon sent-icon" viewBox="0 0 1024 1024"><use href="#icon-senticon"></use></svg>確認送出
        </button>
        <p class="form-desc"><svg class="righticon" viewBox="0 0 1024 1024"><use href="#icon-righticon"></use></svg>專業客服將在第一時間回覆您</p>
    </form>

    @include('components.qa', ['faqs' => $faqs])

@endsection
