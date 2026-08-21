@php
    use App\Models\Setting;
    $video = Setting::text('hero_video');
@endphp

<section class="relative flex min-h-[100svh] flex-col justify-end overflow-hidden bg-ink-950 pb-8 pt-28 text-sand-50 lg:pb-12">

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
            <p class="eyebrow inline-flex items-center gap-2.5 rounded-full border border-white/12 bg-white/[0.06] px-4 py-2 text-clay-300 backdrop-blur-sm"
               data-reveal>
                <span class="relative flex h-1.5 w-1.5">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-ember-500 opacity-75"></span>
                    <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-ember-500"></span>
                </span>
                {{ Setting::text('hero_eyebrow', 'کارخانه سفال کیان') }}
            </p>

            <h1 class="mt-7 text-display font-extrabold text-balance text-sand-50" data-reveal style="--reveal-delay: 90ms">
                {{ Setting::text('hero_title', config('kian.brand.tagline')) }}
            </h1>

            <p class="mt-6 max-w-2xl text-lead text-sand-200/75" data-reveal style="--reveal-delay: 180ms">
                {{ Setting::text('hero_subtitle', config('kian.seo.default_description')) }}
            </p>

            <div class="mt-10 flex flex-wrap items-center gap-3" data-reveal style="--reveal-delay: 270ms">
                <x-cta :href="route('products.index')" variant="primary" size="lg">مشاهده محصولات</x-cta>
                <x-cta :href="route('factory')" variant="light" size="lg" icon="play">آشنایی با کارخانه</x-cta>
            </div>
        </div>

        {{-- نوار شاخص‌ها — لنگر بصری پایین قهرمان --}}
        <dl class="mt-10 grid grid-cols-2 gap-px overflow-hidden rounded-[var(--radius-panel)] border border-white/10 bg-white/[0.07] backdrop-blur-md lg:mt-14 lg:grid-cols-4"
            data-reveal data-reveal-stagger="80" style="--reveal-delay: 360ms">
            @foreach($stats as $stat)
                <div class="bg-ink-950/45 px-5 py-5 lg:px-7 lg:py-6">
                    <dd class="text-2xl font-extrabold text-sand-50 lg:text-3xl">
                        <bdi dir="ltr" class="tech inline-block whitespace-nowrap">
                            <span data-countup="{{ $stat->value }}" data-decimals="{{ $stat->decimals }}"
                                  @if($stat->value >= 1000) data-separated @endif>۰</span><span class="text-clay-400">{{ $stat->suffix }}</span>
                        </bdi>
                    </dd>
                    <dt class="mt-1 text-meta text-sand-200/60">{{ $stat->label }}</dt>
                </div>
            @endforeach
        </dl>
    </div>

</section>
