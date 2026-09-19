import { prefersReducedMotion } from '../modules/motion';

/**
 * کاروسل «فشرده».
 *
 * یک ردیف است، نه یک حلقه. ساختارش در هر ده حالت یکی است:
 *
 *     [نوارهای رد شده] [کارتِ باز] [۱] [۲] [۳] [نوارهای نرسیده]
 *                       راست ← چپ
 *
 * کارتِ باز مربع است و عرضش هیچ‌وقت عوض نمی‌شود. بادبزنِ سه‌تایی همیشه سمت
 * چپ اوست — یعنی رو به مراحلی که هنوز نیامده‌اند — و هر کارت بادبزن در همه‌ی
 * حالت‌ها دقیقاً همان عرض را دارد.
 *
 * روی سه مرحله‌ی آخر، جلوتر کارتی نمانده که بادبزن را پر کند. پیش‌تر برای پر
 * کردنش از پشت قرض گرفته می‌شد و نتیجه این بود که بادبزن دور کارتِ باز
 * می‌چرخید و الگو عوض می‌شد. حالا بادبزن همان‌جا کوتاه می‌شود و فضای آزاد به
 * نوارهای سمت راست می‌رسد: هرچه جلوتر می‌روی، تاریخچه‌ی طی‌شده پهن‌تر باز
 * می‌شود. هیچ کارتی هم پنهان نمی‌ماند — هر ده‌تا همیشه روی صفحه‌اند.
 */

/** سهم هر کارتِ بادبزن از «فضای مرجع». */
const FAN = [0.61, 0.3, 0.15];

/** کارتی که نشانگر رویش است، جا می‌گیرد. */
const STRETCHED = [0.71, 0.4, 0.25];

/** همسایه‌ها کمی عقب می‌کشند تا هزینه‌اش را بدهند. */
const SQUEEZED = [0.59, 0.28, 0.13];

/* هندسه بر حسب پیکسل. */
const MIN_SLAT = 8;
const GAP = 12;
const MAX_FAN = 3;

/*
| لغزشِ نوار بلند است چون حرکت اصلی است؛ پاسخ به نشانگر باید کوتاه باشد،
| وگرنه عرض ستون یک ثانیه بعد از رد شدن ماوس عوض می‌شود و کند به نظر می‌رسد.
*/
const SLIDE_MS = 900;
const HOVER_MS = 380;

export default (labels = []) => ({
    labels,
    count: labels.length,
    open: 0,
    hover: -1,
    sliding: false,
    desktop: false,
    reduced: false,
    rtl: true,
    timer: null,
    ticking: false,

    init() {
        this.reduced = prefersReducedMotion();
        this.rtl = getComputedStyle(document.documentElement).direction === 'rtl';

        // نقطه‌ی شکست همان lg است: پایین‌تر از آن، پانلِ ۱۶:۹ به‌تنهایی از کل
        // عرض صفحه پهن‌تر می‌شود و «فضای باقی‌مانده» منفی می‌شود.
        const wide = window.matchMedia('(min-width: 1024px)');
        const read = () => (this.desktop = wide.matches);

        read();
        wide.addEventListener('change', read);
    },

    destroy() {
        clearTimeout(this.timer);
    },

    /* ---------- هندسه ---------- */

    /** کارت‌های بادبزن: هرچه جلوتر مانده، تا سقف سه‌تا. */
    get fan() {
        return Math.max(0, Math.min(MAX_FAN, this.count - this.open - 1));
    },

    /** هرچه بادبزن نیست، نوار است — چه رد شده باشد چه نرسیده. هیچ‌کدام پنهان نیستند. */
    get slats() {
        return this.count - 1 - this.fan;
    },

    get ms() {
        if (this.reduced) return 0;
        return this.sliding ? SLIDE_MS : HOVER_MS;
    },

    /**
     * سهم هر کارتِ بادبزن از فضای مرجع.
     *
     * جمعشان با بادبزنِ کامل دقیقاً ۱ می‌شود — چه نشانگر روی کارتی باشد چه
     * نباشد — پس عرض هر کارت در همه‌ی حالت‌ها یکی است.
     */
    get shares() {
        const lit = ! this.reduced && this.hover > this.open && this.hover - this.open <= MAX_FAN
            ? this.hover - this.open - 1
            : -1;

        const base = lit === -1
            ? FAN
            : FAN.map((_, i) => (i === lit ? STRETCHED[i] : SQUEEZED[i]));

        const total = base.reduce((sum, share) => sum + share, 0);

        return base.map((share) => share / total);
    },

    /** چه کسری از فضای مرجع را بادبزنِ این حالت برمی‌دارد. */
    get taken() {
        return this.shares.slice(0, this.fan).reduce((sum, share) => sum + share, 0);
    },

    /**
     * عرض نوارها.
     *
     * فضای مرجع (--sq-room) ثابت است و در CSS تعریف می‌شود، پس عرض کارت‌های
     * بادبزن هیچ‌وقت تغییر نمی‌کند. تنها چیزی که بین حالت‌ها فرق می‌کند همین
     * است: روی سه مرحله‌ی آخر که بادبزن کوتاه می‌شود، سهمِ استفاده‌نشده بین
     * نوارها پخش می‌شود و تاریخچه‌ی سمت راست پهن‌تر باز می‌شود.
     */
    get slatWidth() {
        if (this.slats < 1) return '0px';

        /*
         * صورتِ کسر، سهمِ استفاده‌نشده‌ی بادبزن است به‌اضافه‌ی عرضِ پایه‌ی
         * نوارها. شکل سرراست‌ترش «عرض کادر منهای بقیه» بود، ولی آن‌وقت 100cqi
         * دو بار در یک calc می‌آمد — یک‌بار مستقیم و یک‌بار داخل --sq-room — و
         * مرورگر آن دو را با هم ساده نمی‌کرد و کل عبارت صفر درمی‌آمد. اینجا
         * جبرش از قبل انجام شده و 100cqi فقط یک‌بار، آن هم داخل --sq-room، هست.
         */
        const base = Math.max(0, this.count - 4) * MIN_SLAT;

        return `calc((var(--sq-room) * ${(1 - this.taken).toFixed(4)} + ${base}px) / ${this.slats})`;
    },

    get stripStyle() {
        return { '--sq-ms': `${this.ms}ms`, '--sq-slat': this.slatWidth };
    },

    panelStyle(index) {
        if (! this.desktop) {
            return { width: '', marginInlineStart: '', borderRadius: '' };
        }

        const place = index - this.open;
        let width = 'var(--sq-slat)';

        if (place === 0) {
            width = 'var(--sq-h)';
        } else if (place > 0 && place <= this.fan) {
            width = `calc(var(--sq-room) * ${this.shares[place - 1].toFixed(4)})`;
        }

        return {
            width,
            marginInlineStart: index === 0 ? '0px' : `${GAP}px`,
            // نوار باریک با شعاع ۲۰ پیکسل به قرص تبدیل می‌شود، نه نوار.
            borderRadius: `min(var(--radius-panel), calc(${width} / 2))`,
            transitionProperty: 'width',
            transitionDuration: 'var(--sq-ms)',
            transitionTimingFunction: 'var(--ease-out-expo)',
        };
    },

    /* ---------- ناوبری ---------- */

    go(index) {
        const next = Math.max(0, Math.min(this.count - 1, index));
        if (next === this.open) return;

        this.open = next;
        this.sliding = true;

        clearTimeout(this.timer);
        this.timer = setTimeout(() => (this.sliding = false), SLIDE_MS + 40);

        if (! this.desktop) this.reveal(next);
    },

    step(by) {
        this.go(this.open + by);
    },

    /** کلیک روی تیغه یا ستون: همان پانل باز شود. */
    select(index) {
        if (this.desktop && index === this.open) return;
        this.go(index);
    },

    onKey(event) {
        const forward = this.rtl ? 'ArrowLeft' : 'ArrowRight';
        const back = this.rtl ? 'ArrowRight' : 'ArrowLeft';

        const moves = {
            [forward]: 1,
            [back]: -1,
            Home: -this.open,
            End: this.count - 1 - this.open,
        };

        const by = moves[event.key];
        if (by === undefined) return;

        event.preventDefault();
        this.step(by);

        // فوکوس باید همراه انتخاب جابه‌جا شود، وگرنه روی پانلی می‌ماند که دیگر
        // tabindex ندارد و کاربر کیبورد در جای غلط گیر می‌کند.
        this.$nextTick(() => this.$refs.strip?.children[this.open]?.querySelector('button')?.focus());
    },

    /* ---------- ریل لمسی ---------- */

    reveal(index) {
        this.$refs.strip?.children[index]?.scrollIntoView({
            behavior: this.reduced ? 'auto' : 'smooth',
            block: 'nearest',
            inline: 'center',
        });
    },

    /**
     * روی گوشی، swipe جای کلیک را می‌گیرد: هر پانلی که به مرکز ریل نزدیک‌تر
     * است، همان است که متنش پایین نشان داده می‌شود.
     */
    onScroll() {
        if (this.desktop || this.ticking) return;
        this.ticking = true;

        requestAnimationFrame(() => {
            this.ticking = false;

            const rail = this.$refs.strip;
            if (! rail) return;

            const middle = rail.getBoundingClientRect().left + rail.clientWidth / 2;
            let nearest = 0;
            let best = Infinity;

            Array.from(rail.children).forEach((panel, index) => {
                const box = panel.getBoundingClientRect();
                const distance = Math.abs(box.left + box.width / 2 - middle);

                if (distance < best) {
                    best = distance;
                    nearest = index;
                }
            });

            if (nearest !== this.open) this.open = nearest;
        });
    },

    /* ---------- وضعیت ---------- */

    get progress() {
        return this.count < 2 ? 1 : (this.open + 1) / this.count;
    },

    get label() {
        return this.labels[this.open] ?? '';
    },
});
