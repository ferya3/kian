<x-layouts.app>
    <x-page-hero
        eyebrow="Product system"
        title="محصولات"
        lead="یک خانواده‌ی ماژولار از بلوک سفالی: تیغه‌ای، دیواری، عایق، سبک، سقفی و متعلقات. ابعاد همه با هم می‌خوانند تا رگ‌چینی بدون برش اضافه پیش برود.">
    </x-page-hero>

    <section class="bg-sand-100 pb-20">
        <div class="container-page">
            {{-- فیلترها --}}
            <div class="sticky top-16 z-30 -mx-5 border-b border-sand-300 bg-sand-100/95 px-5 py-4 backdrop-blur-md lg:top-[4.5rem] lg:-mx-12 lg:px-12">
                <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="ml-1 text-[0.8125rem] font-semibold text-ink-400">دسته:</span>
                        <a href="{{ route('products.index') }}"
                           class="rounded-full px-3.5 py-1.5 text-[0.875rem] font-semibold transition {{ request('category') ? 'bg-sand-200 text-ink-600 hover:bg-sand-300' : 'bg-ink-900 text-sand-50' }}">همه</a>

                        @foreach($categories as $group)
                            @foreach($group->children as $child)
                                <a href="{{ route('products.index', ['category' => $child->slug]) }}"
                                   class="rounded-full px-3.5 py-1.5 text-[0.875rem] font-semibold transition {{ request('category') === $child->slug ? 'bg-ink-900 text-sand-50' : 'bg-sand-200 text-ink-600 hover:bg-sand-300' }}">
                                    {{ $child->name }}
                                </a>
                            @endforeach
                        @endforeach
                    </div>

                    <p class="tech mr-auto text-[0.8125rem] text-ink-400">
                        {{ \App\Support\Jalali::digits($products->count()) }} محصول
                    </p>

                    <a href="{{ route('finder.show') }}"
                       class="flex items-center gap-2 rounded-full bg-clay-500 px-4 py-2 text-[0.875rem] font-semibold text-white transition hover:bg-clay-600">
                        <x-icon name="compass" size="16" />
                        نمی‌دانید کدام؟
                    </a>
                </div>
            </div>

            @if($products->isEmpty())
                <div class="mt-16 rounded-[var(--radius-panel)] border border-dashed border-sand-300 py-20 text-center">
                    <p class="text-lg font-bold">محصولی با این فیلتر پیدا نشد</p>
                    <p class="mt-2 text-ink-400">فیلتر را بردارید یا از موتور انتخاب محصول استفاده کنید.</p>
                    <x-cta :href="route('products.index')" variant="ghost" class="mt-6">همه محصولات</x-cta>
                </div>
            @else
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" data-reveal-stagger="80">
                    @foreach($products as $product)
                        <x-product-card :product="$product" data-reveal />
                    @endforeach
                </div>
            @endif

            {{-- جدول مقایسه‌ای — چیزی که مهندس واقعاً می‌خواهد --}}
            <div class="mt-16">
                <h2 class="text-h3 font-extrabold" data-reveal>جدول مقایسه‌ای</h2>
                <p class="mt-2 text-ink-500" data-reveal>همه‌ی اعداد در یک نگاه. برای دیدن ستون‌های بیشتر، جدول را افقی بکشید.</p>

                <div class="mt-6 overflow-x-auto rounded-[var(--radius-panel)] border border-sand-300" data-reveal>
                    <table class="w-full min-w-[56rem] text-right">
                        <caption class="sr-only">مقایسه مشخصات فنی محصولات</caption>
                        <thead class="bg-sand-200/80">
                            <tr>
                                <th scope="col" class="px-4 py-3.5 text-[0.8125rem] font-bold text-ink-600">محصول</th>
                                <th scope="col" class="px-4 py-3.5 text-[0.8125rem] font-bold text-ink-600">ابعاد (cm)</th>
                                <th scope="col" class="px-4 py-3.5 text-[0.8125rem] font-bold text-ink-600">وزن (kg)</th>
                                <th scope="col" class="px-4 py-3.5 text-[0.8125rem] font-bold text-ink-600">مقاومت (MPa)</th>
                                <th scope="col" class="px-4 py-3.5 text-[0.8125rem] font-bold text-ink-600">λ (W/m·K)</th>
                                <th scope="col" class="px-4 py-3.5 text-[0.8125rem] font-bold text-ink-600">صوت (dB)</th>
                                <th scope="col" class="px-4 py-3.5 text-[0.8125rem] font-bold text-ink-600">آتش (دقیقه)</th>
                                <th scope="col" class="px-4 py-3.5 text-[0.8125rem] font-bold text-ink-600">تعداد در m²</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sand-200 bg-sand-50">
                            @foreach($products as $product)
                                <tr class="transition hover:bg-clay-50">
                                    <th scope="row" class="px-4 py-3 text-right">
                                        <a href="{{ route('products.show', $product) }}" class="font-bold transition hover:text-clay-600">{{ $product->name }}</a>
                                        <span class="tech mr-2 text-[0.75rem] text-ink-300">{{ $product->sku }}</span>
                                    </th>
                                    @foreach([
                                        $product->dimensionLabel(),
                                        $product->weight_kg,
                                        $product->compressive_strength_mpa,
                                        $product->thermal_conductivity,
                                        $product->sound_reduction_db,
                                        $product->fire_resistance_min,
                                        $product->units_per_sqm,
                                    ] as $value)
                                        <td class="px-4 py-3 text-[0.9375rem]">
                                            <x-num :value="$value" />
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
