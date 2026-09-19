import { prefersReducedMotion } from '../modules/motion';

/**
 * کاروسل «فشرده».
 *
 * یک ردیف است، نه یک حلقه. ساختارش در هر ده حالت یکی است، و حولِ کارتِ باز
 * قرینه است:
 *
 *     [نوار] [۲] [۱] [کارتِ باز] [۱] [۲] [نوار]
 *                    راست ← چپ
 *
 * قرینه بودن تزئین نیست، شرطِ کار است. پیش‌تر بادبزن فقط یک طرف بود، و نتیجه
 * این می‌شد که باز شدن یک کارت به جهتِ آمدنت بستگی داشت: رو به جلو، کارتِ
 * بعدی از همان همسایه‌ی بزرگ باز می‌شد و یک پله بالا می‌آمد؛ رو به عقب، یک
 * نوارِ هشت‌پیکسلی یک‌باره تمام‌قد می‌شد. حالا کارتی که باز می‌شود، از هر طرف
 * که بیایی، از همان اندازه شروع می‌کند.
 *
 * عرضِ کارتِ باز و هر چهار کارتِ بادبزن در همه‌ی حالت‌ها ثابت است. تنها چیزی
 * که عوض می‌شود پهنای نوارهاست: روی مرحله‌های اول و آخر که یک سمتِ بادبزن
 * خالی می‌ماند، سهمِ استفاده‌نشده بین نوارها پخش می‌شود. آن هم قرینه است —
 * مرحله‌ی دوم و مرحله‌ی یکی‌مانده‌به‌آخر نوارهای هم‌اندازه دارند.
 *
 * هیچ کارتی پنهان نمی‌ماند؛ هر ده‌تا همیشه روی صفحه‌اند.
 */

/**
 * سهم کارتِ بادبزن در فاصله‌ی یک و دو از کارتِ باز — پیش از یکسان‌سازی.
 *
 * دوتا در هر طرف، نه سه‌تا: با سه‌تا، سهمِ آزادشده در دو سر ردیف آن‌قدر زیاد
 * می‌شود که نوارها از باریک‌ترین کارتِ بادبزن پهن‌تر شوند و پلکان بشکند.
 */
const FAN = [0.61, 0.3];

/** کارتی که نشانگر رویش است، این‌قدر جا می‌گیرد — به هزینه‌ی بقیه‌ی بادبزن. */
const GROW = 1.18;

/* هندسه بر حسب پیکسل. */
const MIN_SLAT = 8;
const GAP = 12;

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

    /**
     * سهم هر کارتِ بادبزنِ این حالت، کلیددار با فاصله‌ی علامت‌دار از کارتِ باز:
     * مثبت یعنی جلوتر (سمت چپ)، منفی یعنی عقب‌تر (سمت راست).
     *
     * یکسان‌سازی روی «بادبزنِ کامل» انجام می‌شود، یعنی دو کارت در هر طرف. پس
     * وقتی هر دو طرف پر است جمعِ سهم‌ها دقیقاً ۱ است و سهمِ هر کارت — در هر
     * حالتی که وجود داشته باشد — همان یک عدد می‌ماند.
     */
    get spread() {
        const unit = 2 * FAN.reduce((sum, share) => sum + share, 0);
        const ahead = Math.min(FAN.length, this.count - 1 - this.open);
        const behind = Math.min(FAN.length, this.open);

        const share = new Map();
        for (let d = 1; d <= ahead; d++) share.set(d, FAN[d - 1] / unit);
        for (let d = 1; d <= behind; d++) share.set(-d, FAN[d - 1] / unit);

        // نشانگر یک کارت را پهن می‌کند و همان اندازه را از بقیه می‌گیرد، پس
        // جمع دست‌نخورده می‌ماند و نوارها تکان نمی‌خورند.
        const lit = this.reduced || this.hover < 0 ? NaN : this.hover - this.open;

        if (share.has(lit) && share.size > 1) {
            const gain = share.get(lit) * (GROW - 1);
            const rest = [...share].reduce((sum, [d, v]) => (d === lit ? sum : sum + v), 0);

            share.forEach((value, d) => {
                share.set(d, d === lit ? value + gain : value - (gain * value) / rest);
            });
        }

        return share;
    },

    /** هرچه بادبزن نیست، نوار است — چه رد شده باشد چه نرسیده. هیچ‌کدام پنهان نیستند. */
    get slats() {
        return this.count - 1 - this.spread.size;
    },

    get ms() {
        if (this.reduced) return 0;
        return this.sliding ? SLIDE_MS : HOVER_MS;
    },

    /** چه کسری از فضای مرجع را بادبزنِ این حالت برمی‌دارد. */
    get taken() {
        return [...this.spread.values()].reduce((sum, share) => sum + share, 0);
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
        const base = Math.max(0, this.count - 1 - 2 * FAN.length) * MIN_SLAT;

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
        const share = this.spread.get(place);
        let width = 'var(--sq-slat)';

        if (place === 0) {
            width = 'var(--sq-h)';
        } else if (share !== undefined) {
            width = `calc(var(--sq-room) * ${share.toFixed(4)})`;
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
