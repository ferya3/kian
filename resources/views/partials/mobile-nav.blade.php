{{-- منوی موبایل — طراحی مستقل، نه نسخه‌ی کوچک‌شده‌ی دسکتاپ --}}
<div x-data="mobileNav"
     @toggle-mobile-nav.window="toggle()"
     @keydown.escape.window="open && hide()"
     class="no-print lg:hidden">

    <div x-show="open" x-transition.opacity.duration.250ms
         @click="hide()"
         class="fixed inset-0 z-[60] bg-ink-950/60 backdrop-blur-sm"
         style="display: none" aria-hidden="true"></div>

    <div x-show="open" x-ref="panel"
         x-transition:enter="transition ease-[var(--ease-out-expo)] duration-400"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-250"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 z-[70] flex w-[min(24rem,90vw)] flex-col bg-sand-50 shadow-float"
         style="display: none"
         role="dialog" aria-modal="true" aria-label="منوی اصلی">

        <div class="flex h-16 shrink-0 items-center justify-between border-b border-sand-200 px-5">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <x-brand-mark class="h-8 w-8" />
                <span class="font-extrabold">{{ config('kian.brand.name') }}</span>
            </a>
            <button type="button" @click="hide()" x-ref="trigger"
                    class="grid h-10 w-10 place-items-center rounded-full text-ink-500 transition hover:bg-sand-200"
                    aria-label="بستن منو">
                <x-icon name="close" size="20" />
            </button>
        </div>

        <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-4" data-lenis-prevent>
            <form action="{{ route('search') }}" method="GET" class="relative mb-5">
                <label for="mobile-search" class="sr-only">جستجو</label>
                <input id="mobile-search" name="q" type="search" placeholder="جستجوی محصول یا فایل فنی…"
                       class="w-full rounded-xl border border-sand-300 bg-sand-100 py-3 pr-11 pl-4 text-[0.9375rem] outline-none focus:border-clay-400">
                <x-icon name="search" size="18" class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-ink-300" />
            </form>

            <nav aria-label="ناوبری موبایل">
                <ul class="divide-y divide-sand-200">
                    <li>
                        <a href="{{ route('products.index') }}" class="flex items-center justify-between py-4 text-lg font-bold">
                            محصولات
                            <x-icon name="chevron-left" size="18" class="text-ink-300" />
                        </a>
                        <ul class="-mt-1 mb-3 space-y-0.5 pr-3">
                            @foreach($megaMenu as $group)
                                @foreach($group->children as $child)
                                    <li>
                                        <a href="{{ route('products.index', ['category' => $child->slug]) }}"
                                           class="flex items-center justify-between rounded-lg px-3 py-2.5 text-[0.9375rem] text-ink-600 transition active:bg-sand-200">
                                            {{ $child->name }}
                                            <span class="tech text-xs text-ink-300">{{ \App\Support\Jalali::digits($child->products_count) }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            @endforeach
                        </ul>
                    </li>

                    @foreach($navigation as $item)
                        @continue($item['route'] === 'products.index')
                        <li>
                            @if(!empty($item['children']))
                                <button type="button" @click="section('{{ $item['route'] }}')"
                                        :aria-expanded="expanded === '{{ $item['route'] }}' ? 'true' : 'false'"
                                        class="flex w-full items-center justify-between py-4 text-lg font-bold">
                                    {{ $item['label'] }}
                                    <x-icon name="chevron-down" size="18" class="text-ink-300 transition-transform duration-300"
                                            ::class="expanded === '{{ $item['route'] }}' && 'rotate-180'" />
                                </button>
                                <ul x-show="expanded === '{{ $item['route'] }}'"
                                    x-transition:enter="transition ease-[var(--ease-out-expo)] duration-300"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="-mt-1 mb-3 space-y-0.5 pr-3" style="display:none">
                                    @foreach($item['children'] as $child)
                                        <li>
                                            <a href="{{ route($child['route']) }}"
                                               class="block rounded-lg px-3 py-2.5 text-[0.9375rem] text-ink-600 transition active:bg-sand-200">
                                                {{ $child['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <a href="{{ route($item['route']) }}" class="flex items-center justify-between py-4 text-lg font-bold">
                                    {{ $item['label'] }}
                                    <x-icon name="chevron-left" size="18" class="text-ink-300" />
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>

        <div class="shrink-0 space-y-3 border-t border-sand-200 bg-sand-100 p-5">
            <a href="{{ route('finder.show') }}"
               class="flex items-center justify-center gap-2 rounded-xl bg-clay-500 py-3.5 font-semibold text-white">
                <x-icon name="compass" size="18" />
                محصول مناسب پروژه‌ام را پیدا کن
            </a>
            <div class="grid grid-cols-2 gap-3">
                <a href="tel:{{ config('kian.contact.phone_raw') }}"
                   class="flex items-center justify-center gap-2 rounded-xl border border-sand-300 bg-sand-50 py-3 text-sm font-semibold">
                    <x-icon name="phone" size="16" />
                    تماس
                </a>
                <a href="{{ route('contact') }}"
                   class="flex items-center justify-center gap-2 rounded-xl bg-ink-900 py-3 text-sm font-semibold text-sand-50">
                    درخواست قیمت
                </a>
            </div>
        </div>
    </div>
</div>
