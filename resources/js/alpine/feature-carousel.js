import { prefersReducedMotion } from '../modules/motion';

/**
 * کاروسل دو‌پانله.
 *
 * سمت راست چرخی از چیپ‌هاست که عمودی می‌چرخد و مرحله‌ی فعال وسطش می‌نشیند؛
 * سمت چپ دسته‌ای از کارت‌ها که مرحله‌ی فعال رو به جلو می‌آید و همسایه‌هایش
 * کمی کوچک و کج پشتش می‌مانند.
 *
 * همه‌چیز حول یک عدد می‌چرخد: فاصله‌ی علامت‌دارِ هر مرحله تا مرحله‌ی فعال،
 * پیچیده‌شده در بازه‌ی [−n/2، n/2). چرخ از آن جابه‌جایی عمودی و شفافیت
 * می‌سازد و دسته از همان، عمق.
 */

/** ارتفاع هر پله‌ی چرخ — با ارتفاع خودِ چیپ یکی است تا چرخ بی‌درز بماند. */
const ITEM_H = 64;

const AUTOPLAY_MS = 3400;

export default (count = 0) => ({
    count,
    step: 0,
    paused: false,
    reduced: false,
    timer: null,

    init() {
        this.reduced = prefersReducedMotion();
        this.$watch('paused', () => this.schedule());
        this.schedule();
    },

    destroy() {
        clearInterval(this.timer);
    },

    /**
     * نوبت بعدی.
     *
     * هر بار از نو زمان‌بندی می‌شود و ادامه‌ی نوبت قبلی نیست: وقتی کاربر خودش
     * چیپی را می‌زند، نباید یک لحظه بعد کاروسل زیر دستش جلو برود.
     */
    schedule() {
        clearInterval(this.timer);

        if (this.reduced || this.paused || this.count < 2) return;

        this.timer = setInterval(() => this.step++, AUTOPLAY_MS);
    },

    get index() {
        return ((this.step % this.count) + this.count) % this.count;
    },

    /** فاصله‌ی علامت‌دار تا مرحله‌ی فعال، در بازه‌ی [−n/2، n/2). */
    offset(i) {
        const half = this.count / 2;

        return ((((i - this.index + half) % this.count) + this.count) % this.count) - half;
    },

    active(i) {
        return this.offset(i) === 0;
    },

    /**
     * همیشه رو به جلو.
     *
     * چرخ فقط یک جهت دارد، پس کلیک روی مرحله‌ای که پشت سر است هم آن را از
     * جلو می‌آورد؛ وگرنه نیمی از کلیک‌ها چرخ را برعکس می‌چرخاند و ترتیبِ
     * «از خاک تا سازه» گم می‌شود.
     */
    select(i) {
        const ahead = (i - this.index + this.count) % this.count;

        if (ahead) this.step += ahead;

        this.schedule();
    },

    /**
     * کیبورد.
     *
     * فقط چیپِ فعال tabindex دارد، پس بدون این، کاربر کیبورد وارد چرخ می‌شود
     * و هیچ راهی برای چرخاندنش ندارد. فوکوس هم باید همراه انتخاب برود، وگرنه
     * روی دکمه‌ای می‌ماند که دیگر در ترتیب Tab نیست.
     */
    onKey(event) {
        const moves = {
            ArrowDown: 1,
            ArrowUp: -1,
            Home: -this.index,
            End: this.count - 1 - this.index,
        };

        const by = moves[event.key];
        if (by === undefined) return;

        event.preventDefault();
        this.select((this.index + by + this.count) % this.count);

        this.$nextTick(() => this.$refs.wheel?.children[this.index]?.querySelector('button')?.focus());
    },

    /* ---------- چرخ ---------- */

    chipStyle(i) {
        const d = this.offset(i);

        return {
            transform: `translateY(${d * ITEM_H}px)`,
            opacity: String(Math.max(0, 1 - Math.abs(d) * 0.25)),
            zIndex: d === 0 ? '10' : '1',
            // چیپ محوشده نباید هدفِ کلیک باشد
            pointerEvents: Math.abs(d) >= 4 ? 'none' : 'auto',
        };
    },

    /* ---------- دسته‌ی کارت ---------- */

    cardStyle(i) {
        const d = this.offset(i);
        const near = d === -1 || d === 1;

        /*
         * در چیدمان راست‌به‌چپ، مرحله‌ی بعدی از سمت چپ می‌آید و مرحله‌ی قبلی
         * سمت راست می‌ماند — پس علامتِ جابه‌جایی و زاویه نسبت به نسخه‌ی
         * چپ‌به‌راست آینه می‌شود.
         */
        const x = d === -1 ? 96 : d === 1 ? -96 : 0;
        const tilt = d === -1 ? 3 : d === 1 ? -3 : 0;
        const scale = d === 0 ? 1 : near ? 0.85 : 0.7;

        return {
            transform: `translateX(${x}px) rotate(${tilt}deg) scale(${scale})`,
            opacity: d === 0 ? '1' : near ? '0.4' : '0',
            zIndex: d === 0 ? '20' : near ? '10' : '0',
            pointerEvents: d === 0 ? 'auto' : 'none',
        };
    },
});
