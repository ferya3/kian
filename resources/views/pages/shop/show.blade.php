@php use App\Support\Jalali; use App\Support\Shop; @endphp

<x-layouts.app>
    <section class="bg-sand-100 section">
        <div class="container-page">
            <x-breadcrumbs class="mb-6" />

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">

                {{-- تصویر و مشخصات کوتاه --}}
                <div class="lg:col-span-5">
                    <div class="relative aspect-[4/3] overflow-hidden rounded-[var(--radius-panel)] border border-sand-300 bg-gradient-to-bl from-sand-200 via-sand-100 to-sand-300">
                        <div class="absolute inset-0 grid place-items-center">
                            <x-media :path="$product->hero_image" :alt="$product->name" eager>
                                <x-block-3d :product="$product" :size="200" :interactive="false" />
                            </x-media>
                        </div>
                    </div>

                    <p class="mt-5 leading-relaxed text-ink-500">{{ $product->summary }}</p>

                    <x-cta :href="route('products.show', $product)" variant="ghost" size="sm" class="mt-5">
                        {{ __('site.shop.full_specs') }}
                    </x-cta>
                </div>

                {{-- فروشنده‌ها --}}
                <div class="lg:col-span-7">
                    <p class="eyebrow text-clay-600">Vendors</p>
                    <h1 class="mt-2 text-h2 font-extrabold text-balance">{{ $product->name }}</h1>
                    <p class="mt-3 text-ink-500">
                        {{ __('site.shop.offers_lead', ['count' => Jalali::digits($offers->count())]) }}
                    </p>

                    @if(session('status'))
                        <p class="mt-5 rounded-xl border border-clay-300 bg-clay-50 px-4 py-3 text-meta font-semibold text-clay-700">
                            {{ session('status') }}
                        </p>
                    @endif

                    <ul class="mt-6 space-y-4">
                        @foreach($offers as $offer)
                            <li class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-5">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="text-card font-extrabold">{{ $offer->vendor->name }}</p>
                                        <p class="mt-1 text-meta text-ink-400">
                                            @if($offer->vendor->city)
                                                {{ $offer->vendor->province }}، {{ $offer->vendor->city }}
                                            @endif
                                        </p>
                                    </div>

                                    <div class="text-end">
                                        <p class="tech text-xl font-extrabold text-clay-600">{{ Shop::price($offer->price) }}</p>
                                        <p class="text-meta text-ink-400">{{ __('site.shop.per_unit', ['unit' => $offer->unit]) }}</p>
                                    </div>
                                </div>

                                <p class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-meta text-ink-500">
                                    <span>{{ __('site.shop.min_order', ['value' => Jalali::digits($offer->min_order), 'unit' => $offer->unit]) }}</span>

                                    @if($offer->stock !== null)
                                        <span>{{ __('site.shop.stock', ['value' => Jalali::digits($offer->stock)]) }}</span>
                                    @endif

                                    @if($offer->lead_time_days)
                                        <span>{{ __('site.shop.lead_time', ['days' => Jalali::digits($offer->lead_time_days)]) }}</span>
                                    @endif
                                </p>

                                <form action="{{ route('cart.store') }}" method="POST" class="mt-4 flex flex-wrap items-center gap-3">
                                    @csrf
                                    <input type="hidden" name="offer" value="{{ $offer->id }}">

                                    <label class="sr-only" for="qty-{{ $offer->id }}">{{ __('site.shop.quantity') }}</label>
                                    <input id="qty-{{ $offer->id }}" name="quantity" type="number" inputmode="numeric"
                                           value="{{ $offer->min_order }}"
                                           min="{{ $offer->min_order }}"
                                           @if($offer->stock !== null) max="{{ $offer->stock }}" @endif
                                           class="h-11 w-24 rounded-xl border border-sand-300 bg-white px-3 text-field outline-none focus:border-clay-400">

                                    <button type="submit" class="tap rounded-full bg-clay-500 px-5 font-semibold text-white transition hover:bg-clay-600">
                                        {{ __('site.shop.add') }}
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
