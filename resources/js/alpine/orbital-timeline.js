/**
 * تایم‌لاین مداری — مراحل روی یک مدار می‌چرخند و با کلیک باز می‌شوند.
 *
 * محتوا در Blade رندر می‌شود، نه اینجا: این کامپوننت فقط *جای* گره‌ها را حساب
 * می‌کند. پس بدون جاوااسکریپت هم هر نُه مرحله در HTML هستند و موتور جستجو و
 * صفحه‌خوان می‌بینندشان.
 *
 * هیچ مقداری پرش نمی‌کند. سه چیز که در نسخه‌ی اول ناگهانی بودند و حرکت را
 * مکانیکی می‌کردند، حالا میرا شده‌اند:
 *
 *   ۱. زاویه پس از کلیک — پیش‌تر کل مدار یک‌باره به جای تازه می‌پرید
 *   ۲. سرعت چرخش هنگام ورود و خروج ماوس — پیش‌تر مثل کلید قطع و وصل می‌شد
 *   ۳. عمق گره‌ها — حالا علاوه بر شفافیت، اندازه هم با فاصله عوض می‌شود
 */

/** کوتاه‌ترین مسیر زاویه‌ای بین دو نقطه: خروجی همیشه بین ۱۸۰- و ۱۸۰ است. */
const shortestTurn = (from, to) => ((to - from + 540) % 360) - 180;

/**
 * میرایی مستقل از نرخ فریم.
 *
 * lerp ساده با ضریب ثابت، روی ۱۲۰ هرتز دو برابر سریع‌تر از ۶۰ هرتز می‌شود.
 * این فرمول می‌گوید «پس از یک ثانیه، این کسر از فاصله باقی بماند» و نتیجه روی
 * هر نمایشگری یکسان است.
 */
const damp = (remainingAfterOneSecond, deltaMs) =>
    1 - Math.pow(remainingAfterOneSecond, deltaMs / 1000);

export default (count = 0) => ({
    count,
    angle: 0,
    target: null,
    speed: 1,
    wantSpeed: 1,
    active: null,
    radius: 200,
    frame: null,
    last: 0,
    settled: false,

    init() {
        this.measure();
        this.onResize = () => this.measure();
        window.addEventListener('resize', this.onResize, { passive: true });

        this.reduced = window.matchMedia('(prefers-reduced-motion: reduce)');

        if (this.reduced.matches) {
            this.speed = 0;
            this.wantSpeed = 0;
        }

        // چرخش با rAF نه setInterval: سرعت به نرخ فریم دستگاه گره نمی‌خورد
        this.last = performance.now();
        this.tick();
    },

    destroy() {
        cancelAnimationFrame(this.frame);
        window.removeEventListener('resize', this.onResize);
    },

    /**
     * شعاع از کوچک‌ترین بُعد کادر می‌آید، نه از عددی ثابت.
     * ۷۸ پیکسل کنار گذاشته می‌شود برای خودِ گره و برچسب زیرش، وگرنه روی
     * نمایشگر باریک، عنوان‌ها از لبه بیرون می‌زنند.
     */
    measure() {
        const box = this.$refs.orbit;
        if (!box) return;

        const smallest = Math.min(box.clientWidth, box.clientHeight);
        this.radius = Math.max(130, Math.min(230, smallest / 2 - 78));
    },

    tick() {
        this.frame = requestAnimationFrame((now) => {
            // سقف delta می‌گذارد تا بعد از برگشت به تب، مدار یک‌باره نپرد
            const delta = Math.min(64, now - this.last);
            this.last = now;

            this.advance(delta);
            this.tick();
        });
    },

    advance(delta) {
        // سرعت به مقدار دلخواه میل می‌کند، نه اینکه ناگهان صفر یا یک شود
        this.speed += (this.wantSpeed - this.speed) * damp(0.004, delta);

        if (this.target !== null) {
            const turn = shortestTurn(this.angle, this.target);

            if (Math.abs(turn) < 0.08) {
                this.angle = this.target;
                this.target = null;
                this.settled = true;

                return;
            }

            this.angle = (this.angle + turn * damp(0.0015, delta) + 360) % 360;

            return;
        }

        if (this.speed > 0.001) {
            this.angle = (this.angle + delta * 0.006 * this.speed) % 360;
        }
    },

    position(index) {
        const degrees = ((index / this.count) * 360 + this.angle) % 360;
        const radians = (degrees * Math.PI) / 180;
        const facing = Math.cos(radians); // ۱ = نزدیک‌ترین، ۱- = دورترین

        return {
            x: this.radius * Math.cos(radians),
            y: this.radius * Math.sin(radians),
            depth: Math.round(100 + 50 * facing),
            // گره‌ی دور هم کوچک‌تر است هم کم‌رنگ‌تر؛ فقط شفافیت، تخت به‌نظر می‌رسید
            scale: 0.88 + 0.17 * ((1 + Math.sin(radians)) / 2),
            fade: Math.max(0.42, Math.min(1, 0.42 + 0.58 * ((1 + Math.sin(radians)) / 2))),
        };
    },

    nodeStyle(index) {
        const { x, y, depth, scale, fade } = this.position(index);
        const open = this.active === index;

        /*
        | وقتی کارتی باز است، بقیه‌ی مدار عقب می‌نشیند.
        |
        | کارت دقیقاً وسط حلقه می‌آید و ناگزیر روی چند برچسب می‌افتد. بدون این
        | عقب‌نشینی، آن هم‌پوشانی مثل تصادف دیده می‌شود؛ با آن، مثل عمق.
        | همسایه‌ها کمتر محو می‌شوند چون هنوز دعوت به کلیک‌اند.
        */
        let dim = 1;

        if (this.active !== null && ! open) {
            dim = this.isNeighbour(index) ? 0.75 : 0.4;
        }

        return `transform: translate(${x.toFixed(1)}px, ${y.toFixed(1)}px) scale(${(open ? 1.08 : scale).toFixed(3)});`
            + `z-index: ${open ? 300 : depth};`
            + `opacity: ${(open ? 1 : fade * dim).toFixed(2)}`;
    },

    toggle(index) {
        if (this.active === index) {
            this.close();

            return;
        }

        this.active = index;
        this.wantSpeed = 0;
        this.settled = false;

        // گره‌ی باز به بالای مدار می‌آید تا کارتش رو به پایین جا شود
        this.target = (270 - (index / this.count) * 360 + 720) % 360;

        if (this.reduced?.matches) {
            this.angle = this.target;
            this.target = null;
            this.settled = true;
        }
    },

    close() {
        this.active = null;
        this.target = null;
        this.settled = false;
        this.wantSpeed = this.reduced?.matches ? 0 : 1;
    },

    /** ماوس روی مدار یعنی کاربر دارد انتخاب می‌کند — حرکت باید نرم بایستد */
    slow() {
        if (this.active === null && ! this.reduced?.matches) {
            this.wantSpeed = 0;
        }
    },

    resume() {
        if (this.active === null && ! this.reduced?.matches) {
            this.wantSpeed = 1;
        }
    },

    /** مرحله‌ی قبل و بعد — خط تولید زنجیره است، نه شبکه */
    isNeighbour(index) {
        return this.active !== null && Math.abs(index - this.active) === 1;
    },
});
