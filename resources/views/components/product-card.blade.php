@props(['product', 'showBars' => true, 'eager' => false])

<article {{ $attributes->merge(['class' => 'group relative flex flex-col overflow-hidden rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 transition-[border-color,box-shadow,transform] duration-500 ease-[var(--ease-out-expo)] hover:-translate-y-1 hover:border-clay-300 hover:shadow-lift focus-within:border-clay-400']) }}>

    {{-- تصویر محصول --}}
    <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-bl from-sand-200 via-sand-100 to-sand-300">
        <div class="absolute inset-0 opacity-[0.5]"
             style="background-image: radial-gradient(circle at 22% 18%, rgba(180,85,45,.16), transparent 55%)"></div>

        <div class="absolute inset-0 grid place-items-center transition-transform duration-700 ease-[var(--ease-out-expo)] group-hover:scale-[1.06]">
            <x-media :path="$product->hero_image" :alt="$product->name" :eager="$eager">
                <x-block-3d :product="$product" :size="152" :interactive="false" />
            </x-media>
        </div>

        @if($product->is_featured)
            <span class="eyebrow absolute right-4 top-4 rounded-full bg-ink-900/90 px-3 py-1 text-micro text-sand-50">
                پرکاربرد
            </span>
        @endif

        <span class="tech absolute left-4 top-4 rounded-full border border-ink-900/10 bg-sand-50/80 px-2.5 py-1 text-micro font-semibold text-ink-500 backdrop-blur">
            {{ $product->sku }}
        </span>

        @if($showBars)
            {{-- میله‌های عملکردی — روی دسکتاپ با hover، روی موبایل همیشه پیدا --}}
            <div class="absolute inset-x-0 bottom-0 border-t border-sand-300/70 bg-sand-50/92 px-4 py-3 backdrop-blur-md
                        transition-[transform,opacity] duration-500 ease-[var(--ease-out-expo)]
                        lg:translate-y-full lg:opacity-0 lg:group-hover:translate-y-0 lg:group-hover:opacity-100 lg:group-focus-within:translate-y-0 lg:group-focus-within:opacity-100">
                <x-perf-bars :product="$product" compact />
            </div>
        @endif
    </div>

    {{-- متن --}}
    <div class="flex flex-1 flex-col p-5">
        <p class="eyebrow text-clay-600">{{ $product->category?->name }}</p>

        <h3 class="mt-2 text-xl font-extrabold leading-snug">
            <a href="{{ route('products.show', $product) }}" class="after:absolute after:inset-0 focus:outline-none">
                {{ $product->name }}
            </a>
        </h3>

        <p class="tech mt-1 text-sm text-ink-400">
            {{ \App\Support\Jalali::digits($product->dimensionLabel()) }} سانتی‌متر
        </p>

        <p class="mt-3 line-clamp-2 text-[0.9375rem] leading-relaxed text-ink-500">
            {{ $product->subtitle }}
        </p>

        {{-- سه عدد کلیدی که مهندس اول از همه دنبالش می‌گردد --}}
        <dl class="mt-auto grid grid-cols-3 gap-px overflow-hidden rounded-xl border border-sand-200 bg-sand-200 pt-px">
            @foreach([
                ['λ', $product->thermal_conductivity, 'W/m·K'],
                ['وزن', $product->weight_kg, 'kg'],
                ['مقاومت', $product->compressive_strength_mpa, 'MPa'],
            ] as [$label, $value, $unit])
                <div class="bg-sand-50 px-2 py-2.5 text-center">
                    <dt class="text-micro text-ink-400">{{ $label }}</dt>
                    <dd class="mt-0.5 text-sm font-bold text-ink-900">
                        <x-num :value="$value" />
                        <span class="text-micro font-normal text-ink-300">{{ $unit }}</span>
                    </dd>
                </div>
            @endforeach
        </dl>
    </div>
</article>
