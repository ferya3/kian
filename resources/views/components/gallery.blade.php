@props(['images' => [], 'title' => 'تصاویر', 'eyebrow' => 'Gallery'])

@php $urls = \App\Support\Media::gallery($images); @endphp

{{--
    گالری بدون جاوااسکریپت: هر تصویر به فایل اصلی لینک می‌شود.
    lightbox عمداً ساخته نشده — یک تب تازه همان کار را بدون تله‌ی فوکوس،
    بدون قفل اسکرول و بدون رفتار متفاوت روی موبایل انجام می‌دهد.
--}}
@if($urls)
    <section {{ $attributes->merge(['class' => 'bg-sand-100 py-16 lg:py-20']) }}>
        <div class="container-page">
            <x-section-heading :eyebrow="$eyebrow" :title="$title" />

            <ul class="mt-10 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 lg:gap-4"
                data-reveal-stagger="70">
                @foreach($urls as $url)
                    <li data-reveal class="overflow-hidden rounded-2xl border border-sand-300 bg-sand-200">
                        <a href="{{ $url }}" target="_blank" rel="noopener"
                           class="group block aspect-[4/3] focus:outline-none focus-visible:ring-2 focus-visible:ring-clay-500">
                            <img src="{{ $url }}" alt="" loading="lazy" decoding="async"
                                 class="h-full w-full object-cover transition-transform duration-700 ease-[var(--ease-out-expo)] group-hover:scale-105">
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
