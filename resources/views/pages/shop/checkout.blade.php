@php use App\Support\Jalali; use App\Support\Shop; @endphp

<x-layouts.app>
    <section class="bg-sand-100 section">
        <div class="container-page">
            <h1 class="text-h2 font-extrabold">تکمیل سفارش</h1>
            <p class="mt-3 max-w-2xl text-ink-500">
                پرداخت آنلاین ندارد. سفارش ثبت می‌شود و فروشنده برای هماهنگی مقدار، کرایه‌ی حمل و زمان تحویل با شما تماس می‌گیرد.
            </p>

            <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-12">
                <form action="{{ route('checkout.store') }}" method="POST" class="lg:col-span-7">
                    @csrf

                    @if($errors->any())
                        <div class="mb-6 rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-meta text-red-700">
                            <ul class="space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        @foreach([
                            ['customer_name', 'نام و نام خانوادگی', 'text', 'name', true],
                            ['customer_phone', 'تلفن همراه', 'tel', 'tel', true],
                            ['customer_email', 'ایمیل (اختیاری)', 'email', 'email', false],
                            ['province', 'استان', 'text', 'address-level1', false],
                            ['city', 'شهر', 'text', 'address-level2', false],
                        ] as [$name, $label, $type, $autocomplete, $required])
                            <label class="block @if($name === 'customer_name' || $name === 'customer_email') sm:col-span-2 @endif">
                                <span class="block text-meta font-semibold text-ink-600">{{ $label }}</span>
                                <input name="{{ $name }}" type="{{ $type }}" autocomplete="{{ $autocomplete }}"
                                       @if($type === 'tel') inputmode="tel" @endif
                                       @if($required) required @endif
                                       value="{{ old($name) }}"
                                       class="mt-1.5 h-12 w-full rounded-xl border border-sand-300 bg-white px-4 text-field outline-none focus:border-clay-400">
                            </label>
                        @endforeach

                        <label class="block sm:col-span-2">
                            <span class="block text-meta font-semibold text-ink-600">نشانی تحویل</span>
                            <textarea name="address" rows="3" autocomplete="street-address"
                                      class="mt-1.5 w-full rounded-xl border border-sand-300 bg-white p-4 text-field outline-none focus:border-clay-400">{{ old('address') }}</textarea>
                        </label>

                        <label class="block sm:col-span-2">
                            <span class="block text-meta font-semibold text-ink-600">توضیح (اختیاری)</span>
                            <textarea name="note" rows="3"
                                      class="mt-1.5 w-full rounded-xl border border-sand-300 bg-white p-4 text-field outline-none focus:border-clay-400">{{ old('note') }}</textarea>
                        </label>
                    </div>

                    <button type="submit" class="tap mt-6 rounded-full bg-clay-500 px-6 font-semibold text-white transition hover:bg-clay-600">
                        ثبت سفارش
                    </button>
                </form>

                <aside class="lg:col-span-5">
                    <div class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-5">
                        <h2 class="font-extrabold">خلاصه‌ی سفارش</h2>

                        <ul class="mt-4 space-y-3 text-meta">
                            @foreach($lines as $line)
                                <li class="flex items-baseline justify-between gap-3">
                                    <span class="min-w-0">
                                        {{ $line['offer']->product->name }}
                                        <span class="text-ink-400">({{ $line['offer']->vendor->name }})</span>
                                        <span class="tech text-ink-400">× {{ Jalali::digits($line['quantity']) }}</span>
                                    </span>
                                    <span class="tech shrink-0 font-bold">{{ Shop::price($line['total']) }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <p class="mt-5 flex items-baseline justify-between border-t border-sand-300 pt-4 text-lead">
                            <span>جمع کل</span>
                            <span class="tech font-extrabold text-clay-600">{{ Shop::price($total) }}</span>
                        </p>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</x-layouts.app>
