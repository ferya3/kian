import { prefersReducedMotion } from './motion';

/**
 * تایم‌لاین «از خاک تا سازه».
 *
 * دسکتاپ: صفحه pin می‌شود و ۹ مرحله به‌صورت افقی حرکت می‌کنند —
 * حسِ حرکت دوربین در طول خط تولید.
 * موبایل و حالت reduced-motion: همان محتوا به‌شکل ریل قابل swipe، بدون pin.
 *
 * نکته‌ی RTL: در چیدمان راست‌به‌چپ، کارت‌ها از لبه‌ی راست به سمت چپ ادامه
 * پیدا می‌کنند و بیرون از کادر قرار می‌گیرند. برای دیدنشان باید ریل را به
 * راست (x مثبت) جابه‌جا کرد — برعکس چیزی که در LTR لازم است.
 */
export async function initProcessScroll() {
    const section = document.querySelector('[data-process-scroll]');
    if (!section) return;

    const track = section.querySelector('[data-process-track]');
    const steps = Array.from(section.querySelectorAll('[data-process-step]'));
    const progressBar = section.querySelector('[data-process-progress]');
    const counter = section.querySelector('[data-process-counter]');
    const parallax = section.querySelector('[data-process-parallax]');

    if (!track || steps.length === 0) return;

    if (window.innerWidth < 1024 || prefersReducedMotion()) {
        section.dataset.mode = 'rail';
        return;
    }

    const { gsap } = await import('gsap');
    const { ScrollTrigger } = await import('gsap/ScrollTrigger');
    gsap.registerPlugin(ScrollTrigger);

    section.dataset.mode = 'pinned';

    // فاصله‌ی پیمایش از روی موقعیت واقعی اولین و آخرین کارت اندازه‌گیری می‌شود،
    // نه از scrollWidth — چون ریل overflow:visible است و scrollWidth در RTL
    // قابل اتکا نیست.
    let distance = 0;

    const measure = () => {
        gsap.set(track, { x: 0 });
        const first = steps[0].getBoundingClientRect();
        const last = steps[steps.length - 1].getBoundingClientRect();
        const content = first.right - last.left;
        distance = Math.max(0, content - section.offsetWidth + 64);
    };

    measure();
    ScrollTrigger.addEventListener('refreshInit', measure);

    const persian = (value) => String(value).replace(/\d/g, (d) => '۰۱۲۳۴۵۶۷۸۹'[Number(d)]);

    /** کارت نزدیک به مرکز صفحه کاملاً روشن است؛ بقیه محو می‌شوند. */
    const focusNearest = () => {
        const center = window.innerWidth / 2;

        steps.forEach((step) => {
            const body = step.querySelector('[data-process-body]');
            if (!body) return;

            const rect = step.getBoundingClientRect();
            const offset = Math.abs(rect.left + rect.width / 2 - center);
            const t = Math.min(1, offset / (window.innerWidth * 0.55));

            body.style.opacity = String(1 - t * 0.65);
            body.style.transform = `translateY(${t * 14}px)`;
        });
    };

    gsap.to(track, {
        x: () => distance,
        ease: 'none',
        scrollTrigger: {
            trigger: section,
            start: 'top top',
            end: () => `+=${distance}`,
            pin: true,
            scrub: 0.7,
            invalidateOnRefresh: true,
            anticipatePin: 1,
            onUpdate: (self) => {
                if (progressBar) progressBar.style.transform = `scaleX(${self.progress})`;

                if (counter) {
                    const index = Math.min(steps.length - 1, Math.floor(self.progress * (steps.length - 1)) + 1);
                    counter.textContent = persian(String(index).padStart(2, '0'));
                }

                focusNearest();
            },
        },
    });

    // پس‌زمینه کندتر حرکت می‌کند: عمق میدان
    if (parallax) {
        gsap.to(parallax, {
            x: () => distance * 0.3,
            ease: 'none',
            scrollTrigger: {
                trigger: section,
                start: 'top top',
                end: () => `+=${distance}`,
                scrub: 0.7,
                invalidateOnRefresh: true,
            },
        });
    }

    focusNearest();
    ScrollTrigger.refresh();
}
