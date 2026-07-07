import './bootstrap';
import Lenis from 'lenis';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

function initScrollEngine() {
    const lenis = new Lenis({
        duration: 1.1,
        smoothWheel: true,
        wheelMultiplier: 0.9,
        touchMultiplier: 1.2,
    });

    lenis.on('scroll', ScrollTrigger.update);

    gsap.ticker.add((time) => {
        lenis.raf(time * 1000);
    });

    gsap.ticker.lagSmoothing(0);

    return { lenis, gsap, ScrollTrigger };
}

/**
 * @param {import('lenis').default} lenis
 */
async function initHomeOnlyAnimations(lenis) {
    const page = document.body?.dataset?.page;
    const isRootPath = window.location.pathname.replace(/\/+$/, '') === '';
    const isHome = page === 'home' || isRootPath;

    if (!isHome || !document.querySelector('.index-banner')) {
        return null;
    }

    const { initHomeGsapEnvironment } = await import('./pages/home-gsap');
    return initHomeGsapEnvironment(lenis);
}

function exposeScrollApi(state) {
    window.__scrollFx = {
        ...state,
        refresh: () => ScrollTrigger.refresh(),
        destroy: () => {
            ScrollTrigger.getAll().forEach((trigger) => trigger.kill());
            state.lenis?.destroy();
        },
    };
}

async function bootstrap() {
    const hasIndexBanner = !!document.querySelector('.index-banner');

    if (!hasIndexBanner) {
        return;
    }

    const state = initScrollEngine();
    const homeState = await initHomeOnlyAnimations(state.lenis);

    exposeScrollApi({
        ...state,
        ...(homeState || {}),
    });

    window.addEventListener('load', () => {
        ScrollTrigger.refresh();
    });
}

bootstrap();
