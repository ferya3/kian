/**
 * ممیزی استانداردهای موبایل.
 *
 *   npm run audit:mobile              # روی http://127.0.0.1:8000
 *   BASE=http://localhost:8131 npm run audit:mobile
 *
 * قاعده‌هایی که روی موبایل بیشترین آسیب را می‌زنند بررسی می‌شود:
 *
 *  ۱. زوم خودکار iOS — هر کنترل فرم با font-size کمتر از ۱۶px باعث می‌شود
 *     سافاری هنگام فوکوس کل صفحه را زوم کند و کاربر جهت را گم کند.
 *  ۲. اندازه‌ی هدف لمسی — ۴۴×۴۴، راهنمای اپل و WCAG 2.5.5.
 *  ۳. فاصله‌ی هدف‌های لمسی — دست‌کم ۸ پیکسل میان دو هدفِ همسایه.
 *  ۴. خوانایی — هیچ متنی روی موبایل زیر ۱۲px نباشد.
 *  ۵. سرریز افقی و touch-action که اسکرول عمودی را می‌بلعد.
 *
 * لینک‌های کشیده‌شده روی کارت (::after با inset صفر) و لینک‌های درون متن جاری
 * استثنا هستند: ناحیه‌ی لمسی واقعی‌شان بزرگ‌تر از کادر خودشان است.
 *
 * دو نکته که بدون آن‌ها ممیزی نتیجه‌ی دروغ می‌دهد:
 *
 *   — پیش از اندازه‌گیری، transition و animation خاموش می‌شوند. وگرنه کارتی
 *     که وسطِ حرکت است با اندازه‌ی لحظه‌ایِ خودش سنجیده می‌شود و ممیزی هر بار
 *     نتیجه‌ی دیگری می‌دهد.
 *   — pointer-events: none روی والد، مقدار محاسبه‌شده‌ی فرزند را عوض نمی‌کند.
 *     پس زنجیره‌ی والدها بررسی می‌شود؛ دکمه‌ای در کارتِ پس‌زمینه‌ی کاروسل
 *     اصلاً هدف لمسی نیست، هر اندازه‌ای که داشته باشد.
 */
import { existsSync } from 'node:fs';
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

/*
 * پنل مدیریت پشت ورود است. اگر ADMIN_EMAIL و ADMIN_PASSWORD داده شوند، ممیزی
 * یک‌بار وارد می‌شود و این مسیرها را هم بررسی می‌کند.
 */
const ADMIN_PATHS = [
    '/admin', '/admin/products', '/admin/products/1/edit', '/admin/products/create',
    '/admin/projects', '/admin/documents', '/admin/messages', '/admin/settings',
    '/admin/users', '/admin/activity', '/admin/media', '/admin/certificates',
];

const ADMIN_EMAIL = process.env.ADMIN_EMAIL || '';
const ADMIN_PASSWORD = process.env.ADMIN_PASSWORD || '';

const MIN_FIELD_FONT = 16;   // زیر این مقدار، iOS زوم می‌کند
const MIN_TAP = 44;          // راهنمای اپل و WCAG 2.5.5
const MIN_GAP = 8;           // فاصله‌ی دو هدف همسایه
const MIN_TEXT = 12;

/** حرکت را خاموش می‌کند تا اندازه‌ها قطعی باشند، نه لحظه‌ای. */
const FREEZE = '*,*::before,*::after{transition:none!important;animation:none!important}';

function collect({ MIN_TAP, MIN_GAP }) {
    const W = window.innerWidth;
    const out = { zoom: [], tap: [], gap: [], tiny: [], touchNone: [], overflow: null };

    const name = (el) => `${el.tagName.toLowerCase()}.${String(el.className).trim().split(/\s+/)[0]}`;

    /** والدی که pointer-events آن none است، فرزند را هم از دسترس لمس بیرون می‌برد. */
    const untouchable = (el) => {
        for (let n = el; n; n = n.parentElement) {
            if (getComputedStyle(n).pointerEvents === 'none') return true;
        }
        return false;
    };

    document.querySelectorAll('input, select, textarea').forEach((el) => {
        const fs = parseFloat(getComputedStyle(el).fontSize);
        if (fs < 16) out.zoom.push(`${el.tagName.toLowerCase()}#${el.id || el.name || '?'} = ${fs}px`);
    });

    const interactive = 'a[href], button, input:not([type=hidden]), select, textarea, [role="button"], [role="tab"], summary';

    /** هدف‌های لمسیِ واقعی — برای قاعده‌ی فاصله هم همین فهرست به کار می‌رود. */
    const live = [];

    document.querySelectorAll(interactive).forEach((el) => {
        const b = el.getBoundingClientRect();
        if (b.width === 0 || b.height === 0) return;

        const st = getComputedStyle(el);
        if (st.visibility === 'hidden' || untouchable(el)) return;

        /*
         * لینک پرش پیش از فوکوس ۱×۱ پیکسل است و فقط با صفحه‌کلید ظاهر می‌شود؛
         * اصلاً هدف لمسی نیست. پیش از افزودن به فهرست کنار گذاشته می‌شود تا
         * قاعده‌ی فاصله هم سراغش نرود — وگرنه در هر صفحه یک «همسایه‌ی ۴
         * پیکسلی» جعلی می‌سازد که هیچ انگشتی با آن روبه‌رو نمی‌شود.
         */
        if (el.classList.contains('sr-only-focusable') || el.classList.contains('sr-only')) return;

        live.push(el);

        // لینک کشیده‌شده روی کل کارت: کارت هدف لمسی است، نه متن
        const after = getComputedStyle(el, '::after');
        if (after.position === 'absolute' && after.inset === '0px') return;

        // لینک درون متن جاری — استثنای صریح WCAG 2.5.8
        if (el.tagName === 'A' && st.display === 'inline') return;

        // ورودی فایلِ پنهان: دکمه‌ی بومی مرورگر ترجمه‌پذیر نیست، پس ورودی
        // sr-only شده و label متصل هدف لمسی واقعی است
        // (عنوانِ فیلد هم همان for را دارد، پس همه‌ی labelها بررسی می‌شوند)
        if (el.type === 'file' && el.classList.contains('sr-only') && el.id) {
            const labels = [...document.querySelectorAll(`label[for="${CSS.escape(el.id)}"]`)];
            const big = labels.some((lb) => {
                const r = lb.getBoundingClientRect();
                return Math.min(r.width, r.height) >= 24;
            });
            if (big) return;
        }

        // ورودی رادیو/چک‌باکس: برچسبِ در بر گیرنده هدف لمسی واقعی است
        if ((el.type === 'radio' || el.type === 'checkbox') && el.closest('label')) {
            const lb = el.closest('label').getBoundingClientRect();
            if (Math.min(lb.width, lb.height) >= 24) return;
        }

        if (Math.min(b.width, b.height) < MIN_TAP - 0.5) {
            out.tap.push(`${name(el)} ${Math.round(b.width)}×${Math.round(b.height)}`);
        }
    });

    /*
     * فاصله‌ی هدف‌های همسایه.
     *
     * ردیف‌های پشت‌سرهمِ یک فهرست استثنا هستند: وقتی هر دو هدف تمام‌عرض‌اند و
     * فقط یک خط جداشان می‌کند — مثل آکاردئونِ پرسش‌های متداول — انگشت ابهامی
     * ندارد، چون محورِ خطا عمودی است و هر ردیف ۴۴ پیکسل ارتفاع دارد.
     */
    for (let i = 0; i < live.length; i++) {
        for (let j = i + 1; j < live.length; j++) {
            if (live[i].contains(live[j]) || live[j].contains(live[i])) continue;

            const a = live[i].getBoundingClientRect();
            const c = live[j].getBoundingClientRect();
            const dx = Math.max(0, Math.max(a.left, c.left) - Math.min(a.right, c.right));
            const dy = Math.max(0, Math.max(a.top, c.top) - Math.min(a.bottom, c.bottom));
            const apart = Math.hypot(dx, dy);

            if (apart < 0.01 || apart >= MIN_GAP) continue;
            if (dx === 0 && a.width > W * 0.5 && c.width > W * 0.5) continue;

            out.gap.push(`${apart.toFixed(1)}px ${name(live[i])} ↔ ${name(live[j])}`);
        }
    }

    document.querySelectorAll('body *').forEach((el) => {
        const hasText = [...el.childNodes].some((n) => n.nodeType === 3 && n.textContent.trim().length > 1);
        if (hasText && parseFloat(getComputedStyle(el).fontSize) < 12) {
            out.tiny.push(`${el.tagName.toLowerCase()} "${el.textContent.trim().slice(0, 24)}"`);
        }

        if (getComputedStyle(el).touchAction === 'none') {
            const b = el.getBoundingClientRect();
            if (b.width > 60 && b.height > 60) {
                out.touchNone.push(name(el));
            }
        }
    });

    if (document.documentElement.scrollWidth > W + 1) {
        out.overflow = `${document.documentElement.scrollWidth} > ${W}`;
    }

    return out;
}

/*
 * اگر نسخه‌ی Playwright با مرورگر نصب‌شده هم‌خوان نباشد، مسیر صریح لازم است؛
 * CHROMIUM_PATH اولویت دارد و بعد مسیر متداول کروميوم سیستمی.
 */
const chromiumPath = [process.env.CHROMIUM_PATH, '/opt/pw-browsers/chromium']
    .find((candidate) => candidate && existsSync(candidate));

const browser = await chromium.launch({ executablePath: chromiumPath });

let failures = 0;

for (const vp of VIEWPORTS) {
    const page = await browser.newPage({
        viewport: { width: vp.width, height: vp.height },
        isMobile: true,
        hasTouch: true,
    });

    const found = { zoom: new Set(), tap: new Set(), gap: new Set(), tiny: new Set(), touchNone: new Set(), overflow: new Set() };

    const paths = [...PATHS];

    if (ADMIN_EMAIL && ADMIN_PASSWORD) {
        // صفحه‌ی ورود باید پیش از احراز هویت بررسی شود، وگرنه به داشبورد می‌رود.
        paths.push('/admin/login');

        await page.goto(BASE + '/admin/login', { waitUntil: 'domcontentloaded' });
        await page.fill('input[name="email"]', ADMIN_EMAIL);
        await page.fill('input[name="password"]', ADMIN_PASSWORD);
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'domcontentloaded' }).catch(() => {}),
            page.click('button[type="submit"]'),
        ]);

        if (new URL(page.url()).pathname === '/admin/login') {
            console.error('ورود به پنل ناموفق بود — مسیرهای مدیریت بررسی نشد.');
            process.exit(1);
        }

        paths.push(...ADMIN_PATHS);
    }

    for (const path of paths) {
        await page.goto(BASE + path, { waitUntil: 'domcontentloaded' });
        await page.addStyleTag({ content: FREEZE });
        await page.waitForTimeout(300);

        const r = await page.evaluate(collect, { MIN_TAP, MIN_GAP });
        r.zoom.forEach((x) => found.zoom.add(`${path} ${x}`));
        r.tap.forEach((x) => found.tap.add(`${path} ${x}`));
        r.gap.forEach((x) => found.gap.add(`${path} ${x}`));
        r.tiny.forEach((x) => found.tiny.add(`${path} ${x}`));
        r.touchNone.forEach((x) => found.touchNone.add(`${path} ${x}`));
        if (r.overflow) found.overflow.add(`${path} ${r.overflow}`);
    }

    await page.close();

    const rules = [
        [`کنترل فرم زیر ${MIN_FIELD_FONT}px (زوم iOS)`, found.zoom],
        [`هدف لمسی زیر ${MIN_TAP}px`, found.tap],
        [`فاصله‌ی دو هدف لمسی زیر ${MIN_GAP}px`, found.gap],
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
