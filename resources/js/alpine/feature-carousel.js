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

/** فاصله‌ی بیضی تا شرحِ زیرش. */
const TEXT_GAP = 10;

export default (count = 0) => ({
    count,
    step: 0,
    paused: false,
    reduced: false,
    timer: null,
    /** جایی که چرخ برای شرحِ مرحله‌ی فعال باز می‌کند. */
    room: 0,

    /**
     * آیا چرخ روی صفحه هست؟
     *
     * زیر lg فقط کارت می‌ماند و چرخ پنهان است، پس نقش‌های tablist/tab هم
     * آنجا نیستند — و tabpanelِ بی‌tablist، ساختارِ شکسته است. این پرچم به
     * مارک‌آپ می‌گوید کدام نقش را بگیرد، و همان مرز را دنبال می‌کند که CSS
     * دنبال می‌کند تا دو جا دو تعریف نداشته باشیم.
     */
    wide: false,

    init() {
        this.reduced = prefersReducedMotion();

        const desktop = window.matchMedia('(min-width: 1024px)');
        this.wide = desktop.matches;
        desktop.addEventListener('change', (event) => {
            this.wide = event.matches;
        });

        this.$watch('paused', () => this.schedule());
        this.$watch('step', () => this.measure());
        this.schedule();

        this.$nextTick(() => this.measure());

        // طول شرح با عرض پنجره عوض می‌شود، پس اندازه دوباره گرفته می‌شود
        this.onResize = () => this.measure();
        window.addEventListener('resize', this.onResize, { passive: true });
    },

    destroy() {
        clearInterval(this.timer);
        window.removeEventListener('resize', this.onResize);
    },

    /**
     * ارتفاع شرحِ مرحله‌ی فعال.
     *
     * شرح زیر بیضی می‌نشیند و absolute است، پس خودش ردیف را بلند نمی‌کند —
     * در عوض ردیف‌های پایین‌تر به همین اندازه هل داده می‌شوند تا رویش نیفتند.
     * اندازه‌اش خوانده می‌شود و حدس زده نمی‌شود، چون شرح‌ها یک تا چهار خطی‌اند.
     */
    measure() {
        const text = this.$refs.wheel?.children[this.index]?.querySelector('[data-text]');

        this.room = text ? text.offsetHeight + TEXT_GAP : 0;
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

        // فقط ردیف‌های پایین‌ترِ مرحله‌ی فعال کنار می‌روند؛ بالایی‌ها سر جایشان
        // می‌مانند، وگرنه کل چرخ با هر قدم بالا و پایین می‌پرد.
        const push = d > 0 ? this.room : 0;

        return {
            transform: `translateY(${d * ITEM_H + push}px)`,
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
