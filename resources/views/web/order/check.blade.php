@extends('web.layout')

@section('track-init')
<script>Track.init({ platform: 'web', page_type: 'order_check' });</script>
@endsection

@section('style')
    @parent
@stop

@section('script')
    @parent

    <script src="{{ release_asset('static/js/sweetalert2.js') }}"></script>
    <script src="{{ release_asset('static/js/FormHelper.js') }}"></script>
    <script>
        // 查询方式切换
        document.addEventListener('DOMContentLoaded', function() {
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

            const radios = document.querySelectorAll('input[name="check_type"]');
            const sectionOrderId = document.getElementById('section-order-id');
            const sectionContact = document.getElementById('section-contact');
            const sectionTrack = document.getElementById('check-sections-track');
            const sectionsViewport = document.getElementById('check-sections-viewport');
            const orderIdInput = document.getElementById('order_id');
            const phoneInput = document.getElementById('phone');
            const emailInput = document.getElementById('email');
            const sectionMap = {
                order_id: sectionOrderId,
                contact: sectionContact
            };

            function syncViewportHeight(targetSection) {
                if (!sectionsViewport || !targetSection) return;
                sectionsViewport.style.height = `${targetSection.scrollHeight}px`;
            }

            function animateViewportHeight(targetSection, isInitial = false) {
                if (!sectionsViewport || !targetSection) return;

                if (isInitial) {
                    syncViewportHeight(targetSection);
                    return;
                }

                const currentHeight = sectionsViewport.getBoundingClientRect().height;
                const nextHeight = targetSection.scrollHeight;

                sectionsViewport.style.height = `${currentHeight}px`;
                requestAnimationFrame(() => {
                    sectionsViewport.style.height = `${nextHeight}px`;
                });
            }

            function toggleCheckType(isInitial = false) {
                const selectedType = document.querySelector('input[name="check_type"]:checked').value;
                
                if (selectedType === 'order_id') {
                    orderIdInput.required = true;
                    phoneInput.required = false;
                    emailInput.required = false;
                } else {
                    orderIdInput.required = false;
                    phoneInput.required = true;
                    emailInput.required = true;
                }

                if (sectionTrack) {
                    sectionTrack.dataset.active = selectedType;
                }

                Object.entries(sectionMap).forEach(([type, section]) => {
                    if (!section) return;
                    const isActive = type === selectedType;
                    section.classList.toggle('is-active', isActive);
                    section.setAttribute('aria-hidden', String(!isActive));
                });

                const activeSection = sectionMap[selectedType];
                if (isInitial) {
                    requestAnimationFrame(() => animateViewportHeight(activeSection, true));
                    return;
                }
                animateViewportHeight(activeSection);
            }

            radios.forEach(radio => {
                radio.addEventListener('change', toggleCheckType);
            });

            // 初始化
            toggleCheckType(true);

            if (sectionsViewport) {
                sectionsViewport.addEventListener('transitionend', (event) => {
                    if (event.propertyName !== 'height') return;
                    const selectedType = document.querySelector('input[name="check_type"]:checked')?.value;
                    const activeSection = selectedType ? sectionMap[selectedType] : null;
                    if (!activeSection) return;
                    sectionsViewport.style.height = `${activeSection.scrollHeight}px`;
                });
            }

            window.addEventListener('resize', () => {
                const selectedType = document.querySelector('input[name="check_type"]:checked')?.value;
                if (!selectedType) return;
                syncViewportHeight(sectionMap[selectedType]);
            });
        });

        // 表单提交
        document.querySelector("#check-form").addEventListener("submit", e => {
            e.preventDefault();
            
            const checkType = document.querySelector('input[name="check_type"]:checked').value;
            let rules = {};

            if (checkType === 'order_id') {
                const orderId = document.getElementById('order_id').value.trim();
                if (!orderId) {
                    Swal.fire({
                        icon: 'error',
                        text: '請填寫訂單編號',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
                if (!/^\d{4}\s\d{4}\s\d{4}\s\d{4}$/.test(orderId)) {
                    Swal.fire({
                        icon: 'error',
                        text: '訂單編號格式不正確',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
            } else {
                rules = {
                    email: {
                        type: "email|required",
                        messages: { required: "請填寫郵箱", email: "郵箱格式不正確" }
                    },
                    phone: {
                        type: "required|phone",
                        messages: { required: "請填寫訂購電話", phone: "電話格式不正確" }
                    }
                };
            }

            FormHelper.submit("#check-form", {
                rules,
                onValidateFail: function (errors) {
                    if (typeof Track !== 'undefined' && errors && errors.length) {
                        Track.validationError(errors[0].field || 'unknown');
                    }
                },
                onSuccess: function (data) {
                    if (typeof Track !== 'undefined') {
                        if (data && (data.code == 200 || data.status === 'success' || data.redirect || data.jump)) {
                            Track.orderCheckSuccess();
                        } else {
                            Track.orderCheckError({ error_code: 'query_fail' });
                        }
                    }
                    if (data && data.jump) {
                        window.location.href = data.jump;
                    } else if (data && data.redirect) {
                        window.location.href = data.redirect;
                    } else if (data && data.status === 'success') {
                        Swal.fire({ icon: 'success', text: data.message || '操作成功', timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', text: (data && data.message) || '查詢失敗', timer: 2000, showConfirmButton: false });
                    }
                },
                onError: function (err) {
                    if (typeof Track !== 'undefined') {
                        Track.orderCheckError({ error_code: (err && err.message) || 'server_error' });
                    }
                    Swal.fire({ icon: 'error', text: (err && err.message) || '服務器錯誤', timer: 1500, showConfirmButton: false });
                },
            });
        });

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

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

            const orderIdInput = document.getElementById('order_id');
            if (orderIdInput) {
                function formatOrderId(value) {
                    const digits = value
                        .replace(/[０-９]/g, d => String.fromCharCode(d.charCodeAt(0) - 65248))
                        .replace(/\D/g, '')
                        .slice(0, 16);

                    return digits.replace(/(\d{4})(?=\d)/g, '$1 ');
                }

                function getCursorAfterFormat(oldValue, newValue, cursor) {
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

                orderIdInput.addEventListener('input', (e) => {
                    const oldValue = e.target.value;
                    const cursor = e.target.selectionStart;

                    const formatted = formatOrderId(oldValue);
                    const newCursor = getCursorAfterFormat(oldValue, formatted, cursor);

                    e.target.value = formatted;
                    e.target.setSelectionRange(newCursor, newCursor);
                });

                orderIdInput.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pasted = (e.clipboardData || window.clipboardData).getData('text');
                    orderIdInput.value = formatOrderId(pasted);
                });
            }

            const inputs = document.querySelectorAll('#phone, #email, #order_id');

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
        @include('components.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item">訂單追蹤</li>'])
        {{--<header class="page-header">
            <h1 class="page-header-title">訂單查詢</h1>
            <p class="page-header-description">透過下單資訊或訂單號碼即可查看您的訂單與出貨進度</p>
            <p class="page-header-description">*查詢結果僅顯示本站訂單與出貨進度 不會顯示或公開任何個人隱私資料*</p>
        </header>--}}

        <form action="" id="check-form" method="post" onsubmit="return orderCheck()">
            {{ csrf_field() }}
            <h1 class="page-title">訂單追蹤</h1>
            
            <div class="check-type-selector">
                <input type="radio" name="check_type" id="check-type-order-id" value="order_id" checked>
                <label class="check-type-label" for="check-type-order-id">透過訂單編號查詢</label>
                <input type="radio" name="check_type" id="check-type-contact" value="contact">
                <label class="check-type-label" for="check-type-contact">透過聯絡資訊查詢</label>
                <div class="active-bg"></div>
            </div>

            <div class="check-sections-viewport" id="check-sections-viewport">
                <div class="check-sections-track" id="check-sections-track" data-active="order_id">
                    <div class="data-group check-section is-active" id="section-order-id">
                        <div class="form-item">
                            <svg class="formicon" viewBox="0 0 1024 1024">
                                <path d="M416 151.2h192c26.4 0 48-21.6 48-48s-21.6-48-48-48H416c-26.4 0-48 21.6-48 48s21.6 48 48 48z" fill="currentColor"/>
                                <path d="M848 103.3H728c0 52.8-43.2 96-96 96H392c-52.8 0-96-43.2-96-96H176c-39.8 0-72 32.2-72 72v721.5c0 39.8 32.2 72 72 72h672c39.8 0 72-32.2 72-72V175.3c0-39.8-32.3-72-72-72zM280 343.2h464c13.2 0 24 10.8 24 24s-10.8 24-24 24H280c-13.2 0-24-10.8-24-24s10.8-24 24-24z m0 168h464c13.2 0 24 10.8 24 24s-10.8 24-24 24H280c-13.2 0-24-10.8-24-24s10.8-24 24-24z m232 192c0 13.2-10.8 24-24 24H280c-13.2 0-24-10.8-24-24s10.8-24 24-24h208c13.2 0 24 10.8 24 24z m264 0c0 13.2-10.8 24-24 24H608c-13.2 0-24-10.8-24-24s10.8-24 24-24h144c13.2 0 24 10.8 24 24z" fill="currentColor"/>
                            </svg>
                            <div class="form-input">
                                <input type="text" id="order_id" name="order_id" inputmode="numeric" autocomplete="off" pattern="^\d{4}\s\d{4}\s\d{4}\s\d{4}$" minlength="19" maxlength="19" placeholder="**** **** **** ****">
                                <label class="js-split-label" data-split-step="50">請輸入您的訂單編號</label>
                                <p class="hint">**** **** **** ****</p>
                            </div>
                        </div>
                    </div>

                    <div class="data-group check-section" id="section-contact" aria-hidden="true">
                        <div class="form-item">
                            <svg class="formicon" viewBox="0 0 1024 1024"><use href="#icon-formicon-phone"></use></svg>
                            <div class="form-input">
                                <input type="tel" id="phone" name="phone" inputmode="tel" autocomplete="tel" pattern="09\d{2}\s\d{3}\s\d{3}" maxlength="12" placeholder="09** *** ***">
                                <label class="js-split-label" data-split-step="50">請輸入訂單預留電話</label>
                                <p class="hint">09** *** ***</p>
                            </div>
                        </div>
                        <div class="form-item">
                            <svg class="formicon" viewBox="0 0 1024 1024"><use href="#icon-formicon-email"></use></svg>
                            <div class="form-input">
                                <input type="email" id="email" name="email" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false"  autocorrect="off" placeholder="********@gmail.com" enterkeyhint="done">
                                <label class="js-split-label" data-split-step="50">請輸入訂單預留郵箱</label>
                                <p class="hint">********@gmail.com</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="form-btn main-btn" type="submit"><svg class="btn-icon" viewBox="0 0 1024 1024"><use href="#icon-checkicon"></use></svg>立即查詢</button>
            <p class="form-desc"><svg class="righticon" viewBox="0 0 1024 1024"><use href="#icon-righticon"></use></svg>訂單查詢結果的個人隱私資料已加密保護</p>
        </form>


        @include('components.qa', ['faqs' => $faqs])

@endsection
