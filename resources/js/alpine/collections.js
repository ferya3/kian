/** فیلتر دسته‌بندی پروژه‌ها / دانلودها — سمت کلاینت، بدون رفت‌وبرگشت به سرور. */
export const filterable = (initial = 'all') => ({
    filter: initial,

    matches(value) {
        return this.filter === 'all' || this.filter === value;
    },

    setFilter(value) {
        this.filter = value;

        const url = new URL(window.location);
        value === 'all' ? url.searchParams.delete('type') : url.searchParams.set('type', value);
        window.history.replaceState({}, '', url);
    },

    get count() {
        return this.$el.querySelectorAll('[data-item]:not([hidden])').length;
    },
});

/** آکاردئون قابل دسترس — یک آیتم باز در هر لحظه. */
export const accordion = (open = null) => ({
    open,

    toggle(key) {
        this.open = this.open === key ? null : key;
    },

    isOpen(key) {
        return this.open === key;
    },
});

/** تب‌های صفحه محصول — با پیمایش کیبورد مطابق الگوی WAI-ARIA. */
export const tabs = (initial) => ({
    active: initial,

    select(key) {
        this.active = key;
    },

    onKey(event, keys) {
        const index = keys.indexOf(this.active);
        if (index === -1) return;

        let next = null;
        // در RTL جهت فلش‌ها برعکس است
        if (event.key === 'ArrowLeft') next = (index + 1) % keys.length;
        if (event.key === 'ArrowRight') next = (index - 1 + keys.length) % keys.length;
        if (event.key === 'Home') next = 0;
        if (event.key === 'End') next = keys.length - 1;

        if (next === null) return;

        event.preventDefault();
        this.active = keys[next];
        this.$refs[`tab-${keys[next]}`]?.focus();
    },
});
