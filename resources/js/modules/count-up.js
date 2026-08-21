import { observeOnce, prefersReducedMotion } from './motion';

const FA_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

function toPersian(value) {
    return String(value).replace(/\d/g, (d) => FA_DIGITS[Number(d)]);
}

function format(value, decimals, separated) {
    const fixed = Number(value).toFixed(decimals);

    if (!separated) return toPersian(fixed);

    const [int, frac] = fixed.split('.');
    const grouped = int.replace(/\B(?=(\d{3})+(?!\d))/g, '٬');

    return toPersian(frac ? `${grouped}.${frac}` : grouped);
}

/**
 * شمارش صعودی اعداد بزرگ هنگام ورود به viewport.
 * <span data-countup="120000" data-decimals="0" data-separated>
 */
export function initCountUp(root = document) {
    const targets = root.querySelectorAll('[data-countup]');
    if (!targets.length) return;

    targets.forEach((el) => {
        const target = Number(el.dataset.countup);
        const decimals = Number(el.dataset.decimals ?? 0);
        const separated = el.hasAttribute('data-separated');
        el.textContent = prefersReducedMotion() ? format(target, decimals, separated) : format(0, decimals, separated);
    });

    if (prefersReducedMotion()) return;

    observeOnce(targets, (el) => {
        const target = Number(el.dataset.countup);
        const decimals = Number(el.dataset.decimals ?? 0);
        const separated = el.hasAttribute('data-separated');
        const duration = Number(el.dataset.duration ?? 1600);
        const start = performance.now();

        const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            // easeOutExpo — سریع شروع می‌شود و نرم می‌ایستد
            const eased = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);

            el.textContent = format(target * eased, decimals, separated);

            if (progress < 1) requestAnimationFrame(tick);
        };

        requestAnimationFrame(tick);
    });
}
