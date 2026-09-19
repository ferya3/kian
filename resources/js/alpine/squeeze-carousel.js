import { prefersReducedMotion } from '../modules/motion';

/**
 * کاروسل «فشرده».
 *
 * یک ردیف است، نه یک حلقه: پانلِ باز بیشترین جا را می‌گیرد، سه پانل بعدی
 * به‌ترتیب باریک‌تر می‌شوند و باقی به تیغه‌های نازک ته صف تبدیل می‌شوند.
 * مرحله‌ای که رد می‌شود هم به تیغه فشرده می‌شود و سر صف می‌ماند؛ نوار جابه‌جا
 * نمی‌شود، فقط عرض‌ها عوض می‌شوند.
 *
 * چهار ستون سهمی از «فضای باقی‌مانده» می‌گیرند. سهم‌ها همیشه ۱ جمع می‌شوند،
 * پس عرض کل ردیف دقیقاً برابر عرض کادر است و هیچ اندازه‌گیری‌ای در JS لازم
 * نیست — عرض‌ها calc در CSS هستند. سهم ستون صفر منفی است چون پانل باز از یک
 * بلوک ۱۶:۹ شروع می‌کند و کمی از آن پس می‌دهد.
 */
const SHARES = [-0.06, 0.61, 0.3, 0.15];

/** ستونی که نشانگر رویش است، جا می‌گیرد. */
const STRETCHED = [0, 0.71, 0.4, 0.25];

/** همسایه‌ها کمی عقب می‌کشند تا هزینه‌اش را بدهند. */
const SQUEEZED = [-0.12, 0.59, 0.28, 0.13];

/* هندسه بر حسب پیکسل — همان مقادیر پیش‌فرض کامپوننت اصلی. */
const SLAT = 8;
const SLAT_GAP = 8;
const GAP = 16;
const MAX_SLATS = 3;

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

    /** ستون‌های بعد از پانلِ باز — نزدیک انتهای صف کمتر از سه‌تا می‌مانند. */
    get cols() {
        return Math.max(0, Math.min(3, this.count - this.open - 1));
    },

    /** تیغه‌های ته صف — مراحلی که هنوز نرسیده‌اند. بیش از سه‌تا نشان داده نمی‌شود. */
    get slats() {
        return Math.max(0, Math.min(MAX_SLATS, this.count - this.open - 4));
    },

    /**
     * تیغه‌های سر صف — مراحلی که رد شده‌اند.
     *
     * برخلاف ته صف سقف ندارد: هر مرحله‌ای که پشت سر گذاشته می‌شود، به تیغه
     * تبدیل شده و همان‌جا کنار لبه می‌ماند. پیش‌تر از کادر بیرون می‌رفت و
     * ناپدید می‌شد — که هم راه برگشت را می‌بست، هم مسیر طی‌شده را پاک می‌کرد.
     */
    get behind() {
        return this.open;
    },

    get ms() {
        if (this.reduced) return 0;
        return this.sliding ? SLIDE_MS : HOVER_MS;
    },

    /**
     * سهم هر ستون از فضای باقی‌مانده.
     *
     * نزدیک انتهای صف که ستون کم می‌آید، سهم‌های باقی‌مانده بزرگ‌نمایی می‌شوند
     * تا جمعشان باز هم ۱ شود — یعنی ردیف هیچ‌وقت کوتاه‌تر از کادر نمی‌ماند و
     * آخرین پانل تمام عرض را می‌گیرد.
     */
    get shares() {
        const k = this.cols;
        if (k === 0) return [1];

        const col = this.hover - this.open;
        const lit = ! this.reduced && this.hover >= 0 && col >= 0 && col <= k ? col : -1;
        const base = lit === -1
            ? SHARES
            : SHARES.map((_, col) => (col === lit ? STRETCHED[col] : SQUEEZED[col]));

        const head = base[0];
        const tail = base.slice(1, k + 1);
        const scale = (1 - head) / tail.reduce((sum, share) => sum + share, 0);

        return [head, ...tail.map((share) => share * scale)];
    },

    /** آخرین پانلی که هنوز دیده می‌شود؛ بعد از آن عرض صفر است. */
    get last() {
        return this.cols + this.slats;
    },

    /**
     * فضای باقی‌مانده برای چهار ستون.
     *
     * هرچه ثابت است اول کنار گذاشته می‌شود: بلوک ۱۶:۹ پانلِ باز، تیغه‌های دو
     * سر با فاصله‌شان، و فاصله‌ی بین ستون‌ها. چون جمع سهم‌ها همیشه ۱ است،
     * عرض کل ردیف دقیقاً برابر عرض کادر درمی‌آید و نوار هیچ‌وقت جابه‌جا
     * نمی‌شود — فقط عرض‌ها عوض می‌شوند.
     */
    get room() {
        const b = this.behind;
        const fixed = b * SLAT
            + (b > 0 ? (b - 1) * SLAT_GAP + GAP : 0)
            + this.cols * GAP
            + this.slats * (SLAT + SLAT_GAP);

        return `calc(100cqi - var(--sq-hero) - ${fixed}px)`;
    },

    get stripStyle() {
        return { '--sq-ms': `${this.ms}ms`, '--sq-room': this.room };
    },

    panelStyle(index) {
        if (! this.desktop) {
            return { width: '', marginInlineStart: '', borderRadius: '', opacity: '' };
        }

        const col = index - this.open;
        let width = `${SLAT}px`;
        // تیغه‌ها تنگِ هم می‌نشینند تا مثل یک شانه دیده شوند، نه کارت‌های جدا.
        let margin = col > 0 && col <= this.cols ? `${GAP}px` : `${SLAT_GAP}px`;

        if (col > this.last) {
            width = '0px';
            margin = '0px';
        } else if (col === 0) {
            width = `calc(var(--sq-hero) + var(--sq-room) * ${this.shares[0]})`;
            margin = `${GAP}px`;
        } else if (col > 0 && col <= this.cols) {
            width = `calc(var(--sq-room) * ${this.shares[col]})`;
        }

        if (index === 0) margin = '0px';

        return {
            width,
            marginInlineStart: margin,
            // تیغه‌ی ۸ پیکسلی با شعاع ۲۰ پیکسل به قرص تبدیل می‌شود، نه نوار.
            borderRadius: `min(var(--radius-panel), calc(${width} / 2))`,
            opacity: col > this.last ? '0' : '1',
            transitionProperty: 'width, margin-inline-start, opacity',
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
