@props(['name', 'size' => 20])

@php
    // مجموعه‌ی آیکون درون‌خطی — بدون کتابخانه‌ی بیرونی و بدون درخواست اضافه.
    $paths = [
        'arrow-left'   => '<path d="M19 12H5m0 0 6-6m-6 6 6 6"/>',
        'arrow-right'  => '<path d="M5 12h14m0 0-6-6m6 6-6 6"/>',
        'arrow-down'   => '<path d="M12 5v14m0 0 6-6m-6 6-6-6"/>',
        'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
        'chevron-left' => '<path d="m15 18-6-6 6-6"/>',
        'close'        => '<path d="M18 6 6 18M6 6l12 12"/>',
        'menu'         => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'search'       => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'phone'        => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a1 1 0 0 1-1 1A16 16 0 0 1 3 5a1 1 0 0 1 1-1Z"/>',
        'mail'         => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'pin'          => '<path d="M12 21s7-5.5 7-11a7 7 0 1 0-14 0c0 5.5 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/>',
        'download'     => '<path d="M12 3v12m0 0 4-4m-4 4-4-4"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/>',
        'thermal'      => '<path d="M14 14.8V5a2 2 0 1 0-4 0v9.8a4 4 0 1 0 4 0Z"/><path d="M12 9v6"/>',
        'acoustic'     => '<path d="M4 10v4h3l5 4V6L7 10H4Z"/><path d="M16.5 8.5a5 5 0 0 1 0 7M19.5 5.5a9 9 0 0 1 0 13"/>',
        'fire'         => '<path d="M12 3s5 4.5 5 9a5 5 0 0 1-10 0c0-1.6.8-3 1.6-4 .3 1 1 1.8 1.9 1.8C12 9.8 12 6 12 3Z"/>',
        'shield'       => '<path d="M12 3 5 6v6c0 4.4 3 8.2 7 9 4-.8 7-4.6 7-9V6l-7-3Z"/>',
        'leaf'         => '<path d="M4 20C3 12 8 5 20 4c0 12-6 16-12 16-2 0-4-1-4-1Z"/><path d="M9 15c2-4 5-6 9-7"/>',
        'weight'       => '<path d="M7 8h10l2 12H5L7 8Z"/><circle cx="12" cy="5" r="2.5"/>',
        'ruler'        => '<rect x="3" y="8" width="18" height="8" rx="1.5"/><path d="M7 8v3M11 8v4M15 8v3M19 8v4"/>',
        'layers'       => '<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 13 9 5 9-5"/>',
        'compass'      => '<circle cx="12" cy="12" r="9"/><path d="m15 9-2 5-5 2 2-5 5-2Z"/>',
        'blueprint'    => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M9 9v11M14 9v6h7"/>',
        'trowel'       => '<path d="M13 3 8 12h9l-4-9Z"/><path d="M12.5 12v5M10 21h5"/>',
        'check'        => '<path d="m5 13 4 4L19 7"/>',
        'plus'         => '<path d="M12 5v14M5 12h14"/>',
        'minus'        => '<path d="M5 12h14"/>',
        'play'         => '<path d="M8 5.5v13l11-6.5-11-6.5Z"/>',
        'factory'      => '<path d="M3 21V10l6 4V10l6 4V7l6 3v11H3Z"/><path d="M7 21v-4M12 21v-4M17 21v-4"/>',
        'sparkle'      => '<path d="m12 3 2 6 6 2-6 2-2 6-2-6-6-2 6-2 2-6Z"/>',
        'file'         => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z"/><path d="M14 3v5h5"/>',
        'grid'         => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'clock'        => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'external'     => '<path d="M14 4h6v6"/><path d="M20 4 10 14"/><path d="M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'shrink-0', 'aria-hidden' => 'true', 'focusable' => 'false']) }}
     width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24"
     fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
    {!! $paths[$name] ?? $paths['grid'] !!}
</svg>
