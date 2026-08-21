/**
 * موتور انتخاب محصول.
 *
 * فرم بدون جاوااسکریپت هم کار می‌کند (submit عادی به صفحه‌ی نتایج).
 * با جاوااسکریپت، نتیجه بدون بارگذاری مجدد صفحه در همان جا ظاهر می‌شود.
 */
export default (endpoint) => ({
    endpoint,
    loading: false,
    results: '',
    error: '',
    criteria: {
        project_type: '',
        wall_type: '',
        thickness: '',
        insulation: '',
    },

    get answered() {
        return Object.values(this.criteria).filter(Boolean).length;
    },

    get ready() {
        return this.answered >= 2;
    },

    reset() {
        this.criteria = { project_type: '', wall_type: '', thickness: '', insulation: '' };
        this.results = '';
        this.error = '';
    },

    async submit() {
        if (!this.ready || this.loading) return;

        this.loading = true;
        this.error = '';

        const params = new URLSearchParams({ partial: '1' });
        Object.entries(this.criteria).forEach(([key, value]) => value && params.append(key, value));

        try {
            const response = await fetch(`${this.endpoint}?${params}`, {
                headers: { 'X-Partial': '1', 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) throw new Error(response.statusText);

            this.results = await response.text();

            this.$nextTick(() => {
                this.$refs.results?.setAttribute('tabindex', '-1');
                this.$refs.results?.focus({ preventScroll: true });
                this.$refs.results?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
        } catch (e) {
            // اگر درخواست شکست خورد، کاربر به صفحه‌ی کامل نتایج هدایت می‌شود.
            this.error = 'ارتباط برقرار نشد. در حال انتقال به صفحه نتایج…';
            this.$refs.form?.submit();
        } finally {
            this.loading = false;
        }
    },
});
