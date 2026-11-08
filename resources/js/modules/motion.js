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
     *
     * تشخیصِ «همین حالا دیده می‌شود» با یک ناظرِ دوم است و نه
     * getBoundingClientRect: آن یکی مرورگر را وادار به چیدمانِ همزمانِ کلِ
     * صفحه می‌کرد، درست وسطِ راه‌اندازی — Lighthouse: forced reflow.
     */
    const first = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            first.unobserve(entry.target);
            if (entry.isIntersecting) callback(entry.target);
            else observer.observe(entry.target);
        });
    }, { threshold: 0 });

    items.forEach((item) => first.observe(item));
}
