/**
 * product plugin — sticky buy bar
 */
(function (window, document) {
    'use strict';
    if (!window.Track) return;

    document.addEventListener('DOMContentLoaded', function () {
        var sticky = document.querySelector('.footer-buy, [data-track-sticky-buy]');
        if (!sticky || !window.IntersectionObserver) return;
        var ctx = Track.getPageContext();
        var sent = false;
        var io = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (en) {
                    if (sent || !en.isIntersecting) return;
                    // footer-buy 常以 show class 才可見：若不可見則等 class
                    if (sticky.classList.contains('footer-buy') && !sticky.classList.contains('show') && !sticky.classList.contains('pc-show')) {
                        return;
                    }
                    sent = true;
                    Track.event('sticky_buy_view', {
                        product_id: ctx.goods_id != null ? String(ctx.goods_id) : undefined,
                        goods_id: ctx.goods_id,
                    });
                });
            },
            { threshold: 0.3 }
        );
        io.observe(sticky);

        if (window.MutationObserver && sticky.classList.contains('footer-buy')) {
            var mo = new MutationObserver(function () {
                if (sent) return;
                if (sticky.classList.contains('show') || sticky.classList.contains('pc-show')) {
                    sent = true;
                    Track.event('sticky_buy_view', {
                        product_id: ctx.goods_id != null ? String(ctx.goods_id) : undefined,
                        goods_id: ctx.goods_id,
                    });
                }
            });
            mo.observe(sticky, { attributes: true, attributeFilter: ['class'] });
        }
    });

    document.addEventListener(
        'click',
        function (e) {
            var a = e.target.closest('.footer-buy a, [data-track-sticky-buy]');
            if (!a) return;
            if (a.getAttribute('data-track') || a.getAttribute('data-track-name')) return;
            var ctx = Track.getPageContext();
            Track.event('sticky_buy_click', {
                product_id: ctx.goods_id != null ? String(ctx.goods_id) : undefined,
                goods_id: ctx.goods_id,
            });
        },
        true
    );
})(window, document);
