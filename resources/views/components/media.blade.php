@props([
    'path' => null,
    'alt' => '',
    'eager' => false,
    'imgClass' => 'h-full w-full object-cover',
])

{{--
    تصویر آپلودشده را نشان می‌دهد؛ اگر چیزی آپلود نشده باشد، محتوای slot
    (آرت وکتوری) رندر می‌شود. سایت با دیتابیس خالی هم کامل دیده می‌شود.
--}}
@php $url = \App\Support\Media::url($path); @endphp

@if($url)
    <img src="{{ $url }}" alt="{{ $alt }}"
         loading="{{ $eager ? 'eager' : 'lazy' }}"
         decoding="async"
         @if($eager) fetchpriority="high" @endif
         {{ $attributes->merge(['class' => $imgClass]) }}>
@else
    {{ $slot }}
@endif
