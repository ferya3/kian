{{--
    مدار مراحل تولید — فقط از lg به بالا.

    جای گره‌ها را Alpine حساب می‌کند، ولی محتوا همین‌جا در HTML است: بدون
    جاوااسکریپت هم هر نُه مرحله با عنوان و توضیح در صفحه هستند، فقط روی هم
    می‌نشینند. در همان حالت ریلِ موبایل هم نمایش داده می‌شود، پس چیزی گم نمی‌شود.

    هر گره button است نه div: با Tab می‌شود بینشان حرکت کرد و با Enter بازشان
    کرد. ورود فوکوس به مدار، چرخش را هم متوقف می‌کند — هدفی که زیر انگشت در
    حال حرکت باشد، با صفحه‌کلید قابل استفاده نیست.
--}}
<div class="container-page mt-6 hidden lg:block">
    {{--
        ورود ماوس چرخش را متوقف می‌کند. مرجع این را ندارد و همان‌جا هم مشکل
        دارد: هدفی که مدام جابه‌جا می‌شود سخت کلیک می‌شود. وقتی نشانگر روی
        مدار است یعنی کاربر دارد انتخاب می‌کند، پس حرکت باید بایستد.
    --}}
    <div x-data="orbitalTimeline({{ $processSteps->count() }})"
         x-on:click="close()"
         x-on:mouseenter="auto = false"
         x-on:mouseleave="if (active === null) auto = true"
         x-on:focusin="auto = false"
         x-on:focusout="if (active === null) auto = true"
         x-on:keydown.escape="close()"
         class="relative h-[40rem] select-none">

        <div x-ref="orbit" class="absolute inset-0 flex items-center justify-center">

            {{-- هسته: خاک رس که از مرکز خط تولید بیرون می‌آید --}}
            <div class="pointer-events-none absolute z-10 grid h-16 w-16 place-items-center rounded-full bg-gradient-to-br from-clay-400 via-clay-500 to-clay-700"
                 aria-hidden="true">
                <span class="absolute h-20 w-20 animate-ping rounded-full border border-clay-500/40 opacity-70"></span>
                <span class="absolute h-24 w-24 animate-ping rounded-full border border-clay-500/25 opacity-50"
                      style="animation-delay: .5s"></span>
                <span class="h-7 w-7 rounded-full bg-sand-50/85 backdrop-blur-md"></span>
            </div>

            {{-- خودِ مدار --}}
            <div class="pointer-events-none absolute rounded-full border border-ink-900/10"
                 :style="`width:${radius * 2}px; height:${radius * 2}px`" aria-hidden="true"></div>

            @foreach($processSteps as $index => $step)
                <div class="absolute transition-[opacity] duration-300"
                     :style="nodeStyle({{ $index }})">

                    <button type="button"
                            x-on:click.stop="toggle({{ $index }})"
                            :aria-expanded="active === {{ $index }} ? 'true' : 'false'"
                            class="tap-icon grid h-11 w-11 place-items-center rounded-full border-2 transition-all duration-300"
                            :class="active === {{ $index }}
                                ? 'scale-125 border-clay-500 bg-clay-500 text-white shadow-lift'
                                : (isNeighbour({{ $index }})
                                    ? 'border-clay-400 bg-clay-100 text-clay-700'
                                    : 'border-ink-900/15 bg-sand-50 text-ink-600 hover:border-clay-400')">
                        <x-icon :name="$stepIcons[$step->step_no] ?? 'grid'" size="17" />
                        <span class="sr-only">{{ $step->title }}</span>
                    </button>

                    <span class="pointer-events-none absolute right-1/2 top-12 translate-x-1/2 whitespace-nowrap text-micro font-bold tracking-wide transition-colors duration-300"
                          :class="active === {{ $index }} ? 'text-clay-700' : 'text-ink-500'"
                          aria-hidden="true">{{ $step->title }}</span>

                    {{--
                        کارت جزئیات. گره‌ی باز همیشه به بالای مدار می‌رود، پس
                        کارت رو به پایین و داخل مدار باز می‌شود و از کادر
                        بیرون نمی‌زند.
                    --}}
                    <div x-show="active === {{ $index }}" x-cloak
                         x-transition:enter="transition duration-300 ease-[var(--ease-out-expo)]"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:leave="transition duration-150"
                         x-transition:leave-end="opacity-0"
                         x-on:click.stop
                         class="absolute right-1/2 top-[4.75rem] w-72 translate-x-1/2 rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50/95 p-5 text-right shadow-float backdrop-blur-md">

                        <span class="absolute -top-3 right-1/2 h-3 w-px translate-x-1/2 bg-sand-300" aria-hidden="true"></span>

                        <div class="flex items-center justify-between gap-3">
                            <span class="tech rounded-full bg-ink-900 px-2.5 py-1 text-micro font-bold text-sand-50">
                                {{ \App\Support\Jalali::digits($step->paddedNumber()) }} / {{ \App\Support\Jalali::digits($processSteps->count()) }}
                            </span>
                            @if($step->duration && $step->duration !== '—')
                                <span class="flex items-center gap-1.5 text-micro text-ink-400">
                                    <x-icon name="clock" size="12" />
                                    {{ $step->duration }}
                                </span>
                            @endif
                        </div>

                        <h3 class="mt-3 text-card font-bold">{{ $step->title }}</h3>
                        <p class="tech text-micro uppercase tracking-[0.14em] text-ink-300">{{ $step->title_en }}</p>

                        <p class="mt-3 text-[0.9375rem] leading-relaxed text-ink-500">{{ $step->description }}</p>

                        {{--
                            نوار پیشرفت، جای «energy» مرجع را گرفته: آنجا عددی
                            دلخواه بود، اینجا واقعاً می‌گوید این مرحله کجای خط
                            تولید است.
                        --}}
                        <div class="mt-4 border-t border-sand-300 pt-3">
                            <div class="flex items-baseline justify-between text-micro">
                                <span class="text-ink-400">پیشرفت خط تولید</span>
                                <span class="tech font-bold text-clay-600">{{ \App\Support\Jalali::digits(round($step->step_no / $processSteps->count() * 100)) }}٪</span>
                            </div>
                            <div class="mt-1.5 h-1 overflow-hidden rounded-full bg-sand-300">
                                <div class="h-full rounded-full bg-gradient-to-l from-clay-400 to-clay-600"
                                     style="width: {{ round($step->step_no / $processSteps->count() * 100) }}%"></div>
                            </div>
                        </div>

                        @if($step->metric_value)
                            <dl class="mt-3 flex items-baseline justify-between">
                                <dt class="text-micro text-ink-400">{{ $step->metric_label }}</dt>
                                <dd class="text-card font-extrabold text-clay-600"><x-num :value="$step->metric_value" /></dd>
                            </dl>
                        @endif

                        {{-- مرحله‌ی قبل و بعد: خط تولید زنجیره است، نه شبکه --}}
                        <div class="mt-4 flex flex-wrap gap-1.5 border-t border-sand-300 pt-3">
                            @foreach([$index - 1, $index + 1] as $sibling)
                                @continue(! isset($processSteps[$sibling]))
                                <button type="button" x-on:click.stop="toggle({{ $sibling }})"
                                        class="tap inline-flex items-center gap-1 rounded-full border border-sand-300 px-2.5 text-micro text-ink-500 transition hover:border-clay-400 hover:text-clay-700 lg:min-h-8">
                                    {{ $processSteps[$sibling]->title }}
                                    <x-icon name="{{ $sibling > $index ? 'arrow-left' : 'arrow-right' }}" size="11" />
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
