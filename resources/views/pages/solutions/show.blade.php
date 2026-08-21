<x-layouts.app>
    <x-page-hero
        :eyebrow="$solution->title_en"
        :title="$solution->title"
        :lead="$solution->summary"
        variant="dark" />

    <section class="bg-sand-50 py-16 lg:py-20">
        <div class="container-page grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-14">
            <div class="lg:col-span-7">
                <h2 class="text-h3 font-extrabold">{{ $solution->subtitle }}</h2>
                <div class="mt-5 space-y-4 text-lead text-ink-600">
                    @foreach(preg_split('/\n+/', $solution->description ?? $solution->summary) as $paragraph)
                        @if(trim($paragraph))
                            <p>{{ $paragraph }}</p>
                        @endif
                    @endforeach
                </div>

                <h3 class="mt-10 text-h3 font-extrabold">مزایای این راهکار</h3>
                <ul class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2" data-reveal-stagger="80">
                    @foreach($solution->benefits ?? [] as $benefit)
                        <li data-reveal class="flex gap-3 rounded-xl border border-sand-300 bg-sand-100 p-4">
                            <x-icon name="check" size="18" class="mt-0.5 shrink-0 text-clay-500" />
                            <span class="leading-relaxed text-ink-600">{{ $benefit }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <aside class="lg:col-span-5">
                <div class="sticky top-28 rounded-[var(--radius-panel)] border border-sand-300 bg-sand-100 p-6">
                    <h2 class="font-bold">محصولات پیشنهادی این راهکار</h2>
                    <ul class="mt-4 space-y-3">
                        @foreach($solution->products as $product)
                            <li>
                                <a href="{{ route('products.show', $product) }}"
                                   class="group flex items-center gap-4 rounded-xl border border-sand-200 bg-sand-50 p-3 transition hover:border-clay-300">
                                    <span class="w-20 shrink-0">
                                        <x-block-3d :product="$product" :size="72" :interactive="false" />
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block font-bold group-hover:text-clay-700">{{ $product->name }}</span>
                                        <span class="tech block text-micro text-ink-400">
                                            λ {{ \App\Support\Jalali::digits($product->thermal_conductivity) }} · {{ \App\Support\Jalali::digits($product->weight_kg) }} kg
                                        </span>
                                    </span>
                                    <x-icon name="chevron-left" size="16" class="shrink-0 text-ink-300 transition group-hover:text-clay-500" />
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <x-cta :href="route('contact', ['type' => 'technical'])" variant="primary" size="sm" class="mt-6 w-full">
                        مشاوره برای این راهکار
                    </x-cta>
                </div>
            </aside>
        </div>
    </section>
    <x-mobile-action-bar
        :primary-href="route('contact', ['type' => 'technical'])"
        primary-label="مشاوره فنی"
        :secondary-href="route('products.index')"
        secondary-label="محصولات"
        secondary-icon="grid" />
</x-layouts.app>
