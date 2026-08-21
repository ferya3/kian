<section class="bg-sand-50 py-20 lg:py-24" aria-labelledby="solutions-heading">
    <div class="container-page">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-section-heading
                eyebrow="Solutions"
                title="راهکار، نه فقط محصول"
                lead="مسئله‌ی پروژه را بگویید تا ترکیب درست محصولات را پیشنهاد بدهیم."
                id="solutions-heading" class="lg:max-w-2xl" />
            <div data-reveal>
                <x-cta :href="route('solutions.index')" variant="ghost">همه راهکارها</x-cta>
            </div>
        </div>

        <ul class="mt-10 grid gap-4 md:grid-cols-2 xl:grid-cols-4" data-reveal-stagger="90">
            @foreach($solutions as $solution)
                <li data-reveal>
                    <a href="{{ route('solutions.show', $solution) }}"
                       class="group flex h-full flex-col rounded-[var(--radius-panel)] border border-sand-300 bg-sand-100 p-6 transition-all duration-500 ease-[var(--ease-out-expo)] hover:-translate-y-1 hover:border-clay-300 hover:bg-clay-50">
                        <p class="tech text-[0.6875rem] uppercase tracking-[0.14em] text-ink-300">{{ $solution->title_en }}</p>
                        <h3 class="mt-2 text-xl font-extrabold leading-snug">{{ $solution->title }}</h3>
                        <p class="mt-1 text-[0.8125rem] font-semibold text-clay-600">{{ $solution->subtitle }}</p>
                        <p class="mt-3 flex-1 text-[0.9375rem] leading-relaxed text-ink-500">{{ $solution->summary }}</p>

                        <span class="mt-5 inline-flex items-center gap-1.5 text-[0.875rem] font-semibold text-ink-700 transition group-hover:text-clay-600">
                            مشاهده راهکار
                            <x-icon name="arrow-left" size="15" class="transition-transform duration-300 group-hover:-translate-x-1" />
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</section>
