@php
    use App\Models\Setting;
    $video = Setting::text('hero_video');
@endphp

{{--
    روی گوشی هیرو مربع است: ارتفاعش برابر عرض صفحه، نه یک بلوک کشیده‌ی
    تمام‌قد. min-h به‌جای aspect استفاده شده تا اگر متن بلندتر شد، به‌جای
    بریده‌شدن، کادر کمی رشد کند.
    از lg به بالا به ارتفاع سینمایی تمام‌صفحه برمی‌گردد.
--}}
<section class="relative flex min-h-[100vw] flex-col justify-center overflow-hidden bg-ink-950 pb-6 pt-[4.5rem] text-sand-50 sm:min-h-[min(100vw,36rem)] sm:pb-12 sm:pt-24 lg:min-h-[88svh] lg:pb-16 lg:pt-32">

    @if($video)
        {{-- ویدئوی سینمایی کارخانه: نمای نزدیک خاک → کوره → خروج محصول → ساختمان --}}
        <video class="absolute inset-0 h-full w-full object-cover opacity-60"
               autoplay muted loop playsinline preload="metadata"
               poster="/media/hero-poster.jpg" aria-hidden="true">
            <source src="{{ $video }}" type="video/mp4">
        </video>
        <div class="absolute inset-0 bg-gradient-to-t from-ink-950 via-ink-950/70 to-ink-950/40"></div>
    @else
        <x-hero-scene />
    @endif

    <div class="container-page relative">
        <div class="max-w-4xl">
            <p class="eyebrow inline-flex items-center gap-2 rounded-full border border-white/12 bg-white/[0.06] px-3 py-1.5 text-clay-300 backdrop-blur-sm sm:gap-2.5 sm:px-4 sm:py-2"
               data-reveal>
                <span class="relative flex h-1.5 w-1.5">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-ember-500 opacity-75"></span>
                    <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-ember-500"></span>
                </span>
                {{ Setting::text('hero_eyebrow', 'کارخانه سفال کیان') }}
            </p>

            <h1 class="mt-3 text-display sm:mt-7 font-extrabold text-balance text-sand-50" data-reveal style="--reveal-delay: 90ms">
                {{ Setting::text('hero_title', config('kian.brand.tagline')) }}
            </h1>

            <p class="mt-2.5 line-clamp-2 max-w-2xl text-lead text-sand-200/75 sm:mt-6 sm:line-clamp-none" data-reveal style="--reveal-delay: 180ms">
                {{ Setting::text('hero_subtitle', config('kian.seo.default_description')) }}
            </p>

            {{-- دو دکمه کنار هم روی گوشی: متن تک‌خطی، padding کم، ارتفاع ۴۴ --}}
            <div class="mt-4 flex items-center gap-2.5 sm:mt-10 sm:gap-3" data-reveal style="--reveal-delay: 270ms">
                {{-- آیکون روی گوشی پنهان می‌شود: در ۳۶۰ پیکسل، برچسب مهم‌تر از فلش است --}}
                <x-cta :href="route('products.index')" variant="primary" size="lg"
                       class="min-w-0 flex-1 justify-center whitespace-nowrap px-2 text-meta [&_svg]:hidden sm:flex-none sm:gap-2 sm:px-5 sm:text-[0.9375rem] sm:[&_svg]:block">مشاهده محصولات</x-cta>
                <x-cta :href="route('factory')" variant="light" size="lg" icon="play"
                       class="min-w-0 flex-1 justify-center whitespace-nowrap px-2 text-meta [&_svg]:hidden sm:flex-none sm:gap-2 sm:px-5 sm:text-[0.9375rem] sm:[&_svg]:block">آشنایی با کارخانه</x-cta>
            </div>
        </div>

    </div>

</section>
