<section class="bg-sand-50 section-lg" aria-labelledby="solutions-heading">
    <div class="container-page">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-section-heading
                eyebrow="Solutions"
                :title="__('site.home.solutions.title')"
                :lead="__('site.home.solutions.lead')"
                id="solutions-heading" class="lg:max-w-2xl" />
            <div data-reveal>
                <x-cta :href="route('solutions.index')" variant="ghost">{{ __('site.home.solutions.all') }}</x-cta>
            </div>
        </div>

        {{--
            یک فهرست، دو چیدمان — همان الگوی بخش محصولات.

            زیر md ریلِ افقیِ قابل‌کشیدن است و از md به بالا با همان مارک‌آپ به
            شبکه برمی‌گردد. پیش‌تر زیر md یک ستونِ چهارتایی بود که چهار کارتِ
            بلند پشت سر هم می‌شد و بخش‌های بعدی را دور می‌کرد.

            مارک‌آپ یکی است و نه دو نسخه‌ی موازی: دو نسخه از هم عقب می‌مانند.
        --}}
        <ul class="scroll-rail mt-10 md:mx-0 md:grid md:snap-none md:grid-cols-2 md:gap-4 md:overflow-visible md:px-0 xl:grid-cols-4"
            data-reveal-stagger="90"
            aria-label="{{ __('site.home.solutions.list') }}">
            @foreach($solutions as $solution)
                <li data-reveal class="w-[78vw] max-w-xs md:w-auto md:max-w-none">
                    <a href="{{ route('solutions.show', $solution) }}"
                       class="group flex h-full flex-col rounded-[var(--radius-panel)] border border-sand-300 bg-sand-100 p-6 transition-all duration-500 ease-[var(--ease-out-expo)] hover:-translate-y-1 hover:border-clay-300 hover:bg-clay-50">
                        <p class="tech text-micro uppercase tracking-[0.14em] text-ink-300">{{ $solution->title_en }}</p>
                        <h3 class="mt-2 text-card font-extrabold">{{ $solution->title }}</h3>
                        <p class="mt-1 text-meta font-semibold text-clay-600">{{ $solution->subtitle }}</p>
                        <p class="mt-3 flex-1 text-[0.9375rem] leading-relaxed text-ink-500">{{ $solution->summary }}</p>

                        <span class="mt-5 inline-flex items-center gap-1.5 text-[0.875rem] font-semibold text-ink-700 transition group-hover:text-clay-600">
                            {{ __('site.home.solutions.view') }}
                            <x-icon name="arrow-left" size="15" class="transition-transform duration-300 group-hover:-translate-x-1" />
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- راهنمای کشیدن — فقط آنجا که ریل هست --}}
        <p class="mt-4 flex items-center gap-2 text-meta text-ink-400 md:hidden">
            <x-icon name="arrow-right" size="15" />
            {{ __('site.home.swipe') }}
        </p>
    </div>
</section>
