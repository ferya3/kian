import { prefersReducedMotion } from './motion';

/**
 * پارالاکس سبک برای لایه‌های صحنه‌ی قهرمان.
 * فقط transform تغییر می‌کند تا layout دوباره محاسبه نشود.
 *
 * روی موبایل خاموش می‌ماند — با همان شرطی که اسکرول نرم دارد.
 *
 * دلیلش صرفاً کارایی نیست: روی iOS و اندروید نوار آدرس هنگام اسکرول جمع و
 * باز می‌شود و ارتفاع viewport عوض می‌شود، پس لایه‌ها وسط حرکت می‌پرند.
 * تزیینی که روی دسکتاپ عمق می‌سازد، اینجا فقط تکان می‌خورد و هر فریم از
 * بودجه‌ی اسکرولِ انگشت می‌دزدد.
 */
export function initParallax(root = document) {
    const layers = Array.from(root.querySelectorAll('[data-parallax]'));
    if (!layers.length || prefersReducedMotion()) return;

    if (window.innerWidth < 1024 || !window.matchMedia('(pointer: fine)').matches) return;

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
