/**
 * Web / Mobile 檢聽 SDK — 詳見 docs/analytics-backend-spec.md
 */
(function (global) {
    'use strict';

    var COLLECT_URL = '/api/analytics/collect';
    var SESSION_TIMEOUT_MS = 30 * 60 * 1000;
    var FLUSH_INTERVAL_MS = 5000;
    var SCROLL_THROTTLE_MS = 200;
    var BLOCK_THRESHOLD = 0.5;

    function uuid() {
        if (global.crypto && crypto.randomUUID) {
            return crypto.randomUUID();
        }
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = (Math.random() * 16) | 0;
            var v = c === 'x' ? r : (r & 0x3) | 0x8;
            return v.toString(16);
        });
    }

    function cookieGet(name) {
        var m = document.cookie.match(
            new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()[\]\\/+^])/g, '\\$1') + '=([^;]*)')
        );
        return m ? decodeURIComponent(m[1]) : '';
    }

    function cookieSet(name, value, days) {
        document.cookie = name + '=' + encodeURIComponent(value) + '; path=/; max-age=' + days * 86400 + '; SameSite=Lax';
    }

    function now() {
        return Date.now();
    }

    function pathOnly() {
        var p = global.location.pathname || '/';
        return p.length > 1 && p.slice(-1) === '/' ? p.slice(0, -1) : p;
    }

    function parseUtm() {
        var q = new URLSearchParams(global.location.search);
        var u = {};
        ['utm_source', 'utm_medium', 'utm_campaign'].forEach(function (k) {
            if (q.get(k)) {
                u[k] = q.get(k);
            }
        });
        return u;
    }

    function isInternalLink(el) {
        if (!el || el.tagName !== 'A') {
            return false;
        }
        var href = el.getAttribute('href');
        if (!href || href.indexOf('javascript:') === 0 || href === '#') {
            return false;
        }
        try {
            return new URL(href, global.location.origin).origin === global.location.origin;
        } catch (e) {
            return false;
        }
    }

    var Track = {
        platform: '',
        visitorId: '',
        sessionId: '',
        pageViewId: '',
        queue: [],
        flushTimer: null,
        initialized: false,
        leftSent: false,

        page: {
            type: 'unknown',
            title: '',
            goodsId: null,
            articleId: null,
            categoryUri: null,
            cmsUri: null,
        },

        enteredAt: 0,
        lastFieldInteract: null,
        firstFieldInteractAt: null,
        submitClickedAt: null,
        checkoutEnteredAt: null,

        scroll: {
            target: 'document',
            maxPercent: 0,
            milestones: {},
            firstScrollAt: null,
        },

        blocks: {
            seen: [],
            seenSet: {},
            times: {},
            lastId: null,
        },

        init: function (opts) {
            opts = opts || {};
            if (this.initialized) {
                this.configure(opts);
                return this;
            }

            this.platform = opts.platform === 'mobile' ? 'mobile' : 'web';
            var vidKey = this.platform === 'mobile' ? 'vid_m' : 'vid_web';
            var sidKey = this.platform === 'mobile' ? 'sid_m' : 'sid_web';

            this.visitorId = cookieGet(vidKey) || uuid();
            cookieSet(vidKey, this.visitorId, 365);

            this.sessionId = cookieGet(sidKey) || uuid();
            this._touchSession(sidKey);

            this.configure(opts);
            this.pageViewId = uuid();
            this.enteredAt = now();
            this.leftSent = false;

            if (this.page.type === 'checkout') {
                this.checkoutEnteredAt = this.enteredAt;
            }

            this._bindClicks();
            this._bindForms();
            this._bindScroll();
            this._bindBlocks();
            this._bindLeave();

            this.event('page_view', this._pageProps(parseUtm()));
            this.initialized = true;

            var self = this;
            this.flushTimer = setInterval(function () {
                self.flush(false);
            }, FLUSH_INTERVAL_MS);

            return this;
        },

        configure: function (opts) {
            opts = opts || {};
            if (opts.page_type) {
                this.page.type = opts.page_type;
            }
            if (opts.page_title) {
                this.page.title = opts.page_title;
            } else if (document.title) {
                this.page.title = document.title;
            }
            if (opts.goods_id != null) {
                this.page.goodsId = opts.goods_id;
            }
            if (opts.article_id != null) {
                this.page.articleId = opts.article_id;
            }
            if (opts.category_uri) {
                this.page.categoryUri = opts.category_uri;
            }
            if (opts.cms_uri) {
                this.page.cmsUri = opts.cms_uri;
            }
        },

        _touchSession: function (sidKey) {
            var tsKey = sidKey + '_ts';
            var last = parseInt(cookieGet(tsKey), 10) || 0;
            if (!last || now() - last > SESSION_TIMEOUT_MS) {
                this.sessionId = uuid();
                cookieSet(sidKey, this.sessionId, 1);
            }
            cookieSet(tsKey, String(now()), 1);
            setInterval(function () {
                cookieSet(tsKey, String(now()), 1);
            }, 60000);
        },

        _pageProps: function (extra) {
            var p = {
                page_path: pathOnly(),
                page_type: this.page.type,
                page_title: this.page.title || document.title || '',
            };
            if (this.page.goodsId != null) {
                p.goods_id = this.page.goodsId;
            }
            if (this.page.articleId != null) {
                p.article_id = this.page.articleId;
            }
            if (this.page.categoryUri) {
                p.category_uri = this.page.categoryUri;
            }
            if (this.page.cmsUri) {
                p.cms_uri = this.page.cmsUri;
            }
            // Release token
            p.html_token = this._getHtmlToken();
            var assets = this._collectAssetTokens(p.html_token);
            p.asset_tokens = assets.tokens;
            p.asset_missing_token_count = assets.missing;
            p.asset_count = assets.count;
            if (assets.mismatchSamples.length > 0) {
                p.asset_mismatch_samples = assets.mismatchSamples;
            }
            p.dpr = this._getDpr();
            if (extra) {
                for (var k in extra) {
                    if (Object.prototype.hasOwnProperty.call(extra, k)) {
                        p[k] = extra[k];
                    }
                }
            }
            return p;
        },

        _contextSnapshot: function () {
            return {
                blocks_seen: this.blocks.seen.slice(),
                last_block_id: this.blocks.lastId,
                duration_before_click_sec: Math.round((now() - this.enteredAt) / 1000),
                max_scroll_before_click_percent: this.scroll.maxPercent,
            };
        },

        _getHtmlToken: function () {
            var classes = document.documentElement.className.split(/\s+/);
            for (var i = 0; i < classes.length; i++) {
                if (/^[a-z][a-z0-9]{11}$/.test(classes[i])) {
                    return classes[i];
                }
            }
            return '';
        },

        _getDpr: function () {
            var dpr = document.documentElement.getAttribute('data-dpr');
            return dpr ? parseFloat(dpr) : 1;
        },

        _collectAssetTokens: function (htmlToken) {
            var tokens = [];
            var missing = 0;
            var mismatchSamples = [];
            var count = 0;

            // 掃描 CSS <link>
            var links = document.querySelectorAll('link[rel~="stylesheet"][href]');
            for (var i = 0; i < links.length; i++) {
                var href = links[i].getAttribute('href');
                if (!href || href.indexOf('/') !== 0) {
                    continue;
                }
                count++;
                var q = href.indexOf('?');
                if (q === -1) {
                    missing++;
                    continue;
                }
                var t = href.substring(q + 1);
                if (!/^[a-z][a-z0-9]{11}$/.test(t)) {
                    missing++;
                    continue;
                }
                if (tokens.indexOf(t) === -1) {
                    tokens.push(t);
                }
                if (t !== htmlToken && mismatchSamples.length < 3) {
                    mismatchSamples.push(href);
                }
            }

            // 掃描 JS <script src>
            var scripts = document.querySelectorAll('script[src]');
            for (var j = 0; j < scripts.length; j++) {
                var src = scripts[j].getAttribute('src');
                if (!src || src.indexOf('/') !== 0) {
                    continue;
                }
                count++;
                var qs = src.indexOf('?');
                if (qs === -1) {
                    missing++;
                    continue;
                }
                var ts = src.substring(qs + 1);
                if (!/^[a-z][a-z0-9]{11}$/.test(ts)) {
                    missing++;
                    continue;
                }
                if (tokens.indexOf(ts) === -1) {
                    tokens.push(ts);
                }
                if (ts !== htmlToken && mismatchSamples.length < 3) {
                    mismatchSamples.push(src);
                }
            }

            return {
                tokens: tokens,
                missing: missing,
                count: count,
                mismatchSamples: mismatchSamples,
            };
        },

        event: function (name, props) {
            props = props || {};
            var merged = this._pageProps();
            for (var k in props) {
                if (Object.prototype.hasOwnProperty.call(props, k)) {
                    merged[k] = props[k];
                }
            }
            var eventProps = {};
            var skip = { page_path: 1, page_type: 1, page_title: 1 };
            for (var j in merged) {
                if (!skip[j]) {
                    eventProps[j] = merged[j];
                }
            }
            this.queue.push({
                event_name: name,
                client_ts: now(),
                page_path: merged.page_path,
                page_type: merged.page_type,
                page_title: merged.page_title,
                props: eventProps,
            });
            if (this.queue.length >= 15) {
                this.flush(false);
            }
        },

        click: function (elementId, extra) {
            extra = extra || {};
            var ctx = this._contextSnapshot();
            extra.element_id = elementId;
            for (var k in ctx) {
                extra[k] = ctx[k];
            }
            this.event('click', extra);
        },

        fieldInteract: function (field, action, extra) {
            extra = extra || {};
            extra.field = field;
            extra.action = action;
            this.lastFieldInteract = field;
            if (!this.firstFieldInteractAt) {
                this.firstFieldInteractAt = now();
            }
            if (field !== 'order_type') {
                delete extra.value;
            }
            this.event('field_interact', extra);
        },

        validationError: function (errorField) {
            this.event('validation_error', { error_field: errorField || this.lastFieldInteract || 'unknown' });
        },

        areaLoad: function (step, status, extra) {
            extra = extra || {};
            extra.step = step;
            extra.status = status;
            if (extra.order_type == null) {
                var ot = document.querySelector('input[name="order_type"]:checked');
                if (ot) {
                    extra.order_type = ot.value;
                }
            }
            this.event('area_load', extra);
        },

        orderSubmit: function (extra) {
            extra = extra || {};
            if (this.page.goodsId != null && extra.goods_id == null) {
                extra.goods_id = this.page.goodsId;
            }
            if (this.checkoutEnteredAt) {
                extra.checkout_duration_sec = Math.round((now() - this.checkoutEnteredAt) / 1000);
            }
            this.event('order_submit', extra);
            this.flush(true);
        },

        orderSubmitError: function (extra) {
            this.event('order_submit_error', extra || {});
        },

        messageSubmit: function () {
            this.event('message_submit', {});
            this.flush(true);
        },

        messageSubmitError: function (extra) {
            this.event('message_submit_error', extra || {});
        },

        orderCheckSuccess: function () {
            this.event('order_check_success', {});
            this.flush(true);
        },

        _bindClicks: function () {
            var self = this;
            document.addEventListener(
                'click',
                function (e) {
                    var el = e.target.closest('[data-track]');
                    if (!el) {
                        return;
                    }
                    var id = el.getAttribute('data-track');
                    if (!id) {
                        return;
                    }
                    var extra = {
                        click_zone: el.getAttribute('data-track-zone') || 'content',
                    };
                    if (el.tagName === 'A') {
                        var href = el.getAttribute('href');
                        if (href) {
                            try {
                                extra.target_path = new URL(href, global.location.origin).pathname;
                            } catch (err) {
                                extra.target_path = href;
                            }
                        }
                    }
                    var gid = el.getAttribute('data-goods-id');
                    if (gid) {
                        extra.goods_id = parseInt(gid, 10) || gid;
                    }
                    var bid = el.getAttribute('data-banner-id');
                    if (bid) {
                        extra.banner_id = bid;
                    }
                    self.click(id, extra);

                    if (id.indexOf('submit') !== -1 || id.indexOf('checkout_submit') !== -1) {
                        self.submitClickedAt = now();
                    }

                    if (isInternalLink(el)) {
                        self._sendPageLeave('link_click', extra.target_path);
                        self.flush(true);
                    }
                },
                true
            );
        },

        _bindForms: function () {
            var self = this;
            ['order-form', 'message-form', 'check-form'].forEach(function (formId) {
                var form = document.getElementById(formId);
                if (!form) {
                    return;
                }
                form.addEventListener(
                    'focusin',
                    function (e) {
                        var t = e.target;
                        if (!t || !t.name) {
                            return;
                        }
                        self.fieldInteract(t.name, 'focus', { filled: self._hasValue(t) });
                    },
                    true
                );
                form.addEventListener(
                    'focusout',
                    function (e) {
                        var t = e.target;
                        if (!t || !t.name) {
                            return;
                        }
                        self.fieldInteract(t.name, 'blur', { filled: self._hasValue(t) });
                    },
                    true
                );
                form.addEventListener('change', function (e) {
                    var t = e.target;
                    if (!t || !t.name) {
                        return;
                    }
                    var extra = {};
                    if (t.name === 'order_type') {
                        extra.value = String(t.value);
                    }
                    self.fieldInteract(t.name, 'change', extra);
                });
            });
        },

        _hasValue: function (el) {
            if (!el) {
                return false;
            }
            if (el.type === 'checkbox' || el.type === 'radio') {
                return el.checked;
            }
            return String(el.value || '').length > 0;
        },

        _bindScroll: function () {
            var self = this;
            var ticking = false;
            var articleEl = document.querySelector('.article-content') || document.querySelector('.acticle-content');

            function update() {
                ticking = false;
                var docEl = document.documentElement;
                var scrollTop = global.pageYOffset || docEl.scrollTop;
                var viewport = global.innerHeight || docEl.clientHeight;
                var docHeight = Math.max(docEl.scrollHeight, document.body.scrollHeight) - viewport;
                var percent = 0;
                var target = 'document';

                if (articleEl && (self.page.type === 'news_detail' || articleEl.offsetHeight > 100)) {
                    target = 'article';
                    var rect = articleEl.getBoundingClientRect();
                    var articleTop = scrollTop + rect.top;
                    var articleHeight = articleEl.offsetHeight;
                    var viewed = scrollTop + viewport - articleTop;
                    percent =
                        articleHeight > 0 ? Math.min(100, Math.max(0, Math.round((viewed / articleHeight) * 100))) : 0;
                } else if (docHeight > 0) {
                    percent = Math.min(100, Math.max(0, Math.round((scrollTop / docHeight) * 100)));
                }

                if (scrollTop > 20 && !self.scroll.firstScrollAt) {
                    self.scroll.firstScrollAt = now();
                }

                if (percent > self.scroll.maxPercent) {
                    self.scroll.maxPercent = percent;
                }
                self.scroll.target = target;

                [25, 50, 75, 100].forEach(function (m) {
                    if (percent >= m && !self.scroll.milestones[m]) {
                        self.scroll.milestones[m] = true;
                        self.event('scroll_milestone', { percent: m, scroll_target: target });
                    }
                });
            }

            global.addEventListener(
                'scroll',
                function () {
                    if (!ticking) {
                        ticking = true;
                        setTimeout(update, SCROLL_THROTTLE_MS);
                    }
                },
                { passive: true }
            );
            update();
        },

        _bindBlocks: function () {
            if (!('IntersectionObserver' in global)) {
                return;
            }
            var self = this;
            var io = new IntersectionObserver(
                function (entries) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting || entry.intersectionRatio < BLOCK_THRESHOLD) {
                            return;
                        }
                        var el = entry.target;
                        var blockId = el.getAttribute('data-track-block');
                        if (!blockId || self.blocks.seenSet[blockId]) {
                            return;
                        }
                        self.blocks.seenSet[blockId] = true;
                        self.blocks.seen.push(blockId);
                        self.blocks.times[blockId] = now();
                        self.blocks.lastId = blockId;
                        self.event('block_view', { block_id: blockId });
                    });
                },
                { threshold: [BLOCK_THRESHOLD] }
            );
            document.querySelectorAll('[data-track-block]').forEach(function (el) {
                io.observe(el);
            });
        },

        _bindLeave: function () {
            var self = this;
            global.addEventListener('pagehide', function () {
                self._sendPageLeave('close_or_hidden');
                self.flush(true);
            });
            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'hidden') {
                    self._sendPageLeave('close_or_hidden');
                    self.flush(true);
                }
            });
        },

        _engagementType: function (durationSec) {
            var sp = this.scroll.maxPercent;
            if (durationSec < 3 && sp < 10) {
                return 'bounce';
            }
            if (durationSec < 8 && sp < 15) {
                return 'quick_navigate';
            }
            if (sp >= 90 && this.page.type === 'news_detail') {
                return 'deep_read';
            }
            if (sp >= 50 || durationSec >= 60) {
                return 'read';
            }
            if (sp >= 10) {
                return 'skim';
            }
            return 'bounce';
        },

        _sendPageLeave: function (exitType, targetPath) {
            if (this.leftSent) {
                return;
            }
            this.leftSent = true;
            var durationSec = Math.round((now() - this.enteredAt) / 1000);
            var lastBlock = this.blocks.lastId;
            var lastBlockDur = 0;
            if (lastBlock && this.blocks.times[lastBlock]) {
                lastBlockDur = Math.round((now() - this.blocks.times[lastBlock]) / 1000);
            }

            var props = {
                duration_sec: durationSec,
                max_scroll_percent: this.scroll.maxPercent,
                scroll_target: this.scroll.target,
                scroll_milestones: Object.keys(this.scroll.milestones).map(Number),
                time_to_first_scroll_sec: this.scroll.firstScrollAt
                    ? Math.round((this.scroll.firstScrollAt - this.enteredAt) / 1000)
                    : 0,
                exit_type: exitType || 'close_or_hidden',
                engagement_type: this._engagementType(durationSec),
                blocks_seen: this.blocks.seen.slice(),
                last_block_id: lastBlock,
                exit_after_block: lastBlock,
                last_block_duration_sec: lastBlockDur,
            };

            if (targetPath) {
                props.target_path = targetPath;
            }

            if (this.page.type === 'checkout') {
                props.checkout_outcome = 'abandoned';
                if (this.submitClickedAt) {
                    props.checkout_outcome = 'error';
                }
                if (this.firstFieldInteractAt) {
                    props.time_to_first_field_sec = Math.round((this.firstFieldInteractAt - this.enteredAt) / 1000);
                }
                if (this.submitClickedAt) {
                    props.time_to_submit_sec = Math.round((this.submitClickedAt - this.enteredAt) / 1000);
                }
            }

            this.event('page_leave', props);
        },

        flush: function (sync) {
            if (!this.queue.length) {
                return;
            }
            var body = {
                platform: this.platform,
                visitor_id: this.visitorId,
                session_id: this.sessionId,
                page_view_id: this.pageViewId,
                events: this.queue.splice(0, this.queue.length),
            };
            var json = JSON.stringify(body);

            if (sync && navigator.sendBeacon) {
                try {
                    navigator.sendBeacon(COLLECT_URL, new Blob([json], { type: 'application/json' }));
                    return;
                } catch (e) {
                    /* fall through */
                }
            }

            if (typeof fetch !== 'undefined') {
                var payload = json;
                fetch(COLLECT_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: payload,
                    keepalive: true,
                    credentials: 'same-origin',
                }).catch(function () {
                    setTimeout(function () {
                        fetch(COLLECT_URL, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: payload,
                            keepalive: true,
                            credentials: 'same-origin',
                        }).catch(function () {});
                    }, 2000);
                });
            }
        },
    };

    global.Track = Track;
})(window);
