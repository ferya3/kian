{{-- خروجی موتور انتخاب محصول — هم برای تزریق زنده و هم برای صفحه‌ی کامل نتایج --}}
<div class="mt-6 rounded-[var(--radius-panel)] border border-clay-200 bg-clay-50/70 p-5 lg:p-7">

    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-clay-200 pb-4">
        <div>
            <p class="eyebrow text-clay-600">Recommended</p>
            <h3 class="mt-1.5 text-xl font-extrabold">
                {{ count($matches) }} محصول برای پروژه‌ی شما
            </h3>
        </div>

        @if($labels)
            <ul class="flex flex-wrap items-center gap-1.5">
                @foreach($labels as $label)
                    <li class="rounded-full border border-clay-200 bg-sand-50 px-3 py-1 text-meta text-clay-700">{{ $label }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <ol class="mt-5 space-y-4">
        @foreach($matches as $index => $match)
            @php $product = $match['product']; @endphp
            <li class="group relative overflow-hidden rounded-2xl border border-sand-300 bg-sand-50 transition hover:border-clay-300 hover:shadow-lift">
                <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center">

                    <div class="flex shrink-0 items-center gap-4">
                        <span class="tech grid h-9 w-9 place-items-center rounded-full text-sm font-bold
                                     {{ $index === 0 ? 'bg-clay-500 text-white' : 'bg-sand-200 text-ink-500' }}">
                            {{ \App\Support\Jalali::digits($index + 1) }}
                        </span>
                        <div class="hidden w-[132px] shrink-0 sm:block">
                            <x-block-3d :product="$product" :size="118" :interactive="false" />
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                            <h4 class="text-lg font-extrabold">
                                <a href="{{ route('products.show', $product) }}" class="after:absolute after:inset-0">{{ $product->name }}</a>
                            </h4>
                            <span class="tech text-sm text-ink-400">{{ \App\Support\Jalali::digits($product->dimensionLabel()) }} cm</span>
                            @if($index === 0)
                                <span class="rounded-full bg-ink-900 px-2.5 py-0.5 text-micro font-semibold text-sand-50">بهترین تطابق</span>
                            @endif
                        </div>

                        <p class="mt-1.5 line-clamp-2 text-[0.9375rem] leading-relaxed text-ink-500">{{ $product->summary }}</p>

                        @if($match['reasons'])
                            <ul class="mt-3 flex flex-wrap gap-x-4 gap-y-1.5">
                                @foreach($match['reasons'] as $reason)
                                    <li class="flex items-center gap-1.5 text-meta text-clay-700">
                                        <x-icon name="check" size="14" class="text-clay-500" />
                                        {{ $reason }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if($match['gaps'] && $index > 0)
                            <p class="mt-2 text-meta text-ink-400">{{ $match['gaps'][0] }}</p>
                        @endif
                    </div>

                    <div class="shrink-0 sm:w-40">
                        <div class="mb-2 flex items-baseline justify-between gap-2">
                            <span class="text-micro text-ink-400">میزان تطابق</span>
                            <span class="tech text-sm font-bold text-clay-600">{{ \App\Support\Jalali::digits($match['score']) }}٪</span>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded-full bg-sand-300"
                             role="meter" aria-valuenow="{{ $match['score'] }}" aria-valuemin="0" aria-valuemax="100"
                             aria-label="میزان تطابق {{ $product->name }}">
                            <div class="h-full rounded-full bg-gradient-to-l from-ember-500 to-clay-500"
                                 style="width: {{ $match['score'] }}%"></div>
                        </div>
                        <x-perf-bars :product="$product" compact class="mt-4 hidden sm:block" />
                    </div>
                </div>
            </li>
        @endforeach
    </ol>

    <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-clay-200 pt-5">
        <p class="text-meta text-ink-400">
            این پیشنهاد بر اساس معیارهای شماست. برای محاسبه‌ی دقیق مبحث ۱۹ با واحد فنی تماس بگیرید.
        </p>
        <div class="flex flex-wrap gap-2">
            <x-cta :href="route('contact', ['type' => 'technical'])" variant="ghost" size="sm">مشاوره فنی رایگان</x-cta>
            <x-cta :href="route('products.index')" variant="dark" size="sm">مشاهده همه محصولات</x-cta>
        </div>
    </div>
</div>
