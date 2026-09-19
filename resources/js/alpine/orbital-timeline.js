/**
 * تایم‌لاین مداری — مراحل روی یک مدار می‌چرخند و با کلیک باز می‌شوند.
 *
 * محتوا در Blade رندر می‌شود، نه اینجا: این کامپوننت فقط *جای* گره‌ها را حساب
 * می‌کند. پس بدون جاوااسکریپت هم هر نُه مرحله در HTML هستند و موتور جستجو و
 * صفحه‌خوان می‌بینندشان.
 */
export default (count = 0) => ({
    count,
    angle: 0,
    active: null,
    radius: 200,
    auto: true,
    frame: null,
    last: 0,

    init() {
        this.measure();
        this.onResize = () => this.measure();
        window.addEventListener('resize', this.onResize, { passive: true });

        // چرخش با rAF نه setInterval: سرعت به نرخ فریم دستگاه گره نمی‌خورد
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            this.last = performance.now();
            this.tick();
        }
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
            const delta = Math.min(64, now - this.last);
            this.last = now;

            // ~۶ درجه در ثانیه؛ سقف delta می‌گذارد تا بعد از برگشت به تب، نپرد
            if (this.auto) {
                this.angle = (this.angle + delta * 0.006) % 360;
            }

            this.tick();
        });
    },

    position(index) {
        const degrees = ((index / this.count) * 360 + this.angle) % 360;
        const radians = (degrees * Math.PI) / 180;

        return {
            x: this.radius * Math.cos(radians),
            y: this.radius * Math.sin(radians),
            // گره‌های نزدیک‌تر به بیننده جلوتر و روشن‌تر می‌نشینند
            depth: Math.round(100 + 50 * Math.cos(radians)),
            fade: Math.max(0.45, Math.min(1, 0.45 + 0.55 * ((1 + Math.sin(radians)) / 2))),
        };
    },

    nodeStyle(index) {
        const { x, y, depth, fade } = this.position(index);
        const open = this.active === index;

        return `transform: translate(${x.toFixed(1)}px, ${y.toFixed(1)}px);`
            + `z-index: ${open ? 300 : depth};`
            + `opacity: ${open ? 1 : fade.toFixed(2)}`;
    },

    toggle(index) {
        if (this.active === index) {
            this.close();

            return;
        }

        this.active = index;
        this.auto = false;

        // گره‌ی باز به بالای مدار می‌آید تا کارتش رو به پایین جا شود
        this.angle = (270 - (index / this.count) * 360 + 720) % 360;
    },

    close() {
        this.active = null;
        this.auto = true;
    },

    /** مرحله‌ی قبل و بعد — خط تولید زنجیره است، نه شبکه */
    isNeighbour(index) {
        return this.active !== null && Math.abs(index - this.active) === 1;
    },
});
