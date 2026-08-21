@php
    use App\Support\Navigation;
    // صفحاتی که قهرمان تیره‌ی تمام‌قد دارند، هدر شفاف با متن روشن می‌گیرند.
    $overHero = request()->routeIs('home');
@endphp

<header x-data="siteHeader({{ $overHero ? 'true' : 'false' }})"
        @keydown.escape.window="close(); searchOpen = false"
        class="no-print fixed inset-x-0 top-0 z-50 transition-transform duration-500 ease-[var(--ease-out-expo)]"
        style="padding-top: var(--safe-top)"
        :class="hidden ? '-translate-y-full' : 'translate-y-0'">

    {{-- نوار خدماتی — تماس مستقیم و میان‌بر مهندسان --}}
    <div class="hidden overflow-hidden bg-ink-950 text-sand-200 transition-[height] duration-500 ease-[var(--ease-out-expo)] lg:block"
         :class="scrolled ? 'h-0' : 'h-10'">
        <div class="container-page flex h-10 items-center justify-between text-meta">
            <div class="flex items-center gap-6">
                <a href="tel:{{ config('kian.contact.phone_raw') }}"
                   class="flex h-10 items-center gap-2 transition hover:text-clay-300">
                    <x-icon name="phone" size="15" />
                    <span class="tech">{{ config('kian.contact.phone') }}</span>
                </a>
                <span class="flex items-center gap-2 text-sand-200/55">
                    <x-icon name="clock" size="15" />
                    {{ config('kian.contact.working_hours') }}
                </span>
            </div>
            <div class="flex items-center gap-5">
                <a href="{{ route('technical.downloads') }}" class="flex items-center gap-1.5 transition hover:text-clay-300">
                    <x-icon name="download" size="15" />
                    دانلود دیتاشیت، CAD و BIM
                </a>
                <span class="h-4 w-px bg-sand-200/20"></span>
                <a href="{{ route('distributors') }}" class="transition hover:text-clay-300">نمایندگان فروش</a>
            </div>
        </div>
    </div>

    {{-- نوار اصلی --}}
    <div class="transition-colors duration-300"
         :class="onDark ? 'bg-transparent' : 'bg-sand-50/95 backdrop-blur-xl border-b border-sand-300'"
         @mouseleave="scheduleClose()">
        <div class="container-page flex h-16 items-center gap-2 lg:h-[4.5rem] lg:gap-4">

            {{--
                موبایل: همبرگر سمت راست، لوگو وسط، جستجو سمت چپ.
                همان عناصر روی دسکتاپ به چیدمان افقی متعارف برمی‌گردند —
                بدون تکرار مارک‌آپ، فقط با ترتیب و flex.
            --}}
            <button type="button" @click="$dispatch('toggle-mobile-nav')"
                    class="tap-icon shrink-0 rounded-full transition lg:hidden"
                    :class="onDark ? 'text-sand-50 hover:bg-white/10' : 'text-ink-800 hover:bg-sand-200'"
                    aria-label="باز کردن منو">
                <x-icon name="menu" size="24" />
            </button>

            <a href="{{ route('home') }}"
               class="flex min-h-11 flex-1 items-center justify-center gap-2.5 lg:min-w-11 lg:flex-none lg:justify-start lg:gap-3"
               aria-label="{{ config('kian.brand.legal_name') }} — صفحه اصلی">
                <x-brand-mark class="h-8 w-8 shrink-0 sm:h-9 sm:w-9 lg:h-10 lg:w-10" />
                <span class="leading-tight">
                    <span class="block text-[1.0625rem] font-extrabold tracking-tight transition-colors"
                          :class="onDark ? 'text-sand-50' : 'text-ink-900'">{{ config('kian.brand.name') }}</span>
                    <span class="tech hidden text-micro uppercase tracking-[0.18em] transition-colors sm:block"
                          :class="onDark ? 'text-sand-200/55' : 'text-ink-400'">Ceramic Blocks</span>
                </span>
            </a>

            {{-- ناوبری دسکتاپ --}}
            <nav class="mr-auto hidden items-center lg:flex" aria-label="ناوبری اصلی">
                @foreach($navigation as $item)
                    @php
                        $active = Navigation::isActive($item);
                        $hasPanel = ($item['mega'] ?? false) || !empty($item['children']);
                    @endphp
                    <div class="relative" @if($hasPanel) @mouseenter="open('{{ $item['route'] }}')" @endif>
                        <a href="{{ route($item['route']) }}"
                           @if($hasPanel)
                               @focus="open('{{ $item['route'] }}')"
                               :aria-expanded="openMenu === '{{ $item['route'] }}' ? 'true' : 'false'"
                               aria-haspopup="true"
                           @endif
                           @if($active) aria-current="page" @endif
                           class="relative flex items-center gap-1 px-4 py-2.5 text-[0.9375rem] font-semibold transition-colors"
                           :class="onDark
                                ? ({{ $active ? 'true' : 'false' }} ? 'text-clay-300' : 'text-sand-100/85 hover:text-white')
                                : ({{ $active ? 'true' : 'false' }} ? 'text-clay-600' : 'text-ink-700 hover:text-clay-600')">
                            {{ $item['label'] }}
                            @if($hasPanel)
                                <x-icon name="chevron-down" size="14"
                                        class="transition-transform duration-300"
                                        ::class="openMenu === '{{ $item['route'] }}' && 'rotate-180'" />
                            @endif
                            <span class="absolute inset-x-4 -bottom-px h-0.5 origin-right bg-clay-500 transition-transform duration-300 ease-[var(--ease-out-expo)] {{ $active ? 'scale-x-100' : 'scale-x-0' }}"
                                  :class="openMenu === '{{ $item['route'] }}' && 'scale-x-100'"></span>
                        </a>
                    </div>
                @endforeach
            </nav>

            <div class="flex shrink-0 items-center gap-1 lg:gap-2">
                <button type="button" @click="openSearch()"
                        class="tap-icon rounded-full transition"
                        :class="onDark ? 'text-sand-100 hover:bg-white/10' : 'text-ink-600 hover:bg-sand-200 hover:text-ink-900'"
                        aria-label="جستجو در سایت">
                    <x-icon name="search" size="24" />
                </button>

                <a href="{{ route('contact') }}"
                   class="hidden items-center gap-2 rounded-full px-5 py-2.5 text-sm font-semibold transition lg:flex"
                   :class="onDark ? 'bg-clay-500 text-white hover:bg-clay-400' : 'bg-ink-900 text-sand-50 hover:bg-clay-600'">
                    درخواست قیمت
                </a>
            </div>
        </div>

        @include('partials.mega-menu')
    </div>

    @include('partials.search-overlay')
</header>

{{-- جبران ارتفاع هدر ثابت برای صفحات بدون قهرمان تمام‌قد --}}
@unless($overHero)
    <div class="header-offset" aria-hidden="true"></div>
@endunless
