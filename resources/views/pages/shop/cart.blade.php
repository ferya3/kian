@php use App\Support\Jalali; use App\Support\Shop; @endphp

<x-layouts.app>
    <section class="bg-sand-100 section">
        <div class="container-page">
            <h1 class="text-h2 font-extrabold">سبد خرید</h1>

            @if(session('status'))
                <p class="mt-5 rounded-xl border border-clay-300 bg-clay-50 px-4 py-3 text-meta font-semibold text-clay-700">
                    {{ session('status') }}
                </p>
            @endif

            @if($lines->isEmpty())
                <p class="mt-6 text-ink-500">سبد خالی است.</p>
                <x-cta :href="route('shop.index')" variant="primary" class="mt-6">رفتن به فروشگاه</x-cta>
            @else
                {{--
                    ردیف‌ها به تفکیک فروشنده.

                    مشتری باید پیش از ثبت بداند سفارشش نزد چند فروشنده می‌رود؛
                    کرایه‌ی حمل و زمان تحویلِ هرکدام جداست.
                --}}
                @foreach($lines->groupBy(fn ($line) => $line['offer']->vendor->name) as $vendorName => $group)
                    <div class="mt-6 overflow-hidden rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50">
                        <p class="border-b border-sand-300 bg-sand-200/60 px-5 py-3 font-bold">{{ $vendorName }}</p>

                        <ul class="divide-y divide-sand-200">
                            @foreach($group as $line)
                                @php $offer = $line['offer']; @endphp
                                <li class="flex flex-wrap items-center gap-4 p-5">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold">{{ $offer->product->name }}</p>
                                        <p class="tech mt-1 text-meta text-ink-400">
                                            {{ Shop::price($offer->price) }} × {{ Jalali::digits($line['quantity']) }} {{ $offer->unit }}
                                        </p>
                                    </div>

                                    <form action="{{ route('cart.update', $offer) }}" method="POST" class="flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <label class="sr-only" for="line-{{ $offer->id }}">تعداد</label>
                                        <input id="line-{{ $offer->id }}" name="quantity" type="number" inputmode="numeric"
                                               value="{{ $line['quantity'] }}" min="0"
                                               class="h-11 w-24 rounded-xl border border-sand-300 bg-white px-3 text-field outline-none focus:border-clay-400">
                                        <button type="submit" class="tap rounded-full border border-sand-300 px-4 text-meta font-semibold transition hover:bg-sand-200">
                                            به‌روزرسانی
                                        </button>
                                    </form>

                                    <p class="tech w-32 text-end font-extrabold">{{ Shop::price($line['total']) }}</p>

                                    <form action="{{ route('cart.destroy', $offer) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="tap-icon rounded-lg text-ink-400 transition hover:bg-sand-200 hover:text-clay-600"
                                                aria-label="برداشتن «{{ $offer->product->name }}» از سبد">
                                            <x-icon name="close" size="18" />
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                <div class="mt-8 flex flex-wrap items-center justify-between gap-4 rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-5">
                    <p class="text-lead">
                        جمع کل:
                        <span class="tech font-extrabold text-clay-600">{{ Shop::price($total) }}</span>
                    </p>
                    <x-cta :href="route('checkout.show')" variant="primary">تکمیل سفارش</x-cta>
                </div>
            @endif
        </div>
    </section>
</x-layouts.app>
