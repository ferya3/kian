/**
 * آکاردئون افقیِ پروژه‌ها.
 *
 * پانلِ فعال باز می‌شود و بقیه به باریکه‌ای جمع می‌شوند که فقط نشانِ
 * دسته‌شان دیده می‌شود. کل هندسه در CSS است و اینجا فقط یک عدد نگه‌داری
 * می‌شود: شماره‌ی پانلِ باز.
 *
 * ورودِ پلکانیِ پانل‌ها هم اینجا نیست؛ همان data-reveal سراسری سایت آن را
 * می‌سازد، پس تأخیرها، IntersectionObserver و احترام به prefers-reduced-motion
 * یک‌بار نوشته شده و اینجا تکرار نمی‌شود.
 */
export default (count = 0) => ({
    count,
    index: 0,

    active(i) {
        return this.index === i;
    },

    select(i) {
        this.index = i;
    },

    /**
     * کلیدهای جهت.
     *
     * Tab خودش از روی دکمه‌ها رد می‌شود و این اضافه‌ی آن است، نه جایگزینش.
     * در چیدمان راست‌به‌چپ «بعدی» سمت چپ است، پس معنیِ کلیدها با جهتِ صفحه
     * آینه می‌شود؛ وگرنه فلشِ راست کاربر فارسی را به عقب می‌برد.
     */
    onKey(event) {
        const rtl = getComputedStyle(this.$el).direction === 'rtl';

        const moves = {
            ArrowRight: rtl ? -1 : 1,
            ArrowLeft: rtl ? 1 : -1,
            ArrowDown: 1,
            ArrowUp: -1,
            Home: -this.index,
            End: this.count - 1 - this.index,
        };

        const by = moves[event.key];
        if (by === undefined) return;

        event.preventDefault();
        this.select(Math.min(this.count - 1, Math.max(0, this.index + by)));

        // فوکوس باید همراه انتخاب برود، وگرنه فلشِ بعدی از جای قبلی حساب می‌شود
        this.$nextTick(() => this.$refs.rail?.children[this.index]?.querySelector('button')?.focus());
    },
});
