{{--
    نوار شاخص‌ها — بلافاصله زیر هیرو.
    چهار عدد در یک ردیف روی همه‌ی اندازه‌ها، حتی گوشی. برای اینکه در ۹۰ پیکسل
    عرضِ هر ستون جا شود، عدد و برچسب روی موبایل کوچک‌تر و leading فشرده‌تر است.
--}}
<section aria-label="شاخص‌های کارخانه" class="border-b border-sand-300 bg-sand-50">
    <div class="mx-auto w-full max-w-[88rem] px-3 sm:px-6 lg:px-12">
        <dl class="grid grid-cols-4 divide-x divide-x-reverse divide-sand-200">
            @foreach($stats as $stat)
                <div class="px-1.5 py-4 text-center sm:px-3 sm:py-6 lg:px-5 lg:py-8 lg:text-right">
                    <dd class="font-extrabold text-ink-900">
                        <bdi dir="ltr" class="tech inline-block whitespace-nowrap text-[0.9375rem] sm:text-xl lg:text-3xl">
                            <span data-countup="{{ $stat->value }}" data-decimals="{{ $stat->decimals }}"
                                  @if($stat->value >= 1000) data-separated @endif>۰</span><span class="text-clay-500">{{ $stat->suffix }}</span>
                        </bdi>
                    </dd>
                    <dt class="mt-1 text-micro leading-tight text-ink-400 sm:mt-1.5 sm:leading-snug lg:mt-2 lg:text-meta lg:text-ink-500">
                        {{ $stat->label }}
                    </dt>
                </div>
            @endforeach
        </dl>
    </div>
</section>
