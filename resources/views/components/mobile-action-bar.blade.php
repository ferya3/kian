@props([
    'primaryHref',
    'primaryLabel' => 'استعلام قیمت',
    'secondaryHref' => null,
    'secondaryLabel' => null,
    'secondaryIcon' => 'download',
])

{{--
    نوار اقدام چسبان موبایل.

    روی موبایل، کاربر بعد از چند صفحه اسکرول نباید برای تماس به بالای صفحه برگردد.
    این نوار فقط روی صفحات با قصد خرید بالا (محصول، راهکار، پروژه) نمایش داده
    می‌شود، نه در کل سایت — تا دائماً ارتفاع مفید صفحه را نخورد.
--}}

<div data-mobile-action-bar
     class="no-print fixed inset-x-0 bottom-0 z-40 border-t border-sand-300 bg-sand-50/95 backdrop-blur-lg lg:hidden"
     style="padding-bottom: var(--safe-bottom)">
    <div class="container-page flex items-center gap-2 py-3">
        <a href="tel:{{ config('kian.contact.phone_raw') }}"
           class="tap-icon shrink-0 rounded-xl border border-sand-300 bg-sand-100 text-ink-700 transition active:bg-sand-200"
           aria-label="تماس تلفنی با واحد فروش">
            <x-icon name="phone" size="20" />
        </a>

        @if($secondaryHref)
            <a href="{{ $secondaryHref }}"
               class="tap flex-1 justify-center gap-2 rounded-xl border border-sand-300 bg-sand-100 px-3 text-[0.9375rem] font-semibold text-ink-800 transition active:bg-sand-200">
                <x-icon :name="$secondaryIcon" size="18" />
                {{ $secondaryLabel }}
            </a>
        @endif

        <a href="{{ $primaryHref }}"
           class="tap flex-[1.4] justify-center rounded-xl bg-clay-500 px-3 text-[0.9375rem] font-semibold text-white transition active:bg-clay-600">
            {{ $primaryLabel }}
        </a>
    </div>
</div>
