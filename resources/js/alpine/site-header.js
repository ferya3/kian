/**
 * هدر دسکتاپ: مِگا منو، حالت اسکرول‌شده و جستجوی سراسری.
 * منو با کیبورد هم کار می‌کند: Escape می‌بندد و فوکوس به دکمه برمی‌گردد.
 */
export default (overHero = false) => ({
    overHero,
    openMenu: null,
    scrolled: false,
    hidden: false,
    searchOpen: false,
    lastScroll: 0,
    closeTimer: null,

    /** هدر روی قهرمان تیره: متن روشن. به‌محض اسکرول یا باز شدن منو، به حالت روشن برمی‌گردد. */
    get onDark() {
        return this.overHero && !this.scrolled && !this.openMenu;
    },

    init() {
        this.onScroll();
        window.addEventListener('scroll', () => this.onScroll(), { passive: true });
    },

    onScroll() {
        const y = window.scrollY;
        this.scrolled = y > 24;

        // هنگام اسکرول به پایین هدر جمع می‌شود تا فضای دید بیشتری بماند
        this.hidden = y > 400 && y > this.lastScroll && !this.openMenu && !this.searchOpen;
        this.lastScroll = y;
    },

    toggle(key) {
        this.openMenu = this.openMenu === key ? null : key;
    },

    open(key) {
        clearTimeout(this.closeTimer);
        this.openMenu = key;
    },

    /** تأخیر کوتاه هنگام خروج ماوس تا حرکت مورب به سمت منو، آن را نبندد. */
    scheduleClose() {
        clearTimeout(this.closeTimer);
        this.closeTimer = setTimeout(() => (this.openMenu = null), 180);
    },

    close() {
        clearTimeout(this.closeTimer);
        this.openMenu = null;
    },

    openSearch() {
        this.searchOpen = true;
        this.close();
        this.$nextTick(() => this.$refs.searchInput?.focus());
    },
});
