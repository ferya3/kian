import Alpine from 'alpinejs';

import blockViewer from './alpine/block-viewer';
import { accordion, filterable, tabs } from './alpine/collections';
import factoryMap from './alpine/factory-map';
import featureCarousel from './alpine/feature-carousel';
import mobileNav from './alpine/mobile-nav';
import productFinder from './alpine/product-finder';
import projectSelector from './alpine/project-selector';
import siteHeader from './alpine/site-header';
import { initCountUp } from './modules/count-up';
import { initParallax } from './modules/parallax';
import { initReveal } from './modules/reveal';
import { initSmoothScroll } from './modules/smooth-scroll';
import { prefersReducedMotion } from './modules/motion';

Alpine.data('siteHeader', siteHeader);
Alpine.data('mobileNav', mobileNav);
Alpine.data('productFinder', productFinder);
Alpine.data('blockViewer', blockViewer);
Alpine.data('factoryMap', factoryMap);
Alpine.data('featureCarousel', featureCarousel);
Alpine.data('projectSelector', projectSelector);
Alpine.data('filterable', filterable);
Alpine.data('accordion', accordion);
Alpine.data('tabs', tabs);

window.Alpine = Alpine;

/*
| بخش‌های زیرِ صفحه تا نزدیک‌شدنِ اسکرول راه‌اندازی نمی‌شوند.
|
| Alpine در شروع کلِ صفحه را می‌پیماید؛ روی صفحه‌ی اصلی ۲۵۰۰ عنصر، و روی
| گوشی یک کارِ پیوسته‌ی نیم‌ثانیه‌ای که صفحه را از پاسخ‌دادن به لمس باز
| می‌داشت (Lighthouse: TBT). x-ignore آن بخش‌ها را از پیمایشِ نخست بیرون
| می‌گذارد و ۶۰۰ پیکسل پیش از رسیدنِ اسکرول، initTree راه‌شان می‌اندازد.
*/
const lazy = Array.from(document.querySelectorAll('[data-alpine-lazy]'));
if ('IntersectionObserver' in window) {
    /*
    | x-data روی پوسته لازم است و نه فقط x-ignore: Alpine هر x-data را ریشه‌ای
    | مستقل می‌شمارد و از راهِ querySelector پیدایش می‌کند، پس x-ignore روی
    | والدی که خودش کامپوننت نیست، جلوی کامپوننت‌های درونش را نمی‌گرفت. با
    | x-data، پوسته ریشه است و درونی‌ها زیرِ آن — و x-ignore همه را کنار می‌گذارد.
    */
    lazy.forEach((el) => {
        el.setAttribute('x-data', '');
        el.setAttribute('x-ignore', '');
    });
}

/*
| کامپوننتی که فقط با یک رویداد به کار می‌آید — منوی موبایل — تا آن رویداد
| راه‌اندازی نمی‌شود. نخستین رویداد اینجا گرفته می‌شود، کامپوننت راه می‌افتد
| و همان رویداد دوباره فرستاده می‌شود تا شنونده‌ی تازه‌اش آن را بگیرد.
*/
document.querySelectorAll('[data-alpine-on]').forEach((el) => {
    const event = el.dataset.alpineOn;
    el.setAttribute('x-ignore', '');

    window.addEventListener(event, (e) => {
        /*
        | رویدادِ اصلی همین‌جا می‌ایستد و پس از راه‌اندازی دوباره فرستاده
        | می‌شود. اگر ادامه می‌داد، شنونده‌ی تازه‌ی Alpine هم آن را می‌گرفت —
        | هم رویدادِ اصلی و هم تکرارش: منو باز و فوراً بسته می‌شد.
        */
        e.stopImmediatePropagation();
        el.removeAttribute('x-ignore');
        delete el._x_ignore;
        Alpine.initTree(el);
        queueMicrotask(() => window.dispatchEvent(new CustomEvent(event, { detail: e.detail })));
    }, { once: true, capture: true });
});

Alpine.start();

if (lazy.length && 'IntersectionObserver' in window) {
    const wake = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            wake.unobserve(el);
            el.removeAttribute('x-ignore');
            el._x_ignore = false;
            Array.from(el.children).forEach((child) => Alpine.initTree(child));
        });
    }, { rootMargin: '600px 0px' });

    lazy.forEach((el) => wake.observe(el));
}

const boot = () => {
    initSmoothScroll();
    initReveal();
    initCountUp();
    initParallax();
};

document.readyState === 'loading'
    ? document.addEventListener('DOMContentLoaded', boot)
    : boot();

/*
| ویدئوی هیرو: فقط دسکتاپ، فقط پس از load، و نه برای کسی که «کاهش حرکت» یا
| «صرفه‌جویی در داده» را خواسته. روی گوشی پوستر می‌ماند — hero.blade.php.
*/
const startHeroVideo = () => {
    const video = document.querySelector('video[data-hero-video]');
    if (!video) return;

    const wide = window.matchMedia('(min-width: 1024px)').matches;
    const saveData = navigator.connection?.saveData;
    if (!wide || saveData || prefersReducedMotion()) return;

    video.src = video.dataset.heroVideo;
    video.play().catch(() => {});
};

document.readyState === 'complete'
    ? startHeroVideo()
    : window.addEventListener('load', startHeroVideo, { once: true });
