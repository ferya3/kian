{{--
    «از خاک تا سازه» — تایم‌لاین فرایند تولید.

    دسکتاپ: صفحه pin می‌شود و ۹ مرحله افقی حرکت می‌کنند؛ حسِ حرکت دوربین در طول خط تولید.
    موبایل / reduced-motion: همان محتوا به‌صورت ریل قابل swipe، بدون هیچ pin شدنی.
--}}
<section data-process-scroll
         class="relative overflow-hidden bg-sand-50 py-20 lg:h-[100svh] lg:overflow-hidden lg:py-0"
         aria-labelledby="process-heading">

    {{-- لایه‌ی پس‌زمینه که کندتر حرکت می‌کند --}}
    <div data-process-parallax class="pointer-events-none absolute inset-0 hidden opacity-70 lg:block" aria-hidden="true">
        <svg class="absolute bottom-0 right-0 h-[34%] w-[230%]" viewBox="0 0 3200 400" preserveAspectRatio="xMaxYMax slice">
            <g stroke="#ded7cb" stroke-width="2" fill="none">
                <path d="M0 340h3200"/>
                <path d="M0 300h420v-60h180v60h520v-90h240v90h700v-50h300v50h840"/>
                <path d="M180 240v-70h60v70M980 210v-90h50v90M1900 250v-40h40v40"/>
            </g>
            <g fill="#ece8e0" fill-opacity=".75">
                <rect x="600" y="250" width="380" height="90" rx="4"/>
                <rect x="1500" y="230" width="500" height="110" rx="4"/>
                <rect x="2400" y="262" width="320" height="78" rx="4"/>
            </g>
        </svg>
    </div>

    <div class="relative flex h-full flex-col lg:justify-center">

        {{-- سربرگ ثابت --}}
        <div class="container-page shrink-0 lg:pt-24">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div class="max-w-2xl">
                    <p class="eyebrow text-clay-600" data-reveal>From earth to architecture</p>
                    <h2 id="process-heading" class="mt-3 text-h2 font-extrabold text-balance" data-reveal>از خاک تا سازه</h2>
                    <p class="mt-4 text-lead text-ink-500" data-reveal>
                        نُه مرحله، از برداشت خاک رس معدن تا پالت شرینک‌پیچ‌شده‌ی آماده‌ی بارگیری.
                    </p>
                </div>

                <p class="tech hidden items-baseline gap-1.5 lg:flex" aria-hidden="true">
                    <span class="text-5xl font-extrabold leading-none text-clay-500" data-process-counter>۰۱</span>
                    <span class="text-2xl font-bold leading-none text-sand-300">/</span>
                    <span class="text-2xl font-bold leading-none text-ink-400">۰۹</span>
                </p>
            </div>

            <div class="mt-7 hidden h-0.5 overflow-hidden rounded-full bg-sand-300 lg:block">
                <div data-process-progress class="h-full origin-right scale-x-0 bg-clay-500 transition-transform duration-150"></div>
            </div>
        </div>

        {{-- ریل مراحل --}}
        <div class="mt-10 lg:mt-12">
            <ol data-process-track
                class="scroll-rail lg:mx-0 lg:flex lg:gap-8 lg:overflow-visible lg:px-[max(1.25rem,calc((100vw-88rem)/2+3rem))]">
                @foreach($processSteps as $step)
                    <li data-process-step
                        class="w-[78vw] max-w-sm shrink-0 sm:w-[52vw] lg:w-[26rem]">
                        <article class="flex h-full flex-col rounded-[var(--radius-panel)] border border-sand-300 bg-sand-100 p-6 transition hover:border-clay-300 lg:p-7">

                            <div class="flex items-start justify-between gap-4">
                                <span class="tech text-4xl font-extrabold leading-none text-clay-500/35">{{ \App\Support\Jalali::digits($step->paddedNumber()) }}</span>
                                @if($step->duration)
                                    <span class="flex items-center gap-1.5 rounded-full border border-sand-300 bg-sand-50 px-3 py-1 text-[0.75rem] text-ink-500">
                                        <x-icon name="clock" size="13" />
                                        {{ $step->duration }}
                                    </span>
                                @endif
                            </div>

                            <div data-process-body class="mt-5 flex flex-1 flex-col">
                                <h3 class="text-h3 font-bold">{{ $step->title }}</h3>
                                <p class="tech mt-1 text-[0.75rem] uppercase tracking-[0.14em] text-ink-300">{{ $step->title_en }}</p>

                                <p class="mt-4 flex-1 leading-relaxed text-ink-500">{{ $step->description }}</p>

                                @if($step->metric_value)
                                    <dl class="mt-6 flex items-baseline justify-between border-t border-sand-300 pt-4">
                                        <dt class="text-[0.8125rem] text-ink-400">{{ $step->metric_label }}</dt>
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

        <p class="container-page mt-5 flex items-center gap-2 text-[0.8125rem] text-ink-400 lg:hidden">
            <x-icon name="arrow-right" size="15" />
            مراحل را بکشید
        </p>
    </div>
</section>
