/**
 * checkout page plugin — delivery_type / cascade
 */
(function (window) {
    'use strict';
    if (!window.Track) return;

    document.addEventListener(
        'change',
        function (e) {
            var t = e.target;
            if (!t || t.name !== 'order_type') return;
            Track.event('delivery_type_change', {
                value: String(t.value),
                product_id: Track.page.goodsId != null ? String(Track.page.goodsId) : undefined,
            });
            Track.event('cascade_step', {
                step: 'order_type',
                changed: 1,
                value: String(t.value),
            });
        },
        true
    );
})(window);
