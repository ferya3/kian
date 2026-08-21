/**
 * ممیزی استانداردهای موبایل.
 *
 *   npm run audit:mobile              # روی http://127.0.0.1:8000
 *   BASE=http://localhost:8131 npm run audit:mobile
 *
 * چهار قاعده‌ای که روی موبایل بیشترین آسیب را می‌زنند بررسی می‌شود:
 *
 *  ۱. زوم خودکار iOS — هر کنترل فرم با font-size کمتر از ۱۶px باعث می‌شود
 *     سافاری هنگام فوکوس کل صفحه را زوم کند و کاربر جهت را گم کند.
 *  ۲. اندازه‌ی هدف لمسی — WCAG 2.5.8 حداقل ۲۴×۲۴ و راهنمای اپل ۴۴×۴۴.
 *  ۳. خوانایی — هیچ متنی روی موبایل زیر ۱۲px نباشد.
 *  ۴. سرریز افقی و touch-action که اسکرول عمودی را می‌بلعد.
 *
 * لینک‌های کشیده‌شده روی کارت (::after با inset صفر) و لینک‌های درون متن جاری
 * استثنا هستند: ناحیه‌ی لمسی واقعی‌شان بزرگ‌تر از کادر خودشان است.
 */
import { chromium } from 'playwright';

const BASE = process.env.BASE || 'http://127.0.0.1:8000';
const VIEWPORTS = [
    { name: 'Android کوچک', width: 360, height: 740 },
    { name: 'iPhone', width: 390, height: 844 },
    { name: 'iPhone Max', width: 430, height: 932 },
];

const PATHS = [
    '/', '/products', '/products/ceramic-block-20', '/product-finder', '/projects',
    '/projects/niavaran-residential-complex', '/solutions', '/solutions/exterior-envelope',
    '/technology', '/factory', '/sustainability', '/technical', '/technical/downloads',
    '/technical/installation', '/technical/certificates', '/technical/faq', '/articles',
    '/about', '/distributors', '/contact', '/search',
];

const MIN_FIELD_FONT = 16;   // زیر این مقدار، iOS زوم می‌کند
const MIN_TAP = 24;          // WCAG 2.5.8 سطح AA
const MIN_TEXT = 12;

function collect() {
    const W = window.innerWidth;
    const out = { zoom: [], tap: [], tiny: [], touchNone: [], overflow: null };

    document.querySelectorAll('input, select, textarea').forEach((el) => {
        const fs = parseFloat(getComputedStyle(el).fontSize);
        if (fs < 16) out.zoom.push(`${el.tagName.toLowerCase()}#${el.id || el.name || '?'} = ${fs}px`);
    });

    const interactive = 'a[href], button, input:not([type=hidden]), select, textarea, [role="button"], [role="tab"], summary';
    document.querySelectorAll(interactive).forEach((el) => {
        const b = el.getBoundingClientRect();
        if (b.width === 0 || b.height === 0) return;

        const st = getComputedStyle(el);
        if (st.visibility === 'hidden' || st.pointerEvents === 'none') return;

        // لینک کشیده‌شده روی کل کارت: کارت هدف لمسی است، نه متن
        const after = getComputedStyle(el, '::after');
        if (after.position === 'absolute' && after.inset === '0px') return;

        // لینک درون متن جاری — استثنای صریح WCAG 2.5.8
        if (el.tagName === 'A' && st.display === 'inline') return;

        // لینک پرش: تا وقتی فوکوس نگرفته پنهان است؛ اندازه‌ی حالت فوکوس ملاک است
        if (el.classList.contains('sr-only-focusable')) return;

        // ورودی رادیو/چک‌باکس: برچسبِ در بر گیرنده هدف لمسی واقعی است
        if ((el.type === 'radio' || el.type === 'checkbox') && el.closest('label')) {
            const lb = el.closest('label').getBoundingClientRect();
            if (Math.min(lb.width, lb.height) >= 24) return;
        }

        if (Math.min(b.width, b.height) < 24) {
            out.tap.push(`${el.tagName.toLowerCase()}.${String(el.className).split(' ')[0]} ${Math.round(b.width)}×${Math.round(b.height)}`);
        }
    });

    document.querySelectorAll('body *').forEach((el) => {
        const hasText = [...el.childNodes].some((n) => n.nodeType === 3 && n.textContent.trim().length > 1);
        if (hasText && parseFloat(getComputedStyle(el).fontSize) < 12) {
            out.tiny.push(`${el.tagName.toLowerCase()} "${el.textContent.trim().slice(0, 24)}"`);
        }

        if (getComputedStyle(el).touchAction === 'none') {
            const b = el.getBoundingClientRect();
            if (b.width > 60 && b.height > 60) {
                out.touchNone.push(`${el.tagName.toLowerCase()}.${String(el.className).split(' ')[0]}`);
            }
        }
    });

    if (document.documentElement.scrollWidth > W + 1) {
        out.overflow = `${document.documentElement.scrollWidth} > ${W}`;
    }

    return out;
}

const browser = await chromium.launch({
    executablePath: process.env.CHROMIUM_PATH || undefined,
});

let failures = 0;

for (const vp of VIEWPORTS) {
    const page = await browser.newPage({
        viewport: { width: vp.width, height: vp.height },
        isMobile: true,
        hasTouch: true,
    });

    const found = { zoom: new Set(), tap: new Set(), tiny: new Set(), touchNone: new Set(), overflow: new Set() };

    for (const path of PATHS) {
        await page.goto(BASE + path, { waitUntil: 'domcontentloaded' });
        await page.waitForTimeout(300);

        const r = await page.evaluate(collect);
        r.zoom.forEach((x) => found.zoom.add(`${path} ${x}`));
        r.tap.forEach((x) => found.tap.add(`${path} ${x}`));
        r.tiny.forEach((x) => found.tiny.add(`${path} ${x}`));
        r.touchNone.forEach((x) => found.touchNone.add(`${path} ${x}`));
        if (r.overflow) found.overflow.add(`${path} ${r.overflow}`);
    }

    await page.close();

    const rules = [
        [`کنترل فرم زیر ${MIN_FIELD_FONT}px (زوم iOS)`, found.zoom],
        [`هدف لمسی زیر ${MIN_TAP}px`, found.tap],
        [`متن زیر ${MIN_TEXT}px`, found.tiny],
        ['touch-action: none روی ناحیه‌ی بزرگ', found.touchNone],
        ['سرریز افقی', found.overflow],
    ];

    const broken = rules.filter(([, set]) => set.size > 0);
    console.log(`\n${vp.name} (${vp.width}×${vp.height})`);

    if (broken.length === 0) {
        console.log('  ✓ همه‌ی قواعد رعایت شده');
        continue;
    }

    for (const [label, set] of broken) {
        failures += set.size;
        console.log(`  ✗ ${label}: ${set.size}`);
        [...set].slice(0, 5).forEach((x) => console.log(`      ${x}`));
        if (set.size > 5) console.log(`      … و ${set.size - 5} مورد دیگر`);
    }
}

await browser.close();

console.log(failures === 0 ? '\nممیزی موبایل: قبول\n' : `\nممیزی موبایل: ${failures} ایراد\n`);
process.exit(failures === 0 ? 0 : 1);
