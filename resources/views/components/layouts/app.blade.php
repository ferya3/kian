<!DOCTYPE html>
<html lang="fa" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f5f3ef" media="(prefers-color-scheme: light)">
    <meta name="format-detection" content="telephone=no">

    <title>{{ $seo->fullTitle() }}</title>
    <meta name="description" content="{{ $seo->metaDescription() }}">
    <link rel="canonical" href="{{ $seo->canonicalUrl() }}">
    @if($seo->noindex)
        <meta name="robots" content="noindex, follow">
    @else
        <meta name="robots" content="index, follow, max-image-preview:large">
    @endif

    <meta property="og:site_name" content="{{ config('kian.brand.legal_name') }}">
    <meta property="og:locale" content="fa_IR">
    <meta property="og:type" content="{{ $seo->type }}">
    <meta property="og:title" content="{{ $seo->fullTitle() }}">
    <meta property="og:description" content="{{ $seo->metaDescription() }}">
    <meta property="og:url" content="{{ $seo->canonicalUrl() }}">
    <meta property="og:image" content="{{ $seo->ogImage() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo->fullTitle() }}">
    <meta name="twitter:description" content="{{ $seo->metaDescription() }}">
    <meta name="twitter:image" content="{{ $seo->ogImage() }}">

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="sitemap" type="application/xml" href="{{ route('sitemap') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script type="application/ld+json">@json(['@context' => 'https://schema.org', '@graph' => $seo->jsonLd()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>

    @stack('head')
</head>
<body class="min-h-dvh bg-sand-100 text-ink-900 antialiased">
    <a href="#main"
       class="sr-only-focusable fixed top-4 right-4 z-[100] rounded-full bg-ink-900 px-5 py-3 text-sm font-semibold text-sand-50 shadow-float">
        پرش به محتوای اصلی
    </a>

    @include('partials.header')
    @include('partials.mobile-nav')

    <main id="main" class="focus:outline-none">
        {{ $slot }}
    </main>

    @include('partials.footer')

    @stack('scripts')
</body>
</html>
