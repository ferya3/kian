<x-layouts.app>
    <x-page-hero
        eyebrow="Solutions"
        title="راهکارها"
        lead="محصول یک قطعه است؛ راهکار یعنی ترکیب درست قطعه‌ها برای حل مسئله‌ی مشخص پروژه‌ی شما." />

    <section class="bg-sand-100 pb-20">
        <div class="container-page space-y-6">
            @foreach($solutions as $solution)
                <article data-reveal
                         class="group grid gap-8 rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-6 transition hover:border-clay-300 lg:grid-cols-12 lg:p-8">
                    <div class="lg:col-span-5">
                        <p class="tech text-[0.6875rem] uppercase tracking-[0.14em] text-ink-300">{{ $solution->title_en }}</p>
                        <h2 class="mt-2 text-h3 font-extrabold">
                            <a href="{{ route('solutions.show', $solution) }}" class="transition hover:text-clay-600">{{ $solution->title }}</a>
                        </h2>
                        <p class="mt-1.5 font-semibold text-clay-600">{{ $solution->subtitle }}</p>
                        <p class="mt-4 leading-relaxed text-ink-500">{{ $solution->summary }}</p>
                        <x-cta :href="route('solutions.show', $solution)" variant="ghost" size="sm" class="mt-6">جزئیات راهکار</x-cta>
                    </div>

                    <div class="lg:col-span-4">
                        <h3 class="text-[0.8125rem] font-bold uppercase tracking-wider text-ink-400">مزایا</h3>
                        <ul class="mt-3 space-y-2">
                            @foreach($solution->benefits ?? [] as $benefit)
                                <li class="flex gap-2.5 text-[0.9375rem] leading-relaxed text-ink-600">
                                    <x-icon name="check" size="16" class="mt-1 shrink-0 text-clay-500" />
                                    {{ $benefit }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="lg:col-span-3">
                        <h3 class="text-[0.8125rem] font-bold uppercase tracking-wider text-ink-400">محصولات</h3>
                        <ul class="mt-3 space-y-1.5">
                            @foreach($solution->products as $product)
                                <li>
                                    <a href="{{ route('products.show', $product) }}"
                                       class="flex items-center justify-between gap-2 rounded-lg bg-sand-100 px-3.5 py-2.5 text-[0.875rem] font-semibold transition hover:bg-clay-50 hover:text-clay-700">
                                        {{ $product->name }}
                                        <x-icon name="chevron-left" size="15" class="text-ink-300" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</x-layouts.app>
