import { prefersReducedMotion } from './motion';

/**
 * پارالاکس سبک برای لایه‌های صحنه‌ی قهرمان.
 * فقط transform تغییر می‌کند تا layout دوباره محاسبه نشود.
 */
export function initParallax(root = document) {
    const layers = Array.from(root.querySelectorAll('[data-parallax]'));
    if (!layers.length || prefersReducedMotion()) return;

    let ticking = false;

    const update = () => {
        const y = window.scrollY;

        layers.forEach((layer) => {
            const speed = Number(layer.dataset.parallax) || 0.1;
            const scale = layer.dataset.parallaxScale ? 1 + (y / window.innerHeight) * Number(layer.dataset.parallaxScale) : 1;
            layer.style.transform = `translate3d(0, ${y * speed}px, 0) scale(${scale})`;
        });

        ticking = false;
    };

    window.addEventListener('scroll', () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(update);
    }, { passive: true });

    update();
}
