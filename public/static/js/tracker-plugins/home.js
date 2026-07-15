/**
 * home plugin — hero slide + FAQ
 */
(function (window, document) {
    'use strict';
    if (!window.Track) return;

    function trackHeroSlide(index) {
        Track.event('hero_slide_view', { slide_index: index });
    }

    function watchHero() {
        var carousel = document.getElementById('hero-video-carousel') || document.querySelector('.hero-carousel');
        if (!carousel) {
            trackHeroSlide(0);
            return;
        }
        var slides = carousel.querySelectorAll('.hero-slide');
        function currentIndex() {
            for (var i = 0; i < slides.length; i++) {
                if (slides[i].classList.contains('is-active')) return i;
            }
            return 0;
        }
        trackHeroSlide(currentIndex());
        if (!window.MutationObserver) return;
        var last = currentIndex();
        var mo = new MutationObserver(function () {
            var idx = currentIndex();
            if (idx !== last) {
                last = idx;
                trackHeroSlide(idx);
            }
        });
        slides.forEach(function (s) {
            mo.observe(s, { attributes: true, attributeFilter: ['class'] });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', watchHero);
    } else {
        watchHero();
    }

    document.addEventListener(
        'click',
        function (e) {
            var q = e.target.closest('.question-show, .article-faq .faq-item, [data-faq-toggle], .qa-item');
            if (!q) return;
            var item = q.closest('.item, .question-show, .faq-item, .qa-item') || q;
            var id = item.getAttribute('data-faq-id') || item.id || '';
            var expanded = item.classList.contains('is-open') || item.getAttribute('aria-expanded') === 'true';
            Track.event('faq_toggle', { faq_id: id, expanded: expanded ? 1 : 0 });
        },
        true
    );
})(window, document);
