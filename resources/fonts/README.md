# قلم‌های محلی

`luciano.woff2` نشان‌واژه‌ی لبه‌های قاب را می‌نویسد (`.frame-mark` در `app.css`).
تنها جایی است که این قلم به کار می‌رود؛ متن سایت با Vazirmatn و InterVar ست
می‌شود و آن‌ها از `node_modules` می‌آیند.

`luciano.otf` نسخه‌ی اصلی است و سرو نمی‌شود — فقط برای اینکه تبدیل دوباره
قابل تکرار باشد:

```bash
pip install fonttools brotli
python3 -c "from fontTools.ttLib import TTFont; f=TTFont('resources/fonts/luciano.otf'); f.flavor='woff2'; f.save('resources/fonts/luciano.woff2')"
```

قلمی لاتین و شکسته است و هیچ حرف فارسی ندارد؛ به همین دلیل `unicode-range` آن
در `app.css` به `A-Z` و `a-z` محدود شده تا مرورگر برای متن فارسی سراغش نرود.
