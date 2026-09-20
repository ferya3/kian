@php use App\Support\Locales; @endphp
<!DOCTYPE html>
{{-- زبان، جهت و قلم هر سه از config/locales.php می‌آیند --}}
<html lang="{{ Locales::html() }}" dir="{{ Locales::dir() }}" data-font="{{ Locales::meta()['font'] ?? 'vazirmatn' }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f5f3ef" media="(prefers-color-scheme: light)">
    <meta name="format-detection" content="telephone=no">

    <title>{{ $seo->fullTitle() }}</title>
    <meta name="description" content="{{ $seo->metaDescription() }}">
    <link rel="canonical" href="{{ $seo->canonicalUrl() }}">

    {{--
        hreflang — به موتور جستجو می‌گوید همین صفحه به زبان‌های دیگر کجاست.
        x-default برای بازدیدکننده‌ای است که هیچ‌کدام زبانش نیست.
    --}}
    @foreach($seo->alternates() as $code => $href)
        <link rel="alternate" hreflang="{{ Locales::html($code) }}" href="{{ $href }}">
    @endforeach
    @if($default = ($seo->alternates()[config('locales.fallback')] ?? null))
        <link rel="alternate" hreflang="x-default" href="{{ $default }}">
    @endif
    @if($seo->noindex)
        <meta name="robots" content="noindex, follow">
    @else
        <meta name="robots" content="index, follow, max-image-preview:large">
    @endif

    <meta property="og:site_name" content="{{ config('kian.brand.legal_name') }}">
    <meta property="og:locale" content="{{ str_replace('-', '_', Locales::html()) }}">
    @foreach($seo->alternates() as $code => $href)
        @continue($code === Locales::current())
        <meta property="og:locale:alternate" content="{{ str_replace('-', '_', Locales::html($code)) }}">
    @endforeach
    <meta property="og:type" content="{{ $seo->type }}">
    <meta property="og:title" content="{{ $seo->fullTitle() }}">
    <meta property="og:description" content="{{ $seo->metaDescription() }}">
    <meta property="og:url" content="{{ $seo->canonicalUrl() }}">
    <meta property="og:image" content="{{ $seo->ogImage() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo->fullTitle() }}">
    <meta name="twitter:description" content="{{ $seo->metaDescription() }}">
    <meta name="twitter:image" content="{{ $seo->ogImage() }}">

    @php $favicon = \App\Models\SiteMedia::url('brand.favicon'); @endphp
    @if($favicon)
        {{-- نوع اعلام نمی‌شود: فایل آپلودی می‌تواند png یا svg باشد و مرورگر خودش تشخیص می‌دهد --}}
        <link rel="icon" href="{{ $favicon }}">
        <link rel="apple-touch-icon" href="{{ $favicon }}">
    @else
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @endif
    <link rel="sitemap" type="application/xml" href="{{ route('sitemap') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script type="application/ld+json">@json(['@context' => 'https://schema.org', '@graph' => $seo->jsonLd()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>

    @stack('head')
</head>
{{--
    رنگ زمینه عمداً کلاس Tailwind نیست: لایه‌ی utilities بعد از components
    می‌آید و bg-sand-100 روی زمینه‌ی تیره‌ی قاب را می‌گرفت. هر دو حالت در
    app.css تعریف شده‌اند.
--}}
<body class="min-h-dvh text-ink-900 antialiased">
    <a href="#main"
       class="sr-only-focusable fixed z-[100] inline-flex min-h-11 items-center rounded-full bg-ink-900 px-5 text-sm font-semibold text-sand-50 shadow-float"
       style="top: calc(1rem + var(--safe-top)); inset-inline-end: 1rem">
        {{ __('site.nav.skip') }}
    </a>

    @include('partials.header')
    @include('partials.mobile-nav')

    {{--
        نشان‌واژه روی دو لبه‌ی قاب.

        تزیینِ قاب است و نه محتوا، پس aria-hidden می‌ماند؛ نامِ برند در هدر و
        فوتر خوانده می‌شود. زیر lg پنهان می‌ماند — چون روی گوشی قابی در کار
        نیست که لبه‌ای داشته باشد.
    --}}
    @php $wordmark = config('kian.brand.wordmark'); @endphp
    @if($wordmark)
        <div class="frame-mark-rail" aria-hidden="true">
            <span class="frame-mark frame-mark-left" dir="ltr">{{ $wordmark }}</span>
            <span class="frame-mark frame-mark-right" dir="ltr">{{ $wordmark }}</span>
        </div>
    @endif

    {{--
        قاب سایت. هدر عمداً بیرونش می‌ماند: fixed است و باید روی هیرو شناور
        بماند، ولی خودش را با همین عرض هم‌تراز می‌کند.
    --}}
    <div class="page-shell">
        {{-- جبران ارتفاع هدر ثابت برای صفحات بدون قهرمان تمام‌قد --}}
        @unless(\App\Support\Navigation::overHero())
            <div class="header-offset" aria-hidden="true"></div>
        @endunless

        <main id="main" class="focus:outline-none">
            {{ $slot }}
        </main>

        @include('partials.footer')
    </div>

    @stack('scripts')
</body>
</html>
