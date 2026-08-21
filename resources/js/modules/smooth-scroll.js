import Lenis from 'lenis';
import { prefersReducedMotion } from './motion';

let instance = null;

/**
 * اسکرول نرم — فقط اگر کاربر آن را نخواسته باشد که غیرفعال شود.
 * روی موبایل خاموش می‌ماند: اسکرول بومی iOS/Android از هر پیاده‌سازی JS بهتر است.
 */
export function initSmoothScroll() {
    if (prefersReducedMotion() || window.innerWidth < 1024 || !window.matchMedia('(pointer: fine)').matches) {
        return null;
    }

    instance = new Lenis({
        duration: 1.05,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smoothWheel: true,
        wheelMultiplier: 0.9,
    });

    const raf = (time) => {
        instance.raf(time);
        requestAnimationFrame(raf);
    };

    requestAnimationFrame(raf);

    // لنگرهای داخلی باید از همان موتور اسکرول عبور کنند
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href^="#"]');
        if (!link) return;

        const id = link.getAttribute('href');
        if (id.length < 2) return;

        const target = document.querySelector(id);
        if (!target) return;

        event.preventDefault();
        instance.scrollTo(target, { offset: -96 });
        target.setAttribute('tabindex', '-1');
        target.focus({ preventScroll: true });
    });

    return instance;
}

export const lenis = () => instance;

export function stopScroll() {
    instance?.stop();
}

export function startScroll() {
    instance?.start();
}
