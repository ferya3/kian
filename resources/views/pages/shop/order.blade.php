@php use App\Support\Jalali; use App\Support\Shop; @endphp

<x-layouts.app>
    <section class="bg-sand-100 section">
        <div class="container-page max-w-3xl">
            <p class="eyebrow text-clay-600">Order</p>
            <h1 class="mt-2 text-h2 font-extrabold">{{ __('site.shop.placed') }}</h1>

            <p class="mt-4 text-ink-500">
                {!! __('site.shop.placed_lead', [
                    'number' => '<span class="tech font-extrabold text-ink-900">'.e(Jalali::digits($order->number)).'</span>',
                ]) !!}
            </p>

            <p class="mt-2 text-meta text-ink-400">
                {{ __('site.shop.status') }}: <span class="font-bold text-ink-700">{{ $order->statusLabel() }}</span>
            </p>

            {{--
                به تفکیک فروشنده.

                سفارشی که نزد دو فروشنده رفته، دو تماس و احتمالاً دو ارسال
                دارد. نشان‌ندادن این تفکیک یعنی مشتری منتظر یک تماس می‌ماند.
            --}}
            @foreach($order->byVendor() as $vendorName => $items)
                <div class="mt-6 overflow-hidden rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50">
                    <p class="border-b border-sand-300 bg-sand-200/60 px-5 py-3 font-bold">{{ $vendorName }}</p>

                    <ul class="divide-y divide-sand-200">
                        @foreach($items as $item)
                            <li class="flex flex-wrap items-baseline justify-between gap-3 px-5 py-4">
                                <span>
                                    {{ $item->product_name }}
                                    <span class="tech text-meta text-ink-400">× {{ Jalali::digits($item->quantity) }}</span>
                                </span>
                                <span class="flex items-baseline gap-3">
                                    <span class="text-meta text-ink-400">{{ $item->statusLabel() }}</span>
                                    <span class="tech font-bold">{{ Shop::price($item->total) }}</span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            <p class="mt-6 flex items-baseline justify-between rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-5 text-lead">
                <span>{{ __('site.shop.total') }}</span>
                <span class="tech font-extrabold text-clay-600">{{ Shop::price($order->total) }}</span>
            </p>

            <x-cta :href="route('shop.index')" variant="ghost" class="mt-8">{{ __('site.shop.back') }}</x-cta>
        </div>
    </section>
</x-layouts.app>
