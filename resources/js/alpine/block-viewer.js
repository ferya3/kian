import { prefersReducedMotion } from '../modules/motion';

/**
 * نمایشگر بلوک.
 *
 * دو حالت دارد:
 *  - «حجم»: مکعب CSS 3D که با حرکت ماوس یا drag می‌چرخد.
 *    عمداً از WebGL استفاده نشده: یک مکعب شش‌وجهی به Three.js نیاز ندارد
 *    و بار GPU و حجم باندل را بی‌دلیل بالا می‌برد.
 *  - «مقطع»: نمای برش داخلی با نقاط تعاملی روی حفره‌ها.
 */
export default (cavities = []) => ({
    mode: 'solid',
    rotateX: -14,
    rotateY: -24,
    dragging: false,
    startX: 0,
    startY: 0,
    activeCavity: null,
    cavities,

    init() {
        if (prefersReducedMotion()) return;

        // چرخش ملایم خودکار تا وقتی کاربر تعامل نکرده
        this.idle = setInterval(() => {
            if (this.dragging || this.mode !== 'solid') return;
            this.rotateY = (this.rotateY - 0.25) % 360;
        }, 40);

        this.$watch('mode', () => (this.activeCavity = null));
    },

    destroy() {
        clearInterval(this.idle);
    },

    pointerDown(event) {
        if (this.mode !== 'solid') return;
        this.dragging = true;
        this.startX = event.clientX;
        this.startY = event.clientY;
        event.currentTarget.setPointerCapture?.(event.pointerId);
    },

    pointerMove(event) {
        if (!this.dragging) return;
        this.rotateY += (event.clientX - this.startX) * 0.45;
        this.rotateX = Math.max(-60, Math.min(60, this.rotateX - (event.clientY - this.startY) * 0.35));
        this.startX = event.clientX;
        this.startY = event.clientY;
    },

    pointerUp() {
        this.dragging = false;
    },

    /** چرخش با کیبورد — بدون آن، این کنترل برای کاربر کیبورد بی‌فایده است. */
    nudge(axis, amount) {
        if (axis === 'y') this.rotateY += amount;
        else this.rotateX = Math.max(-60, Math.min(60, this.rotateX + amount));
    },

    get transform() {
        return `rotateX(${this.rotateX}deg) rotateY(${this.rotateY}deg)`;
    },

    selectCavity(index) {
        this.activeCavity = this.activeCavity === index ? null : index;
    },

    get cavity() {
        return this.activeCavity === null ? null : this.cavities[this.activeCavity];
    },
});
