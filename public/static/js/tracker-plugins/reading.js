/**
 * reading plugin — news / cms read_progress
 */
(function (window, document) {
    'use strict';
    if (!window.Track) return;

    var MILESTONES = [25, 50, 75, 100];
    var sent = {};
    var ctx = Track.getPageContext();

    function targetEl() {
        return (
            document.querySelector('[data-track-scroll-target]') ||
            document.getElementById('articleContent') ||
            document.getElementById('spageContent') ||
            document.querySelector('.article-content') ||
            document.querySelector('.acticle-content')
        );
    }

    function onEnter() {
        var el = targetEl();
        if (!el) return;
        var meta = {};
        if (ctx.article_id) meta.article_id = ctx.article_id;
        if (ctx.cms_uri) meta.cms_uri = ctx.cms_uri;
        Track.event('content_enter', meta);

        function emitProgress() {
            var p = Track.maxReadProgress || 0;
            for (var i = 0; i < MILESTONES.length; i++) {
                var m = MILESTONES[i];
                if (p >= m && !sent[m]) {
                    sent[m] = true;
                    var props = { percent: m, scroll_target: 'article' };
                    if (ctx.article_id) props.article_id = ctx.article_id;
                    if (ctx.cms_uri) props.cms_uri = ctx.cms_uri;
                    Track.event('read_progress', props);
                }
            }
        }
        window.addEventListener('scroll', emitProgress, { passive: true });
        emitProgress();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', onEnter);
    } else {
        onEnter();
    }

    document.addEventListener(
        'click',
        function (e) {
            var a = e.target.closest('.summary-list a, .article-summary a[href^="#"]');
            if (!a) return;
            var href = a.getAttribute('href') || '';
            Track.event('toc_nav', { heading_id: href.replace(/^#/, '') });
        },
        true
    );

    document.addEventListener(
        'click',
        function (e) {
            var btn = e.target.closest('.summary-switch');
            if (!btn) return;
            var expanded = btn.getAttribute('aria-expanded') === 'true';
            Track.event('toc_expand', { expanded: expanded ? 0 : 1 });
        },
        true
    );
})(window, document);
