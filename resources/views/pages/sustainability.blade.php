<x-layouts.app>
    <x-page-hero
        eyebrow="Sustainability"
        title="خاک → محصول → ساختمان"
        lead="سفال چرخه‌ی بسته‌ای دارد که در آن هیچ ماده‌ی غریبه‌ای وارد نمی‌شود. این صفحه آنچه را انجام داده‌ایم می‌گوید — و آنچه هنوز نکرده‌ایم."
        variant="dark">
        <x-stat-band :stats="$stats" light class="mt-12" />
    </x-page-hero>

    <section class="bg-sand-50 py-16 lg:py-24">
        <div class="container-page grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-5">
                <x-section-heading eyebrow="Life cycle" title="چرخه‌ی عمر یک بلوک" />
            </div>

            <ol class="lg:col-span-7" data-reveal-stagger="90">
                @foreach([
                    ['استخراج', 'خاک رس از معدن اختصاصی در هجده کیلومتری کارخانه برداشت می‌شود. مسافت کوتاه یعنی انتشار حمل‌ونقل کمتر. برای هر جبهه‌ی کاری، طرح بازسازی محل برداشت الزامی است.'],
                    ['تولید', 'حرارت خروجی کوره به خشک‌کن بازمی‌گردد. ضایعات خامِ پیش از پخت صددرصد به خط تولید برمی‌گردد؛ ضایعات پخته خرد و به‌عنوان مصالح زیرسازی استفاده می‌شود.'],
                    ['بهره‌برداری', 'اینجا بزرگ‌ترین اثر رخ می‌دهد: انرژی‌ای که در پنجاه سال بهره‌برداری صرفه‌جویی می‌شود، چند برابر انرژی مصرف‌شده در تولید است.'],
                    ['پایان عمر', 'سفال ماده‌ای معدنی و بی‌اثر است. در تخریب، قابل خردایش و استفاده به‌عنوان مصالح پرکننده یا زیرسازی است — بدون آلایندگی خاک.'],
                ] as $i => [$title, $text])
                    <li data-reveal class="grid grid-cols-[auto_1fr] gap-5 border-b border-sand-200 py-6 first:pt-0 last:border-0">
                        <span class="tech grid h-9 w-9 place-items-center rounded-full bg-clay-100 text-sm font-bold text-clay-700">
                            {{ \App\Support\Jalali::digits($i + 1) }}
                        </span>
                        <div>
                            <h3 class="text-h3 font-bold">{{ $title }}</h3>
                            <p class="mt-2 leading-relaxed text-ink-500">{{ $text }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="bg-sand-100 py-16 lg:py-20">
        <div class="container-page">
            <div class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-7 lg:p-10">
                <p class="eyebrow text-clay-600">Honest note</p>
                <h2 class="mt-3 text-h3 font-extrabold">آنچه هنوز حل نشده</h2>
                <p class="mt-4 max-w-3xl text-lead text-ink-600">
                    پخت سفال ذاتاً فرایندی پرانرژی است. بازیابی حرارت کوره بخشی از مسئله را حل کرده، اما نه همه‌اش.
                    جایگزینی سهمی از سوخت فسیلی و بهبود عایق‌بندی بدنه‌ی کوره، برنامه‌ی سه سال آینده‌ی ماست.
                    هر عددی که در این صفحه نوشته‌ایم، از گزارش پایش داخلی می‌آید و قابل ارائه به کارفرماست.
                </p>
                <x-cta :href="route('contact', ['type' => 'technical'])" variant="ghost" class="mt-7">درخواست گزارش پایش</x-cta>
            </div>
        </div>
    </section>
</x-layouts.app>
