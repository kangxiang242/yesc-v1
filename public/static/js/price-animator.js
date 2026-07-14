/**
 * 价格动画模块 - 统一的价格计算和动画处理
 * 支持多种触发时机：scroll（滚动触发）、load（页面加载触发）、manual（手动触发）
 */
const PriceAnimator = {
    /**
     * 数字格式化 - 添加千分位逗号
     * @param {number} number - 要格式化的数字
     * @returns {string} 格式化后的字符串
     */
    number_format(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },

    /**
     * 解析 CSS 时间变量
     * @param {string} varName - CSS 变量名
     * @param {HTMLElement} targetEl - 目标元素
     * @returns {number} 解析后的毫秒数
     */
    parseTime(val) {
        if (!val) return 0;
        val = String(val).trim();
        if (val.endsWith('ms')) return parseFloat(val);
        if (val.endsWith('s')) return parseFloat(val) * 1000;
        return parseFloat(val) || 0;
    },

    getCssTime(varName, targetEl) {
        return this.parseTime(
            getComputedStyle(targetEl || document.documentElement)
                .getPropertyValue(varName)
        );
    },

    /**
     * 价格下降动画（核心动画函数）
     * @param {jQuery} $priceBox - 价格容器元素（jQuery对象）
     * @param {number} customDuration - 自定义动画时长（毫秒）
     * @param {number} customDelay - 自定义延迟时间（毫秒）
     * @returns {Promise} 动画完成后的 Promise
     */
    animatePrice($priceBox, customDuration, customDelay) {
        return new Promise((resolve) => {
            // 防止重复动画
            if ($priceBox.data('animated')) {
                return resolve();
            }
            $priceBox.data('animated', true);

            const $priceNumber = $priceBox.find('.price-number');
            const marketPrice = parseFloat($priceBox.data('market-price'));
            const finalPrice = parseFloat($priceBox.data('price'));

            // 验证数据
            if (!marketPrice || !finalPrice || marketPrice === finalPrice) {
                return resolve();
            }

            // 获取动画参数
            const pageEl = document.querySelector('.page-checkout') || document.documentElement;
            const duration = customDuration || this.getCssTime('--price-duration', pageEl) || 1000;
            const delay = customDelay || 0;

            // 延迟后开始动画
            setTimeout(() => {
                // 设置初始价格为原价
                $priceNumber.text(this.number_format(marketPrice));

                let startTime = null;

                const animate = (currentTime) => {
                    if (startTime === null) startTime = currentTime;
                    const progress = Math.min((currentTime - startTime) / duration, 1);

                    // 使用缓动函数让动画更自然
                    const easeOutCubic = 1 - Math.pow(1 - progress, 3);
                    const currentPrice = marketPrice - (marketPrice - finalPrice) * easeOutCubic;

                    $priceNumber.text(this.number_format(Math.round(currentPrice)));

                    if (progress < 1) {
                        requestAnimationFrame(animate);
                    } else {
                        $priceNumber.text(this.number_format(finalPrice));
                        resolve();
                    }
                };

                requestAnimationFrame(animate);
            }, delay);
        });
    },

    /**
     * 计算并显示折扣百分比
     * @param {jQuery} $discountBox - 折扣容器元素
     */
    calculateDiscount($discountBox) {
        const marketPrice = parseFloat($discountBox.data('market-price'));
        const price = parseFloat($discountBox.data('price'));

        if (marketPrice > 0 && price >= 0) {
            const discountPercent = Math.round(
                ((marketPrice - price) / marketPrice) * 100
            );
            $discountBox.find('.descount-num').text(discountPercent);
        }
    },

    /**
     * 添加可见性类并触发动画
     * @param {jQuery} $priceBox - 价格容器
     * @param {jQuery} $discountBox - 折扣容器
     * @param {number} duration - 动画时长
     * @param {number} delay - 动画延迟
     */
    showPriceWithAnimation($priceBox, $discountBox, duration, delay) {
        // 添加可见性类
        $priceBox.addClass('price-show');
        if ($discountBox.length) {
            $discountBox.addClass('discount-show');
        }

        // 触发价格下降动画
        this.animatePrice($priceBox, duration, delay);
    },

    /**
     * 滚动触发模式（产品列表页）
     * 当卡片滚动到视口中时触发动画
     */
    initScrollTrigger() {
        const self = this;
        let ticking = false;

        const checkVisibility = () => {
            const scrollTop = $(window).scrollTop();
            const viewportHeight = $(window).height();
            const triggerPoint = viewportHeight * 0.85; // 触发点：85% 视口高度

            $('.product-card').each(function() {
                const $productBox = $(this);
                const elementTop = $productBox.offset().top;
                const elementHeight = $productBox.outerHeight();
                const elementBottom = elementTop + elementHeight;
                const $priceBox = $productBox.find('.price-box');
                const $discountBox = $productBox.find('.discount-box');

                // 检查是否在视口内（卡片的一部分在视口内即可）
                // 卡片顶部在视口内，或者卡片底部在视口内
                const isVisible = (
                    (elementTop >= scrollTop && elementTop < scrollTop + viewportHeight) || // 顶部在视口内
                    (elementBottom > scrollTop && elementBottom <= scrollTop + viewportHeight) || // 底部在视口内
                    (elementTop < scrollTop && elementBottom > scrollTop + viewportHeight) // 卡片比视口大，完全覆盖视口
                );

                if (isVisible) {
                    // 添加产品框显示类
                    if (!$productBox.hasClass('product-card-show')) {
                        $productBox.addClass('product-card-show');
                    }
                    
                    // 显示价格和折扣
                    if ($priceBox.length && !$priceBox.hasClass('price-show')) {
                        self.showPriceWithAnimation($priceBox, $discountBox);
                    }
                }
            });

            ticking = false;
        };

        // 滚动监听
        $(window).on('scroll.priceAnimator', () => {
            if (!ticking) {
                requestAnimationFrame(checkVisibility);
                ticking = true;
            }
        });

        // 页面加载时立即检查当前滚动位置并显示视口内的产品
        $(document).ready(() => {
            // 多次检查确保DOM完全渲染
            const initCheck = () => {
                // 使用 requestAnimationFrame 确保在下一次重绘前执行
                requestAnimationFrame(() => {
                    checkVisibility();
                });
            };

            // 立即检查一次
            initCheck();
            
            // 延迟检查（确保DOM完全渲染）
            setTimeout(initCheck, 50);
            setTimeout(initCheck, 100);
            setTimeout(initCheck, 200);
            setTimeout(initCheck, 300);
            setTimeout(initCheck, 500);
        });

        // 所有资源加载完成后再次检查
        $(window).on('load.priceAnimator', () => {
            setTimeout(() => {
                checkVisibility();
            }, 100);
        });
    },

    /**
     * 页面加载触发模式（产品详情页等）
     * 页面加载完成后立即触发动画
     */
    initLoadTrigger() {
        const self = this;

        $(document).ready(() => {
            // 处理 .product-box 内的价格
            $('.product-box').each(function() {
                const $box = $(this);
                const $priceBox = $box.find('.price-box');
                const $discountBox = $box.find('.discount-box');

                $box.addClass('product-box-show');

                if ($priceBox.length) {
                    self.showPriceWithAnimation($priceBox, $discountBox);
                }
            });

            // 处理没有 .product-box 的价格
            $('.price-box').not('.price-show').each(function() {
                const $priceBox = $(this);
                const $discountBox = $priceBox.siblings('.discount-box').add(
                    $priceBox.find('.discount-box')
                );

                self.showPriceWithAnimation($priceBox, $discountBox);
            });

            // 计算所有折扣
            $('.discount-sub').each(function() {
                self.calculateDiscount($(this));
            });
        });
    },

    /**
     * 结账页触发模式
     * 包含安全扫描动画、卡片进入动画、价格动画的复杂流程
     */
    initCheckoutTrigger() {
        const self = this;
        const STORAGE_KEY = 'securityScanPlayed';
        const hasPlayed = sessionStorage.getItem(STORAGE_KEY);
        const pageEl = document.querySelector('.page-checkout') || document.documentElement;
        const overlayDuration = this.getCssTime('--overlay-duration', pageEl) || 2000;
        const cardEnterDuration = 2000; // 卡片进入动画固定 2 秒

        const $overlay = $('.security-scan-overlay');
        const $body = $('body, html');
        const $pageContent = $('.checkout-btn, .secret, .card');
        const $stampText = $('.secret .stamp-text');

        // 工具函数：等待
        const wait = (ms) => new Promise(resolve => setTimeout(resolve, ms));

        // 显示主内容
        const showMainContent = () => {
            $pageContent.addClass('show');
            $body.css('overflow', '');
        };

        // 播放扫描动画
        const playOverlay = () => {
            return new Promise((resolve) => {
                $overlay.css('display', 'flex');
                $body.css('overflow', 'hidden');

                setTimeout(() => {
                    $overlay.fadeOut(300, () => {
                        $overlay.remove();
                        showMainContent();
                        resolve();
                    });
                }, overlayDuration);
            });
        };

        // 主流程
        const runCheckoutSequence = async (isFirstVisit) => {
            // 第一阶段：扫描动画或直接显示
            if (isFirstVisit && $overlay.length) {
                await playOverlay();
            } else {
                $overlay.remove();
                showMainContent();
            }

            // 第二阶段：准备价格动画
            const $orderPrice = $('#order-price');
            const marketPrice = parseFloat($('#goods-price').text().replace(/,/g, '')) || 0;
            const finalPrice = parseFloat($orderPrice.text().replace(/,/g, '')) || 0;

            if ($orderPrice.length && marketPrice > 0 && finalPrice > 0) {
                // 先显示原价
                $orderPrice.text(self.number_format(marketPrice));
            }

            // 第三阶段：等待卡片进入动画（固定 2 秒）
            await wait(cardEnterDuration);

            // 第四阶段：播放价格下降动画
            if (marketPrice > 0 && finalPrice > 0) {
                const $priceBox = $('.price-box').first();
                if ($priceBox.length) {
                    await self.animatePrice($priceBox);
                }
            }

            // 第五阶段：启动印章动画
            $stampText.addClass('stamp-text-show');

            // 计算折扣
            $('.discount-sub').each(function() {
                self.calculateDiscount($(this));
            });
        };

        // 判断是否首次访问
        if (!hasPlayed && $overlay.length) {
            sessionStorage.setItem(STORAGE_KEY, '1');
            runCheckoutSequence(true);
        } else {
            runCheckoutSequence(false);
        }
    },

    /**
     * 手动触发模式
     * 用于特殊场景，需要手动调用
     * @param {string|jQuery} selector - 价格容器选择器或jQuery对象
     * @param {object} options - 配置选项
     */
    triggerManually(selector, options = {}) {
        const $priceBox = typeof selector === 'string' ? $(selector) : selector;
        const $discountBox = options.discountBox || $priceBox.find('.discount-box');

        this.showPriceWithAnimation(
            $priceBox,
            $discountBox,
            options.duration,
            options.delay
        );
    },

    /**
     * 初始化入口
     * 自动检测页面类型并应用相应的触发模式
     * @param {string} mode - 触发模式：'scroll'、'load'、'checkout'、'manual'
     */
    init(mode = 'auto') {
        // 自动检测模式
        if (mode === 'auto') {
            // 结账页
            if ($('.security-scan-overlay').length || $('.page-checkout').length) {
                mode = 'checkout';
            }
            // 产品列表页
            else if ($('.product-card').length && !$('.product-box').length) {
                mode = 'scroll';
            }
            // 产品详情页或其他页面
            else {
                mode = 'load';
            }
        }

        // 根据模式初始化
        switch (mode) {
            case 'scroll':
                this.initScrollTrigger();
                break;
            case 'load':
                this.initLoadTrigger();
                break;
            case 'checkout':
                this.initCheckoutTrigger();
                break;
            case 'manual':
                // 手动模式不自动初始化，需要手动调用 triggerManually
                break;
        }
    }
};

// 自动初始化（如果页面上有 price-box）
$(document).ready(() => {
    if ($('.price-box').length) {
        // 检查是否有手动触发标记
        if ($('[data-price-animate="manual"]').length) {
            PriceAnimator.init('manual');
        } else {
            PriceAnimator.init('auto');
        }
    }
});
