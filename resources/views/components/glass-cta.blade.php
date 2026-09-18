@props([
    'href' => null,
    'icon' => 'arrow-up-left',
    'tone' => 'neutral',   // neutral | warm
])

{{--
    دکمه‌ی شیشه‌ی دودی هیرو.

    از x-cta جدا شده و جایش را نگرفته: این ظاهر فقط روی زمینه‌ی تیره‌ی هیرو
    معنی دارد — backdrop-blur روی پس‌زمینه‌ی روشن، شیشه نمی‌سازد، مه می‌سازد.
    x-cta هنوز در حدود سی جای دیگر سایت کار می‌کند و دست‌نخورده است.

    کلاهک گرد در *انتهای* دکمه می‌نشیند، یعنی سمت چپ؛ جهت رو به جلو در متن
    فارسی چپ است، پس فلش هم به بالا-چپ اشاره می‌کند نه بالا-راست.

    روی گوشی کلاهک پنهان می‌شود: دو دکمه‌ی کنار هم در ۳۶۰ پیکسل جا ندارند و
    همین الگو برای آیکون‌های هیرو هم به‌کار رفته است.
--}}

@php
    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @else type="{{ $attributes->get('type', 'button') }}" @endif
    {{ $attributes->merge([
        'class' => 'glass-btn group inline-flex min-h-11 items-center justify-center gap-2.5 rounded-full py-1.5 ps-5 pe-1.5 '
            .'font-semibold text-sand-50 focus-visible:outline-2 focus-visible:outline-offset-3 '
            .($tone === 'warm' ? 'glass-btn-warm' : ''),
    ]) }}>

    <span class="relative z-[1] whitespace-nowrap">{{ $slot }}</span>

    @if($icon)
        <span class="glass-cap relative z-[1] hidden h-9 w-9 shrink-0 place-items-center rounded-full text-sand-50/90 transition-colors duration-300 group-hover:text-white sm:grid"
              aria-hidden="true">
            <x-icon :name="$icon" size="15"
                    class="transition-transform duration-300 ease-[var(--ease-out-expo)] group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" />
        </span>
    @endif
</{{ $tag }}>
