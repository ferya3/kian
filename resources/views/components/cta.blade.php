@props([
    'href' => null,
    'variant' => 'primary',   // primary | dark | ghost | light
    'icon' => 'arrow-left',
    'size' => 'md',
])

@php
    $base = 'group inline-flex items-center justify-center gap-2 rounded-full font-semibold transition-all duration-300 ease-[var(--ease-out-expo)] focus-visible:outline-2 focus-visible:outline-offset-3';

    $variants = [
        'primary' => 'bg-clay-500 text-white hover:bg-clay-600 hover:shadow-lift',
        'dark'    => 'bg-ink-900 text-sand-50 hover:bg-ink-700 hover:shadow-lift',
        'ghost'   => 'border border-ink-900/15 text-ink-900 hover:border-ink-900/40 hover:bg-sand-200/60',
        'light'   => 'border border-white/20 text-sand-50 hover:border-white/50 hover:bg-white/5',
        'plain'   => 'text-clay-600 hover:text-clay-700 px-0',
    ];

    // ارتفاع حداقلی ۴۴ پیکسل روی موبایل — استاندارد هدف لمسی؛ روی دسکتاپ فشرده‌تر
    $sizes = [
        'sm' => 'min-h-11 px-4 py-2 text-meta lg:min-h-0',
        'md' => 'min-h-11 px-6 py-3 text-[0.9375rem]',
        'lg' => 'min-h-12 px-8 py-4 text-base',
    ];

    $classes = $base.' '.($variants[$variant] ?? $variants['primary']).' '.($variant === 'plain' ? '' : ($sizes[$size] ?? $sizes['md']));
    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @else type="{{ $attributes->get('type', 'button') }}" @endif
    {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
    @if($icon)
        <x-icon :name="$icon" size="17"
                class="transition-transform duration-300 ease-[var(--ease-out-expo)] group-hover:-translate-x-1" />
    @endif
</{{ $tag }}>
