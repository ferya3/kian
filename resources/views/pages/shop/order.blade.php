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
                @php
                    /*
                    | شناسه از خودِ ردیف‌ها می‌آید و نه از کلیدِ گروه: کلید
                    | نامِ کپی‌شده‌ی فروشنده است و دو فروشنده می‌توانند هم‌نام
                    | باشند.
                    */
                    $vendorId = $items->first()->vendor_id;
                    $vendor = $vendors[$vendorId] ?? null;
                    $receipt = $order->receipts->firstWhere('vendor_id', $vendorId);
                    $due = $items->sum('total');
                @endphp

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

                    {{--
                        حواله‌ی همین فروشنده.

                        هر فروشنده حسابِ خودش را دارد، پس سفارشِ دوفروشنده‌ای
                        دو حواله‌ی جداست. یک فرمِ واحد برای کلِ سفارش یعنی
                        پولِ هر دو به یک حساب برود.
                    --}}
                    @if($vendor?->acceptsTransfer())
                        <div class="border-t border-sand-300 bg-sand-100/70 px-5 py-5">
                            <p class="flex flex-wrap items-baseline justify-between gap-2">
                                <span class="font-bold">{{ __('site.shop.transfer_title') }}</span>
                                <span class="tech text-lead font-extrabold text-clay-600">{{ Shop::price($due) }}</span>
                            </p>

                            <dl class="mt-3 grid gap-x-4 gap-y-1.5 text-meta sm:grid-cols-[auto_1fr]">
                                @foreach(array_filter([
                                    __('site.shop.bank_holder') => $vendor->bank_holder,
                                    __('site.shop.bank_name') => $vendor->bank_name,
                                    __('site.shop.bank_card') => $vendor->bank_card,
                                    __('site.shop.bank_iban') => $vendor->bank_iban,
                                ]) as $label => $value)
                                    <dt class="text-ink-400">{{ $label }}</dt>
                                    <dd class="tech font-bold text-ink-800"><bdi dir="ltr">{{ Jalali::digits($value) }}</bdi></dd>
                                @endforeach
                            </dl>

                            @if($receipt?->isConfirmed())
                                <p class="mt-4 rounded-xl border border-green-600/25 bg-green-50 px-4 py-3 text-meta font-semibold text-green-800">
                                    ✓ {{ $receipt->statusLabel() }}
                                </p>
                            @else
                                @if($receipt?->isPending())
                                    <p class="mt-4 rounded-xl border border-clay-300 bg-clay-50 px-4 py-3 text-meta font-semibold text-clay-700">
                                        {{ $receipt->statusLabel() }} — {{ __('site.shop.receipt_replace') }}
                                    </p>
                                @elseif($receipt?->isRejected())
                                    <p class="mt-4 rounded-xl border border-red-600/25 bg-red-50 px-4 py-3 text-meta text-red-800">
                                        <span class="font-bold">{{ $receipt->statusLabel() }}</span>
                                        @if($receipt->note) — {{ $receipt->note }} @endif
                                    </p>
                                @endif

                                @if(session('receipt_saved') == $vendorId)
                                    <p class="mt-4 rounded-xl border border-green-600/25 bg-green-50 px-4 py-3 text-meta font-semibold text-green-800">
                                        {{ __('site.shop.receipt_thanks') }}
                                    </p>
                                @endif

                                <form method="POST" action="{{ route('orders.receipt', $order) }}"
                                      enctype="multipart/form-data" class="mt-4 grid gap-3 sm:grid-cols-2">
                                    @csrf
                                    <input type="hidden" name="vendor_id" value="{{ $vendorId }}">

                                    <label class="block">
                                        <span class="text-meta text-ink-500">{{ __('site.shop.receipt_reference') }}</span>
                                        <input type="text" name="reference" maxlength="60"
                                               class="mt-1 w-full rounded-xl border border-sand-300 bg-sand-50 px-4 py-2.5">
                                    </label>

                                    <label class="block">
                                        <span class="text-meta text-ink-500">{{ __('site.shop.receipt_image') }}</span>
                                        <input type="file" name="image" required accept="image/*"
                                               class="mt-1 w-full rounded-xl border border-sand-300 bg-sand-50 px-4 py-2 text-meta">
                                    </label>

                                    @error('image')
                                        <p class="text-meta text-red-700 sm:col-span-2">{{ $message }}</p>
                                    @enderror

                                    <button type="submit"
                                            class="tap rounded-full bg-ink-900 px-5 py-2.5 text-sm font-semibold text-sand-50 transition hover:bg-clay-600 sm:col-span-2 sm:justify-self-start">
                                        {{ __('site.shop.receipt_send') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        {{-- بی شماره‌ی حساب، حواله ممکن نیست؛ همان تماسِ فروشنده می‌ماند --}}
                        <p class="border-t border-sand-300 bg-sand-100/70 px-5 py-4 text-meta text-ink-500">
                            {{ __('site.shop.transfer_unavailable') }}
                        </p>
                    @endif
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
