import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

/** 與 initHeroCarouselScrollCollapse 一致：≤1024 視為移動端（不 pin、不縮小 hero） */
const HOME_MOBILE_MQ = '(max-width: 1024px)';
const HOME_DESKTOP_MQ = '(min-width: 1025px)';

function isHomeMobileViewport() {
    return window.matchMedia(HOME_MOBILE_MQ).matches;
}

function isHomeLikePage() {
    const page = document.body?.dataset?.page;
    const path = (window.location.pathname || '').replace(/\/+$/, '');
    return page === 'home' || path === '';
}

function initSloganEntrance() {
    const slogan = document.querySelector('.index-banner .slogan');
    if (!slogan || slogan.dataset.gsapSlogan === '1') {
        return;
    }

    const chars = Array.from(slogan.querySelectorAll('.slogan__char'));
    if (!chars.length) {
        return;
    }

    slogan.dataset.gsapSlogan = '1';

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
        gsap.set(slogan, { opacity: 1 });
        return;
    }

    gsap.set(slogan, { opacity: 1 });
    gsap.set(chars, { opacity: 0, y: 18 });
    gsap.to(chars, {
        opacity: 1,
        y: 0,
        duration: 0.55,
        stagger: 0.055,
        ease: 'power2.out',
        delay: 1,
    });
}

function initSloganScrollFade() {
    const banner = document.querySelector('.index-banner');
    const sloganBox = banner?.querySelector('.slogan-box');
    if (!banner || !sloganBox) {
        return;
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
        return;
    }

    const sloganFadeEnd = () =>
        window.matchMedia('(max-width: 1024px)').matches ? 'bottom 80%' : 'bottom 50%';

    gsap.fromTo(
        sloganBox,
        { opacity: 1 },
        {
            opacity: 0,
            ease: 'none',
            scrollTrigger: {
                trigger: banner,
                start: 'top top',
                end: sloganFadeEnd,
                scrub: true,
                invalidateOnRefresh: true,
            },
        }
    );
}

/**
 * 首頁 logo：內嵌 C_white.svg，字標 path 隨 --header-solid 白→黑（與導覽同步）
 */
async function initHomeHeaderLogoInline() {
    if (!isHomeLikePage()) {
        return;
    }

    const logoLink = document.querySelector('.main-header .logo');
    const img = logoLink?.querySelector('img[src]');
    if (!logoLink || !img || logoLink.dataset.logoInline === '1') {
        return;
    }

    const src = img.getAttribute('src');
    if (!src) {
        return;
    }

    try {
        const response = await fetch(src, { credentials: 'same-origin' });
        if (!response.ok) {
            return;
        }

        const markup = await response.text();
        const doc = new DOMParser().parseFromString(markup, 'image/svg+xml');
        const svg = doc.querySelector('svg');
        if (!svg || doc.querySelector('parsererror')) {
            return;
        }

        svg.classList.add('logo__svg');
        svg.setAttribute('aria-hidden', 'true');
        svg.setAttribute('focusable', 'false');

        const toneSelector =
            '.logoinner, path[fill="#000"], path[fill="#000000"], path[fill="#fff"], path[fill="#FFF"], path[fill="#ffffff"], path[fill="#FFFFFF"]';
        svg.querySelectorAll(toneSelector).forEach((node) => {
            node.classList.add('logo__tone');
            node.removeAttribute('fill');
        });

        const alt = img.getAttribute('alt');
        if (alt && !logoLink.getAttribute('aria-label')) {
            logoLink.setAttribute('aria-label', alt);
        }

        img.replaceWith(svg);
        logoLink.dataset.logoInline = '1';
    } catch {
        /* 保留 img 作為後備 */
    }
}

/**
 * 首頁 header：導覽字色等依 --header-solid（0→1）漸變；
 * 桌面白底由 pin 結束後 .main-header--home-solid；移動端改由捲動距離觸發
 * @param {import('lenis').default | undefined} lenis
 */
function initHomeHeaderScrollSolid(lenis) {
    if (!isHomeLikePage()) {
        return null;
    }

    const header = document.querySelector('.main-header');
    if (!header) {
        return null;
    }

    header.classList.add('main-header--home-scroll');
    header.style.setProperty('--header-solid', '0');

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
        header.style.setProperty('--header-solid', '1');
        header.classList.add('main-header--home-solid');
        header.classList.add('main-header--home-ready');
        return header;
    }

    const getScrollSpan = () => Math.max(120, Math.min(280, window.innerHeight * 0.28));

    const readScrollY = () => {
        const native = window.scrollY ?? document.documentElement.scrollTop ?? 0;
        if (native <= 0) {
            return 0;
        }
        if (lenis && typeof lenis.scroll === 'number' && !Number.isNaN(lenis.scroll)) {
            return lenis.scroll;
        }
        return native;
    };

    const update = () => {
        const y = readScrollY();
        const span = getScrollSpan();
        const p = Math.min(1, Math.max(0, y / span));
        header.style.setProperty('--header-solid', p.toFixed(4));

        if (isHomeMobileViewport()) {
            if (y < 8) {
                header.classList.remove('main-header--home-solid');
            } else {
                header.classList.toggle('main-header--home-solid', p >= 0.999);
            }
        }
    };

    update();
    gsap.ticker.add(update);
    window.addEventListener('resize', update, { passive: true });

    return header;
}

function syncHeaderSolidBgFromPin(header, st) {
    if (!header) {
        return;
    }
    const scrollY = window.scrollY ?? document.documentElement.scrollTop ?? 0;
    if (scrollY < 8) {
        header.classList.remove('main-header--home-solid');
        return;
    }
    const solid = !!(st && st.progress >= 0.999);
    header.classList.toggle('main-header--home-solid', solid);
}

function clearHeroCollapseInlineStyles(heroCarousel, coreSec) {
    gsap.set(heroCarousel, {
        clearProps: 'top,left,right,bottom,width,height,borderRadius,transform',
    });
    if (coreSec) {
        gsap.set(coreSec, { clearProps: 'bottom' });
    }
}

function initHeroCarouselScrollCollapse(header) {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (prefersReducedMotion) {
        header?.classList.add('main-header--home-solid');
        header?.classList.add('main-header--home-ready');
        return;
    }

    const banner = document.querySelector('.index-banner');
    const heroCarousel = document.getElementById('hero-video-carousel');
    const coreSec = banner?.querySelector('.core-sec--hero');
    if (!banner || !heroCarousel) {
        header?.classList.add('main-header--home-ready');
        return;
    }

    const mm = gsap.matchMedia();

    mm.add(HOME_MOBILE_MQ, () => {
        clearHeroCollapseInlineStyles(heroCarousel, coreSec);
        header?.classList.add('main-header--home-ready');

        return () => {
            clearHeroCollapseInlineStyles(heroCarousel, coreSec);
        };
    });

    mm.add(HOME_DESKTOP_MQ, () => {
        let heroCarouselMediaOff = false;

        const syncHeroCarouselMedia = (progress) => {
            const off = progress >= 0.92;
            if (off === heroCarouselMediaOff) {
                return;
            }
            heroCarouselMediaOff = off;
            if (off && typeof window.pauseHeroVideoCarousel === 'function') {
                window.pauseHeroVideoCarousel();
            } else if (!off && typeof window.resumeHeroVideoCarousel === 'function') {
                window.resumeHeroVideoCarousel();
            }
        };

        const insetEnd = '72px';

        gsap.set(heroCarousel, {
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            width: 'auto',
            height: 'auto',
            clearProps: 'transform',
        });

        /*
         * pin 整段 .index-banner：四邊等距 inset 縮小（非 scale），結束後整區跟著頁面滑走
         */
        const collapseTl = gsap.timeline({
            scrollTrigger: {
                trigger: banner,
                start: 'top top',
                end: 'bottom 70%',
                pin: true,
                scrub: true,
                anticipatePin: 1,
                invalidateOnRefresh: true,
                onUpdate(self) {
                    syncHeroCarouselMedia(self.progress);
                    syncHeaderSolidBgFromPin(header, self);
                },
                onRefresh(self) {
                    syncHeaderSolidBgFromPin(header, self);
                },
            },
        });

        collapseTl.to(
            heroCarousel,
            {
                top: insetEnd,
                left: insetEnd,
                right: insetEnd,
                bottom: insetEnd,
                borderRadius: '5vmin',
                ease: 'none',
                duration: 1,
            },
            0
        );

        if (coreSec) {
            collapseTl.to(
                coreSec,
                {
                    bottom: 'calc(5% + 6vmin)',
                    ease: 'none',
                    duration: 1,
                },
                0
            );
        }

        header?.classList.add('main-header--home-ready');
        syncHeaderSolidBgFromPin(header, collapseTl.scrollTrigger);

        return () => {
            collapseTl.scrollTrigger?.kill();
            collapseTl.kill();
            clearHeroCollapseInlineStyles(heroCarousel, coreSec);
            heroCarouselMediaOff = false;
            if (typeof window.resumeHeroVideoCarousel === 'function') {
                window.resumeHeroVideoCarousel();
            }
        };
    });

    ScrollTrigger.refresh();
}

/**
 * core-sec 進場：初始化時立即加 class
 */
function initCoreSecEntrance() {
    const coreSections = document.querySelectorAll('.core-sec--hero');
    if (!coreSections.length) {
        return;
    }
    const reveal = () => {
        coreSections.forEach((section) => {
            section.classList.add('core-sec--entered');
            const items = section.querySelectorAll('.core-item');
            items.forEach((item) => {
                item.classList.add('core-item--entered');
            });
        });
    };

    // 讓初始 transform 先完成一次繪製，再切換到 entered 才會有 transition
    requestAnimationFrame(() => {
        requestAnimationFrame(reveal);
    });
}

/**
 * @param {import('lenis').default | undefined} lenis
 */
function initScrollDownHide(lenis) {
    const scrollDownLink = document.querySelector('.index-banner .scroll-down');
    if (!scrollDownLink) {
        return;
    }

    const HIDE_SCROLL_DOWN_SCROLL_PX = 50;
    let ticking = false;

    function getScrollY() {
        if (lenis && typeof lenis.scroll === 'number' && !Number.isNaN(lenis.scroll)) {
            return lenis.scroll;
        }
        return window.scrollY || window.pageYOffset || 0;
    }

    function updateScrollDownVisibility() {
        scrollDownLink.classList.toggle('hide', getScrollY() > HIDE_SCROLL_DOWN_SCROLL_PX);
        ticking = false;
    }

    function onScrollDownScroll() {
        if (ticking) {
            return;
        }
        ticking = true;
        requestAnimationFrame(updateScrollDownVisibility);
    }

    if (lenis && typeof lenis.on === 'function') {
        lenis.on('scroll', onScrollDownScroll);
    } else {
        window.addEventListener('scroll', onScrollDownScroll, { passive: true });
    }

    requestAnimationFrame(updateScrollDownVisibility);
}

/**
 * @param {import('lenis').default | undefined} lenis
 */
export async function initHomeGsapEnvironment(lenis) {
    const homeTimeline = gsap.timeline({ paused: true });
    initSloganEntrance();
    initSloganScrollFade();
    initCoreSecEntrance();
    initScrollDownHide(lenis);
    const header = initHomeHeaderScrollSolid(lenis);
    initHeroCarouselScrollCollapse(header);
    await initHomeHeaderLogoInline();

    return { gsap, ScrollTrigger, homeTimeline };
}
