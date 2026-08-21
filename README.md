# سفال کیان — وب‌سایت کارخانه بلوک سفالی

وب‌سایت محصول‌محور برای یک کارخانه‌ی تولید بلوک سفالی و مصالح ساختمانی سرامیکی.
پیاده‌سازی با **Laravel 12 + Blade + Tailwind CSS v4 + Alpine.js + GSAP/Lenis**،
کاملاً راست‌به‌چپ و فارسی، با تمرکز بر سه پرسونای واقعی سایت صنعتی:

| پرسونا | پرسش او | پاسخ سایت |
|---|---|---|
| کارفرما و مشتری | «چه محصولی برای پروژه‌ام مناسب است؟» | موتور انتخاب محصول (`/product-finder`) |
| مهندس و معمار | «مشخصات فنی و فایل‌های اجرایی کجاست؟» | مرکز فنی: دیتاشیت، CAD، BIM (`/technical`) |
| پیمانکار و مجری | «چطور اجرا می‌شود؟» | راهنمای اجرا (`/technical/installation`) |

---

## نصب روی سرور اوبونتو — تک دستور

```bash
curl -fsSL https://raw.githubusercontent.com/ferya3/kian/claude/ceramic-factory-website-p5je97/deploy/install.sh | sudo bash
```

با دامنه، SSL و MySQL:

```bash
curl -fsSL https://raw.githubusercontent.com/ferya3/kian/claude/ceramic-factory-website-p5je97/deploy/install.sh \
  | sudo DOMAIN=kian-ceramic.ir SSL=1 DB=mysql bash
```

اسکریپت PHP 8.4 (به‌همراه intl و gd)، Node 22، Composer و nginx را نصب می‌کند،
سورس را می‌گیرد، assets را می‌سازد، دیتابیس را مهاجرت و seed می‌کند، دسترسی‌ها را
تنظیم می‌کند و vhost را با کش یک‌ساله برای فایل‌های هش‌دار می‌نویسد.

| متغیر | پیش‌فرض | توضیح |
|---|---|---|
| `DOMAIN` | — | دامنه؛ خالی یعنی روی IP سرور سرو می‌شود |
| `SSL` | `0` | گرفتن گواهی Let's Encrypt (نیازمند DNS آماده) |
| `DB` | `sqlite` | یا `mysql` — کاربر و دیتابیس خودکار ساخته می‌شود |
| `APP_DIR` | `/var/www/kian` | مسیر نصب |
| `BRANCH` | برنچ توسعه | برنچ گیت |
| `SKIP_SYSTEM` | `0` | پرش از نصب بسته‌های سیستمی |

**به‌روزرسانی بعدی** (فقط مهاجرت‌های جدید، بدون seed مجدد):

```bash
cd /var/www/kian && sudo SKIP_SYSTEM=1 bash deploy/install.sh
```

---

## راه‌اندازی محلی

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite          # یا DB_CONNECTION را روی mysql/pgsql بگذارید
php artisan migrate --seed              # داده‌ی نمونه‌ی کامل و واقع‌نما

npm run build                           # یا: npm run dev
php artisan serve
```

پیش‌فرض روی SQLite است. برای MySQL/PostgreSQL کافی است `DB_*` در `.env` تنظیم شود؛
هیچ‌جای کد به درایور خاصی وابسته نیست.

---

## معماری

### دامنه

```
app/
├── Models/            Product, ProductCategory, ProductCavity, Project, Solution,
│                      ProcessStep, FactorySection, Stat, Certificate, Distributor,
│                      Document, Article, Faq, ContactMessage, Setting
├── Services/
│   └── ProductFinder  موتور امتیازدهی «محصول مناسب پروژه»
└── Support/
    ├── Seo            جمع‌کننده‌ی متادیتای هر صفحه
    ├── Schema         تولید JSON-LD (Organization, Product, Article, FAQ, Breadcrumb…)
    ├── Navigation     منبع واحد ساختار منو برای دسکتاپ، موبایل و فوتر
    ├── Jalali         تاریخ شمسی و ارقام فارسی با intl
    └── Slug           slug فارسی‌پسند (Str::slug روی فارسی رشته‌ی خالی می‌دهد)
```

`config/kian.php` تنها منبع حقیقت برای برند، اطلاعات تماس، گزینه‌های موتور انتخاب
محصول، وزن‌های امتیازدهی و پیش‌فرض‌های سئو است.

### موتور انتخاب محصول

`ProductFinder` به‌جای فیلتر سخت‌گیرانه، به هر محصول امتیاز تطابق می‌دهد
(نوع پروژه ۲۵، نوع دیوار ۳۰، ضخامت ۲۵، سطح عایق ۲۰) و **دلیل** انتخاب را هم
برمی‌گرداند. نتیجه: کاربر هیچ‌وقت به بن‌بست «محصولی یافت نشد» نمی‌خورد.

فرم بدون جاوااسکریپت هم کار می‌کند (submit عادی به `/product-finder/results`)؛
با جاوااسکریپت، نتیجه بدون بارگذاری مجدد در همان صفحه تزریق می‌شود.

### سئو

- هر محصول URL مستقل و پایدار دارد: `/products/ceramic-block-20`
- `sitemap.xml` و `robots.txt` به‌صورت داینامیک تولید می‌شوند
- JSON-LD روی همه‌ی صفحات: `Organization` + `BreadcrumbList` + نوع اختصاصی صفحه
- صفحات نتیجه‌ی جستجو و فیلتر `noindex` می‌شوند

---

## زبان بصری

| نقش | مقدار |
|---|---|
| پس‌زمینه | `#F5F3EF` — کرم سنگی، رنگ خاک خام |
| رنگ اصلی | `#B4552D` — خاک رس پخته (terracotta) |
| گرافیت | `#171717` |
| Accent | `#E2732F` — نارنجی سفالی، فقط برای تأکید |
| تایپوگرافی فارسی | Vazirmatn (variable، خودمیزبان) |
| تایپوگرافی فنی | Inter (variable، برای اعداد و واحدها) |

مقیاس تایپوگرافی و توکن‌های رنگ در `resources/css/app.css` داخل `@theme` تعریف
شده‌اند و در کل پروژه از همان‌ها استفاده می‌شود.

**بدون عکس استوک.** تمام تصاویر مولد و وکتور هستند:

- `<x-block-3d>` — مکعب CSS 3D که ابعادش از ابعاد واقعی محصول در دیتابیس مشتق می‌شود
- `<x-block-section>` — مقطع SVG با آرایش حفره‌ها بر اساس ضخامت و طول واقعی بلوک
- `<x-factory-plan>` — نمای ایزومتریک محوطه‌ی کارخانه با نقاط تعاملی
- `<x-project-cover>` — نمای انتزاعی معماری، قطعی بر اساس slug پروژه
- `<x-hero-scene>` — صحنه‌ی سینمایی چندلایه با پارالاکس

عمداً از Three.js استفاده نشده: یک مکعب شش‌وجهی به WebGL نیاز ندارد و بار GPU و
حجم باندل را بی‌دلیل بالا می‌برد. هر جای دیگر که مدل سه‌بعدی واقعی لازم شود،
`<x-block-3d>` نقطه‌ی تعویض است.

---

## حرکت و انیمیشن

انیمیشن در خدمت محصول است، نه برعکس:

| بخش | حرکت |
|---|---|
| Hero | پارالاکس آرام لایه‌ها + نفس‌کشیدن نور کوره |
| اعداد کارخانه | شمارش صعودی هنگام ورود به viewport |
| «از خاک تا سازه» | pin شدن صفحه و حرکت افقی ۹ مرحله (GSAP + ScrollTrigger) |
| کارت محصول | ظاهر شدن میله‌های عملکردی با hover |
| بلوک تعاملی | چرخش با drag یا کلیدهای جهت |
| ناوبری | مِگا منوی نرم با تأخیر خروج ماوس |

- `prefers-reduced-motion` در CSS و در هر ماژول JS جداگانه رعایت شده است.
- اسکرول نرم (Lenis) فقط روی دسکتاپ با pointer دقیق فعال می‌شود؛ روی موبایل
  اسکرول بومی دست‌نخورده می‌ماند.
- GSAP و ScrollTrigger با dynamic import بارگذاری می‌شوند و فقط در صفحاتی که
  تایم‌لاین دارند دانلود می‌شوند.

**نکته‌ی RTL:** در چیدمان راست‌به‌چپ کارت‌ها از لبه‌ی راست به چپ ادامه پیدا
می‌کنند، پس ریل افقی باید به سمت **راست** (`x` مثبت) حرکت کند — برعکس LTR.
فاصله‌ی پیمایش هم از موقعیت واقعی اولین و آخرین کارت اندازه‌گیری می‌شود، نه از
`scrollWidth` که در RTL قابل اتکا نیست.

---

## دسترس‌پذیری

- ناوبری کامل با کیبورد؛ مِگا منو، نقشه‌ی کارخانه و بلوک سه‌بعدی همه با کلیدهای
  جهت کار می‌کنند
- `skip link`، landmarkهای معنایی، `aria-current`، `aria-expanded`، `role="meter"`
  برای نمودارهای میله‌ای
- فرم تماس: `label` واقعی برای هر فیلد، `aria-invalid`، پیام خطای متصل با
  `aria-describedby`، و تله‌ی ربات به‌جای CAPTCHA
- ارقام فارسی با `tabular-nums`؛ مقادیر فنیِ لاتین داخل `<bdi dir="ltr">` ایزوله
  می‌شوند تا در متن RTL جابه‌جا نشوند (`x-num`)
- موبایل نسخه‌ی کوچک‌شده‌ی دسکتاپ نیست: کشوی اختصاصی، ریل‌های قابل swipe و
  میله‌های عملکردی که همیشه پیدا هستند

---

## پنل مدیریت

ساختار دامنه برای Filament آماده است (همه‌ی مدل‌ها `$guarded = []` و روابط کامل
دارند). برای افزودن پنل:

```bash
composer require filament/filament
php artisan filament:install --panels
php artisan make:filament-resource Product --generate
```

منابع پیشنهادی: `Product`، `ProductCategory`، `Project`، `Solution`،
`ProcessStep`، `FactorySection`، `Document`، `Certificate`، `Distributor`،
`Article`، `Faq`، `ContactMessage`، `Setting`.

## آپلود فایل‌های واقعی

- فایل‌های فنی روی دیسک `public` ذخیره می‌شوند (`php artisan storage:link`) و
  مسیرشان در `documents.file_path` می‌نشیند. تا وقتی فایلی آپلود نشده، لینک
  دانلود کاربر را به فرم درخواست هدایت می‌کند — نه به صفحه‌ی ۴۰۴.
- ویدئوی سینمایی Hero: مقدار تنظیم `hero_video` را با مسیر فایل پر کنید؛ تا آن
  زمان صحنه‌ی وکتور `<x-hero-scene>` نمایش داده می‌شود.
