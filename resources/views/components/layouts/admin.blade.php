@php use App\Support\Admin\Registry; @endphp
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'پنل مدیریت' }} — {{ config('kian.brand.name') }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh bg-sand-100 text-ink-900 antialiased">

<div x-data="{ nav: false }" class="lg:flex">

    {{-- پرده‌ی موبایل --}}
    <div x-show="nav" x-transition.opacity @click="nav = false"
         class="fixed inset-0 z-40 bg-ink-950/50 lg:hidden" style="display:none"></div>

    {{-- ناوبری --}}
    {{--
        نقطه‌ی شکست را CSS تعیین می‌کند، نه جاوااسکریپت: x-show فقط وضعیت کشوی
        موبایل است و .admin-sidebar در ≥۱۰۲۴px با display:flex !important آن را
        پس می‌گیرد. اگر شرط عرض داخل x-show می‌آمد، با چرخاندن دستگاه دوباره
        ارزیابی نمی‌شد (window.innerWidth واکنشی نیست) و کشو پنهان می‌ماند.
        display:none تعبیه‌شده هم از پرش کشو پیش از بالاآمدن Alpine جلوگیری می‌کند.
    --}}
    <aside x-show="nav"
           x-transition:enter="transition ease-[var(--ease-out-expo)] duration-300"
           x-transition:enter-start="translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-200"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="translate-x-full"
           class="admin-sidebar fixed inset-y-0 right-0 z-50 flex w-72 shrink-0 flex-col bg-ink-950 text-sand-200 lg:sticky lg:top-0 lg:h-dvh"
           style="padding-top: var(--safe-top); padding-bottom: var(--safe-bottom); display: none">

        <div class="flex h-16 shrink-0 items-center justify-between gap-3 border-b border-white/[0.07] px-5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                <x-brand-mark class="h-8 w-8" />
                <span class="leading-tight">
                    <span class="block text-[0.9375rem] font-extrabold text-sand-50">پنل مدیریت</span>
                    <span class="tech block text-micro uppercase tracking-[0.16em] text-sand-200/45">{{ config('kian.brand.name_en') }}</span>
                </span>
            </a>
            <button type="button" @click="nav = false" class="tap-icon rounded-lg text-sand-200/60 lg:hidden" aria-label="بستن منو">
                <x-icon name="close" size="20" />
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4" aria-label="بخش‌های پنل">
            <a href="{{ route('admin.dashboard') }}"
               @class([
                   'mb-4 flex items-center gap-3 rounded-xl px-3 py-2.5 text-[0.9375rem] font-semibold transition',
                   'bg-clay-500 text-white' => request()->routeIs('admin.dashboard'),
                   'text-sand-200/75 hover:bg-white/[0.06] hover:text-sand-50' => ! request()->routeIs('admin.dashboard'),
               ])>
                <x-icon name="grid" size="18" />
                نمای کلی
            </a>

            <a href="{{ route('admin.media') }}"
               @class([
                   'mb-4 flex items-center gap-3 rounded-xl px-3 py-2.5 text-[0.9375rem] font-semibold transition',
                   'bg-clay-500 text-white' => request()->routeIs('admin.media'),
                   'text-sand-200/75 hover:bg-white/[0.06] hover:text-sand-50' => ! request()->routeIs('admin.media'),
               ])>
                <x-icon name="layers" size="18" />
                کتابخانه‌ی تصاویر
            </a>

            @foreach(Registry::navigation() as $group => $resources)
                <p class="eyebrow px-3 pb-2 pt-4 text-sand-200/35">{{ $group }}</p>
                <ul class="space-y-0.5">
                    @foreach($resources as $item)
                        @php $active = request()->route('resource') === $item::$slug; @endphp
                        <li>
                            <a href="{{ route('admin.resource.index', $item::$slug) }}"
                               @class([
                                   'flex items-center gap-3 rounded-xl px-3 py-2.5 text-[0.9375rem] transition',
                                   'bg-white/[0.09] font-semibold text-sand-50' => $active,
                                   'text-sand-200/70 hover:bg-white/[0.05] hover:text-sand-50' => ! $active,
                               ])>
                                <x-icon :name="$item::$icon" size="17"
                                        class="{{ $active ? 'text-clay-400' : 'text-sand-200/40' }}" />
                                {{ $item::$label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endforeach

            @if(auth()->user()?->isAdmin())
                <p class="eyebrow px-3 pb-2 pt-4 text-sand-200/35">نظارت</p>
                <a href="{{ route('admin.activity') }}"
                   @class([
                       'flex items-center gap-3 rounded-xl px-3 py-2.5 text-[0.9375rem] transition',
                       'bg-white/[0.09] font-semibold text-sand-50' => request()->routeIs('admin.activity'),
                       'text-sand-200/70 hover:bg-white/[0.05] hover:text-sand-50' => ! request()->routeIs('admin.activity'),
                   ])>
                    <x-icon name="clock" size="17" class="text-sand-200/40" />
                    گزارش فعالیت
                </a>
            @endif
        </nav>

        <div class="shrink-0 border-t border-white/[0.07] p-3">
            <div class="flex items-center gap-3 rounded-xl px-3 py-2.5">
                <span class="tech grid h-9 w-9 shrink-0 place-items-center rounded-full bg-clay-500/20 text-sm font-bold text-clay-300">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </span>
                <span class="min-w-0 flex-1 leading-tight">
                    <span class="block truncate text-[0.875rem] font-semibold text-sand-50">{{ auth()->user()->name }}</span>
                    <span class="block text-micro text-sand-200/45">{{ auth()->user()->roleLabel() }}</span>
                </span>
            </div>
            <div class="mt-1 grid grid-cols-2 gap-2">
                <a href="{{ route('home') }}" target="_blank" rel="noopener"
                   class="tap justify-center gap-1.5 rounded-lg border border-white/10 text-meta text-sand-200/70 transition hover:border-white/25 hover:text-sand-50">
                    <x-icon name="external" size="14" />
                    سایت
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                            class="tap w-full justify-center rounded-lg border border-white/10 text-meta text-sand-200/70 transition hover:border-red-400/40 hover:text-red-300">
                        خروج
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- محتوا --}}
    <div class="min-w-0 flex-1">
        <header class="sticky top-0 z-30 border-b border-sand-300 bg-sand-100/95 backdrop-blur-md"
                style="padding-top: var(--safe-top)">
            <div class="flex h-16 items-center gap-3 px-4 lg:px-8">
                <button type="button" @click="nav = true" class="tap-icon rounded-lg text-ink-700 lg:hidden" aria-label="باز کردن منو">
                    <x-icon name="menu" size="22" />
                </button>

                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-lg font-extrabold">{{ $title ?? 'نمای کلی' }}</h1>
                    @isset($subtitle)
                        <p class="truncate text-meta text-ink-400">{{ $subtitle }}</p>
                    @endisset
                </div>

                @isset($actions)
                    <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
                @endisset
            </div>
        </header>

        <main class="px-4 py-6 lg:px-8 lg:py-8">
            @if(session('success'))
                <div role="status" class="mb-6 flex items-start gap-3 rounded-xl border border-clay-200 bg-clay-50 px-4 py-3">
                    <x-icon name="check" size="18" class="mt-0.5 shrink-0 text-clay-600" />
                    <p class="font-semibold text-clay-800">{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div role="alert" class="mb-6 rounded-xl border border-red-300 bg-red-50 px-4 py-3">
                    <p class="font-bold text-red-800">لطفاً موارد زیر را اصلاح کنید:</p>
                    <ul class="mt-1.5 list-inside list-disc space-y-1 text-[0.9375rem] text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
