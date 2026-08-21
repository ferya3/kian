{{-- مِگا منوی محصولات و منوهای کشویی ساده --}}
@php use App\Support\Navigation; @endphp

<div x-show="openMenu"
     x-transition:enter="transition ease-[var(--ease-out-expo)] duration-300"
     x-transition:enter-start="opacity-0 -translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @mouseenter="clearTimeout(closeTimer)"
     @mouseleave="scheduleClose()"
     class="absolute inset-x-0 top-full hidden border-b border-sand-300 bg-sand-50 shadow-float lg:block"
     style="display: none">

    {{-- پنل محصولات --}}
    <div x-show="openMenu === 'products.index'" class="container-page py-10">
        <div class="grid grid-cols-12 gap-10">
            <div class="col-span-3">
                <p class="eyebrow text-clay-600">Product System</p>
                <h2 class="mt-3 text-2xl font-extrabold leading-snug">سیستم محصولات سفالی</h2>
                <p class="mt-3 text-[0.9375rem] leading-relaxed text-ink-400">
                    از تیغه‌ی ۷ سانتی تا بلوک عایق ۳۰ — یک خانواده‌ی ماژولار که ابعادش با هم هماهنگ است.
                </p>
                <a href="{{ route('finder.show') }}"
                   class="mt-6 inline-flex items-center gap-2 rounded-full bg-clay-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-clay-600">
                    <x-icon name="compass" size="17" />
                    انتخاب محصول مناسب پروژه
                </a>
            </div>

            @foreach($megaMenu as $group)
                <div class="col-span-3">
                    <p class="mb-4 border-b border-sand-300 pb-3 text-sm font-bold text-ink-900">
                        {{ $group->name }}
                        <span class="tech mr-2 text-micro font-normal uppercase tracking-widest text-ink-300">{{ $group->name_en }}</span>
                    </p>
                    <ul class="space-y-0.5">
                        @foreach($group->children as $child)
                            <li>
                                <a href="{{ route('products.index', ['category' => $child->slug]) }}"
                                   class="group flex items-baseline justify-between gap-3 rounded-lg px-3 py-2.5 transition hover:bg-sand-200">
                                    <span>
                                        <span class="block text-[0.9375rem] font-semibold text-ink-800 group-hover:text-clay-600">{{ $child->name }}</span>
                                        <span class="block text-xs text-ink-400">{{ $child->tagline }}</span>
                                    </span>
                                    <span class="tech shrink-0 text-xs text-ink-300">{{ \App\Support\Jalali::digits($child->products_count) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            <div class="col-span-3">
                <div class="rounded-[var(--radius-panel)] bg-ink-900 p-6 text-sand-100">
                    <p class="eyebrow text-clay-300">Featured</p>
                    <h3 class="mt-2 text-xl font-bold">بلوک عایق ۲۵</h3>
                    <p class="mt-2 text-sm leading-relaxed text-sand-200/70">
                        دیوار خارجی تک‌لایه بدون عایق افزوده. ضریب λ برابر ۰٫۲۱.
                    </p>
                    <a href="{{ route('products.show', 'insulating-block-25') }}"
                       class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-clay-300 transition hover:text-clay-200">
                        مشاهده محصول
                        <x-icon name="arrow-left" size="15" />
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-between border-t border-sand-300 pt-5">
            <a href="{{ route('products.index') }}" class="flex items-center gap-2 text-sm font-bold text-clay-600 hover:text-clay-700">
                مشاهده همه محصولات
                <x-icon name="arrow-left" size="16" />
            </a>
            <a href="{{ route('technical.downloads') }}" class="flex items-center gap-2 text-sm text-ink-400 hover:text-ink-900">
                <x-icon name="download" size="16" />
                دانلود دیتاشیت و فایل‌های فنی
            </a>
        </div>
    </div>

    {{-- پنل‌های ساده --}}
    @foreach($navigation as $item)
        @continue(empty($item['children']))
        <div x-show="openMenu === '{{ $item['route'] }}'" class="container-page py-8">
            <div class="grid grid-cols-12 gap-8">
                <div class="col-span-3">
                    <h2 class="text-xl font-extrabold">{{ $item['label'] }}</h2>
                </div>
                <ul class="col-span-9 grid grid-cols-3 gap-2">
                    @foreach($item['children'] as $child)
                        <li>
                            <a href="{{ route($child['route']) }}"
                               class="block rounded-lg px-4 py-3 text-[0.9375rem] font-semibold text-ink-700 transition hover:bg-sand-200 hover:text-clay-600">
                                {{ $child['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endforeach
</div>
