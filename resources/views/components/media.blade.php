@props([
    'path' => null,
    'alt' => '',
    'eager' => false,
    'imgClass' => 'h-full w-full object-cover',
    'sizes' => '(min-width: 1024px) 50vw, 100vw',
])

{{--
    تصویر آپلودشده را نشان می‌دهد؛ اگر چیزی آپلود نشده باشد، محتوای slot
    (آرت وکتوری) رندر می‌شود. سایت با دیتابیس خالی هم کامل دیده می‌شود.
--}}
@php $url = \App\Support\Media::url($path); @endphp

@if($url)
    <img src="{{ $url }}" alt="{{ $alt }}"
         @if($srcset = \App\Support\Media::srcset($url)) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
         loading="{{ $eager ? 'eager' : 'lazy' }}"
         decoding="async"
         @if($eager) fetchpriority="high" @endif
         {{ $attributes->merge(['class' => $imgClass]) }}>
@else
    {{ $slot }}
@endif
