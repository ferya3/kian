@php
    /*
    | آیکون هر مرحله. در دیتابیس ستونی برایش نیست و ساختن ستون تازه برای یک
    | تصمیم صرفاً تصویری، جای درستی نیست — پس نگاشت اینجا می‌ماند و هر کلیدِ
    | ناشناخته به grid برمی‌گردد.
    */
    $stepIcons = [
        1 => 'layers',    // استخراج خاک
        2 => 'grid',      // آماده‌سازی
        3 => 'trowel',    // مخلوط‌سازی
        4 => 'blueprint', // اکستروژن
        5 => 'ruler',     // برش
        6 => 'thermal',   // خشک‌کردن
        7 => 'fire',      // پخت
        8 => 'shield',    // کنترل کیفیت
        9 => 'factory',   // بسته‌بندی
    ];
@endphp

{{--
    «از خاک تا سازه» — تایم‌لاین فرایند تولید.

    دسکتاپ: مدار چرخان. نُه مرحله دور یک هسته می‌چرخند و با کلیک باز می‌شوند.
    موبایل: همان محتوا به‌صورت ریل قابل swipe — مدار در ۳۶۰ پیکسل جا نمی‌شود و
    عنوان‌های فارسی روی شعاع کوچک روی هم می‌افتند.

    محتوا در هر دو حالت از یک منبع می‌آید و هر دو در HTML هستند؛ فقط یکی در هر
    اندازه دیده می‌شود.
--}}
<section class="relative overflow-hidden bg-sand-50 section"
         aria-labelledby="process-heading">

    {{--
        خط افق کارخانه که پیش‌تر اینجا بود، برای حرکت افقی طراحی شده بود و
        پشت مدار بی‌ربط می‌افتاد؛ برداشته شد.
    --}}

    <div class="relative flex h-full flex-col lg:justify-center">

        {{-- سربرگ --}}
        <div class="container-page shrink-0">
            <div class="max-w-2xl">
                <p class="eyebrow text-clay-600" data-reveal>From earth to architecture</p>
                <h2 id="process-heading" class="mt-3 text-h2 font-extrabold text-balance" data-reveal>از خاک تا سازه</h2>
                <p class="mt-4 text-lead text-ink-500" data-reveal>
                    نُه مرحله، از برداشت خاک رس معدن تا پالت شرینک‌پیچ‌شده‌ی آماده‌ی بارگیری.
                    <span class="hidden lg:inline">روی هر مرحله کلیک کنید.</span>
                </p>
            </div>
        </div>

        @include('partials.home.process-orbit', ['stepIcons' => $stepIcons])

        {{-- ریل مراحل — زیر lg --}}
        <div class="mt-10 lg:hidden">
            <ol data-process-track
                class="scroll-rail lg:mx-0 lg:flex lg:gap-8 lg:overflow-visible lg:px-[max(1.25rem,calc((100vw-88rem)/2+3rem))]">
                @foreach($processSteps as $step)
                    <li data-process-step
                        class="w-[78vw] max-w-sm shrink-0 sm:w-[52vw] lg:w-[26rem]">
                        <article class="flex h-full flex-col rounded-[var(--radius-panel)] border border-sand-300 bg-sand-100 p-6 transition hover:border-clay-300 lg:p-7">

                            <div class="flex items-start justify-between gap-4">
                                <span class="tech text-4xl font-extrabold leading-none text-clay-500/35">{{ \App\Support\Jalali::digits($step->paddedNumber()) }}</span>
                                @if($step->duration)
                                    <span class="flex items-center gap-1.5 rounded-full border border-sand-300 bg-sand-50 px-3 py-1 text-micro text-ink-500">
                                        <x-icon name="clock" size="13" />
                                        {{ $step->duration }}
                                    </span>
                                @endif
                            </div>

                            @if(\App\Support\Media::has($step->image))
                                <div class="mt-5 aspect-[16/9] overflow-hidden rounded-2xl bg-sand-200">
                                    <x-media :path="$step->image" :alt="$step->title" />
                                </div>
                            @endif

                            <div data-process-body class="mt-5 flex flex-1 flex-col">
                                <h3 class="text-card font-bold">{{ $step->title }}</h3>
                                <p class="tech mt-1 text-micro uppercase tracking-[0.14em] text-ink-300">{{ $step->title_en }}</p>

                                <p class="mt-4 flex-1 leading-relaxed text-ink-500">{{ $step->description }}</p>

                                @if($step->metric_value)
                                    <dl class="mt-6 flex items-baseline justify-between border-t border-sand-300 pt-4">
                                        <dt class="text-meta text-ink-400">{{ $step->metric_label }}</dt>
                                        <dd class="text-lg font-extrabold text-clay-600"><x-num :value="$step->metric_value" /></dd>
                                    </dl>
                                @endif
                            </div>
                        </article>
                    </li>
                @endforeach

                {{-- کارت پایانی: دعوت به ادامه --}}
                <li data-process-step class="w-[78vw] max-w-sm shrink-0 sm:w-[52vw] lg:w-[26rem]">
                    <div class="flex h-full flex-col justify-center rounded-[var(--radius-panel)] bg-ink-950 p-7 text-sand-50">
                        <p class="eyebrow text-clay-400">Output</p>
                        <h3 class="mt-3 text-h3 font-extrabold">و بعد، ساختمان</h3>
                        <p class="mt-4 leading-relaxed text-sand-200/65">
                            هر پالتی که از این خط خارج می‌شود، شناسه‌ی بچ تولید دارد — تا اگر ده سال بعد پرسشی درباره‌ی آن پیش آمد، بتوانیم پاسخ بدهیم.
                        </p>
                        <div class="mt-7 flex flex-wrap gap-2">
                            <x-cta :href="route('technology')" variant="primary" size="sm">جزئیات فناوری</x-cta>
                            <x-cta :href="route('factory')" variant="light" size="sm">بازدید از کارخانه</x-cta>
                        </div>
                    </div>
                </li>
            </ol>
        </div>

        <p class="container-page mt-5 flex items-center gap-2 text-meta text-ink-400 lg:hidden">
            <x-icon name="arrow-right" size="15" />
            مراحل را بکشید
        </p>
    </div>
</section>
