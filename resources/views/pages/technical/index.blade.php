<x-layouts.app>
    <x-page-hero
        eyebrow="Technical center"
        title="مرکز فنی"
        lead="دیتاشیت، کاتالوگ، فایل CAD، آبجکت BIM، راهنمای اجرا و گواهی‌نامه — بدون ثبت‌نام و بدون تماس با واحد فروش."
        variant="dark">

        <div class="mt-12 grid grid-cols-1 gap-4 sm:grid-cols-3">
            @foreach(config('kian.audiences') as $key => $audience)
                <a href="{{ route($audience['route']) }}"
                   class="group rounded-2xl border border-white/10 bg-white/[0.05] p-6 transition hover:border-clay-500/40 hover:bg-clay-500/10">
                    <x-icon :name="$audience['icon']" size="24" class="text-clay-400" />
                    <p class="mt-4 font-bold">{{ $audience['label'] }}</p>
                    <p class="mt-1.5 text-[0.9375rem] text-sand-200/60">«{{ $audience['question'] }}»</p>
                    <span class="mt-4 inline-flex items-center gap-1.5 text-[0.875rem] font-semibold text-clay-400">
                        {{ $audience['cta'] }}
                        <x-icon name="arrow-left" size="15" class="transition-transform group-hover:-translate-x-1" />
                    </span>
                </a>
            @endforeach
        </div>
    </x-page-hero>

    <section class="bg-sand-100 py-16 lg:py-20">
        <div class="container-page space-y-12">
            @foreach($labels as $category => $label)
                @php $documents = $groups[$category] ?? collect(); @endphp
                @continue($documents->isEmpty())

                <div>
                    <div class="flex flex-wrap items-end justify-between gap-3 border-b border-sand-300 pb-4">
                        <h2 class="text-h3 font-extrabold">{{ $label }}</h2>
                        <p class="tech text-[0.8125rem] text-ink-400">{{ \App\Support\Jalali::digits($documents->count()) }} فایل</p>
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3" data-reveal-stagger="60">
                        @foreach($documents->take(6) as $document)
                            <x-document-row :document="$document" data-reveal />
                        @endforeach
                    </div>

                    @if($documents->count() > 6)
                        <x-cta :href="route('technical.downloads', ['category' => $category])" variant="plain" class="mt-4">
                            مشاهده همه‌ی {{ \App\Support\Jalali::digits($documents->count()) }} فایل {{ $label }}
                        </x-cta>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-sand-50 py-16 lg:py-20">
        <div class="container-page">
            <x-section-heading eyebrow="Per product" title="فایل‌ها به تفکیک محصول"
                lead="اگر می‌دانید کدام محصول را می‌خواهید، از اینجا سریع‌تر است." />

            <ul class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3" data-reveal-stagger="60">
                @foreach($products as $product)
                    <li data-reveal>
                        <a href="{{ route('products.show', $product) }}#downloads"
                           class="group flex items-center gap-4 rounded-xl border border-sand-300 bg-sand-100 p-4 transition hover:border-clay-300 hover:bg-clay-50">
                            <span class="w-16 shrink-0">
                                <x-block-3d :product="$product" :size="58" :interactive="false" />
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block font-bold group-hover:text-clay-700">{{ $product->name }}</span>
                                <span class="tech block text-[0.75rem] text-ink-400">PDF · DWG · RVT · IFC</span>
                            </span>
                            <x-icon name="download" size="18" class="shrink-0 text-ink-300 transition group-hover:text-clay-500" />
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
</x-layouts.app>
