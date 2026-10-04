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

    /**
     * نزدیک‌ترین بخش به جایی که انگشت خورد — راهِ انتخاب روی گوشی.
     *
     * شش نشانه روی نقشه‌ای که ۳۵۰ پیکسل عرض دارد یعنی نزدیک‌ترین دو تا حدود
     * ۴۵ پیکسل از هم فاصله دارند؛ با هدفِ لمسیِ ۴۴ پیکسلی، هدف‌ها عملاً به هم
     * می‌چسبند و انگشت نمی‌تواند بینشان انتخاب کند. برای همین نشانه‌ها روی
     * گوشی اصلاً دکمه نبودند و نقشه یک عکسِ بی‌جان بود.
     *
     * این‌طور، به‌جای شش هدفِ کوچکِ درهم، کلِ نقشه یک هدف است و هر ضربه به
     * نزدیک‌ترین نشانه می‌رسد: هیچ نقطه‌ای از نقشه بی‌جواب نمی‌ماند و هیچ دو
     * هدفی هم تداخل ندارند.
     *
     * فاصله در پیکسل حساب می‌شود و نه در درصد: نقشه ۱٫۶ برابر پهن‌تر از
     * بلند است، پس یک درصدِ افقی و یک درصدِ عمودی یک اندازه نیستند و مقایسه‌ی
     * درصدی، انتخاب را به‌سمت بالا و پایین کج می‌کرد.
     */
    selectNearest(event) {
        const rect = this.$refs.tapLayer?.getBoundingClientRect();

        if (! rect || ! rect.width || ! rect.height) return;

        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;

        let nearest = 0;
        let shortest = Infinity;

        this.sections.forEach((section, index) => {
            const dx = (section.hotspotX / 100) * rect.width - x;
            const dy = (section.hotspotY / 100) * rect.height - y;
            // مجذورِ فاصله کافی است؛ فقط با هم مقایسه می‌شوند
            const distance = dx * dx + dy * dy;

            if (distance < shortest) {
                shortest = distance;
                nearest = index;
            }
        });

        this.select(nearest);
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
