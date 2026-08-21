import { startScroll, stopScroll } from '../modules/smooth-scroll';

/**
 * منوی موبایل — کشوی تمام‌صفحه با قفل اسکرول و تله‌ی فوکوس ساده.
 * موبایل نسخه‌ی کوچک‌شده‌ی دسکتاپ نیست: اینجا فهرست عمودی آکاردئونی است.
 */
export default () => ({
    open: false,
    expanded: null,

    toggle() {
        this.open ? this.hide() : this.show();
    },

    show() {
        this.open = true;
        document.body.style.overflow = 'hidden';
        stopScroll();
        this.$nextTick(() => this.$refs.panel?.querySelector('a, button')?.focus());
    },

    hide() {
        this.open = false;
        this.expanded = null;
        document.body.style.overflow = '';
        startScroll();
        this.$refs.trigger?.focus();
    },

    section(key) {
        this.expanded = this.expanded === key ? null : key;
    },
});
