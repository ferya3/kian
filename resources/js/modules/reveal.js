import { observeOnce, prefersReducedMotion } from './motion';

/**
 * ظاهر شدن تدریجی عناصر هنگام اسکرول.
 * تأخیر پلکانی از data-reveal-stagger روی والد خوانده می‌شود.
 */
export function initReveal(root = document) {
    const targets = root.querySelectorAll('[data-reveal], [data-clip-reveal]');

    if (prefersReducedMotion()) {
        targets.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    root.querySelectorAll('[data-reveal-stagger]').forEach((group) => {
        const step = Number(group.dataset.revealStagger) || 90;
        group.querySelectorAll(':scope > [data-reveal], :scope > * > [data-reveal]').forEach((child, i) => {
            child.style.setProperty('--reveal-delay', `${Math.min(i, 8) * step}ms`);
        });
    });

    observeOnce(targets, (el) => el.classList.add('is-visible'));
}
