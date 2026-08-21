/**
 * قواعد پایه‌ی حرکت.
 * یک منبع واحد برای اینکه «آیا اصلاً اجازه‌ی انیمیشن داریم؟»
 */
const query = window.matchMedia('(prefers-reduced-motion: reduce)');

export const prefersReducedMotion = () => query.matches;

export function onMotionPreferenceChange(callback) {
    query.addEventListener('change', () => callback(query.matches));
}

/** یک بار اجرا کن، وقتی المان وارد viewport شد. */
export function observeOnce(elements, callback, options = {}) {
    const items = Array.from(elements);
    if (!items.length) return;

    if (!('IntersectionObserver' in window)) {
        items.forEach(callback);
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            callback(entry.target);
            observer.unobserve(entry.target);
        });
    }, { rootMargin: '0px 0px -80px 0px', threshold: 0.01, ...options });

    /*
     * حاشیه‌ی منفیِ بالا حس «ظاهر شدن هنگام اسکرول» را می‌سازد، اما عنصری که
     * همان ابتدا کمی زیر خط تا است هرگز فعال نمی‌شد — مثل نوار شاخص‌ها روی
     * نمایشگر ۹۰۰ پیکسلی. هرچه در viewport واقعی دیده می‌شود، بی‌درنگ اجرا شود.
     */
    const visibleNow = [];

    items.forEach((item) => {
        const box = item.getBoundingClientRect();
        const inView = box.top < window.innerHeight && box.bottom > 0;

        if (inView) visibleNow.push(item);
        else observer.observe(item);
    });

    if (visibleNow.length) {
        requestAnimationFrame(() => visibleNow.forEach(callback));
    }
}
