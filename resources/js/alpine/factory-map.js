/**
 * نقشه‌ی تعاملی کارخانه.
 * هر hotspot یک بخش واقعی خط تولید است؛ انتخاب آن پنل کناری را عوض می‌کند.
 */
export default (sections = []) => ({
    sections,
    active: 0,
    autoplay: true,
    timer: null,

    init() {
        this.start();

        // تعامل کاربر، پخش خودکار را برای همیشه متوقف می‌کند
        this.$el.addEventListener('pointerdown', () => this.stop(), { once: true });
        this.$el.addEventListener('keydown', (e) => {
            if (['ArrowLeft', 'ArrowRight'].includes(e.key)) this.stop();
        });
    },

    start() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        this.timer = setInterval(() => {
            this.active = (this.active + 1) % this.sections.length;
        }, 5200);
    },

    stop() {
        this.autoplay = false;
        clearInterval(this.timer);
    },

    select(index) {
        this.stop();
        this.active = index;
    },

    /** پیمایش hotspotها با فلش — روی نقشه‌های تصویری اغلب فراموش می‌شود. */
    move(step) {
        const next = (this.active + step + this.sections.length) % this.sections.length;
        this.active = next;
        this.$refs[`spot${next}`]?.focus();
    },

    get current() {
        return this.sections[this.active];
    },
});
