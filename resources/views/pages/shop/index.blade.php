@php use App\Support\Jalali; use App\Support\Shop; @endphp

<x-layouts.app>
    <x-page-hero
        eyebrow="Shop"
        title="فروشگاه"
        lead="هر محصول را چند فروشنده عرضه می‌کنند. قیمت‌ها را کنار هم ببینید و از همان فروشنده‌ای بخرید که شرایطش به پروژه‌تان می‌خورد." />

    <section class="bg-sand-100 section-b">
        <div class="container-page">
            @if($products->isEmpty())
                <p class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-6 text-ink-500">
                    هنوز محصولی برای فروش عرضه نشده است.
                </p>
            @else
                <ul class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3" data-reveal-stagger="70">
                    @foreach($products as $product)
                        <li data-reveal
                            class="group relative flex flex-col overflow-hidden rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50">
                            <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-bl from-sand-200 via-sand-100 to-sand-300">
                                <div class="absolute inset-0 grid place-items-center">
                                    <x-media :path="$product->hero_image" :alt="$product->name">
                                        <x-block-3d :product="$product" :size="140" :interactive="false" />
                                    </x-media>
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col p-5">
                                <p class="text-meta text-ink-400">{{ $product->category?->name }}</p>

                                <h2 class="mt-1 text-card font-extrabold">
                                    <a href="{{ route('shop.show', $product) }}" class="after:absolute after:inset-0 focus:outline-none">
                                        {{ $product->name }}
                                    </a>
                                </h2>

                                <div class="mt-auto pt-4">
                                    <p class="text-meta text-ink-400">از</p>
                                    <p class="tech text-lg font-extrabold text-clay-600">{{ Shop::price((int) $product->best_price) }}</p>
                                    <p class="mt-1 text-meta text-ink-400">
                                        {{ Jalali::digits($product->vendors_count) }} فروشنده
                                    </p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-10">{{ $products->links() }}</div>
            @endif
        </div>
    </section>
</x-layouts.app>
