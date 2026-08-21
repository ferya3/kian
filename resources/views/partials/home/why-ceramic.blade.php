@php
    $pillars = [
        ['key' => 'thermal',   'icon' => 'thermal',  'en' => 'Thermal Insulation', 'title' => 'عایق حرارتی',
         'text' => 'هوای ساکن محبوس در حفره‌های چندردیفه، عایقی است که خودِ ماده می‌سازد. هرچه ردیف‌ها بیشتر و مسیر انتقال طولانی‌تر، ضریب λ کمتر.',
         'metric' => 'λ از ۰٫۱۷', 'metricLabel' => 'وات بر متر کلوین'],
        ['key' => 'acoustic',  'icon' => 'acoustic', 'en' => 'Acoustic Performance', 'title' => 'عملکرد صوتی',
         'text' => 'ترکیب جرم حجمی سفال با حفره‌های هوا، هم صدای هوابرد را میرا می‌کند و هم بدون بار اضافی روی سازه اجرا می‌شود.',
         'metric' => 'تا ۵۵', 'metricLabel' => 'دسی‌بل کاهش صوت'],
        ['key' => 'fire',      'icon' => 'fire',     'en' => 'Fire Resistance', 'title' => 'مقاومت در برابر آتش',
         'text' => 'سفال در نهصد درجه پخته شده؛ چیزی برای سوختن باقی نمانده. غیرقابل اشتعال است و در حریق گاز سمی منتشر نمی‌کند.',
         'metric' => 'تا ۲۴۰', 'metricLabel' => 'دقیقه مقاومت آتش'],
        ['key' => 'durability','icon' => 'shield',   'en' => 'Durability', 'title' => 'دوام',
         'text' => 'جمع‌شدگی بلندمدت ندارد، پوسیده نمی‌شود و در برابر رطوبت و یخبندان پایدار می‌ماند. عمرش برابر عمر ساختمان است.',
         'metric' => '۵۰+', 'metricLabel' => 'سال عمر مفید'],
        ['key' => 'natural',   'icon' => 'leaf',     'en' => 'Natural Material', 'title' => 'ماده‌ی طبیعی',
         'text' => 'خاک، آب، آتش. بدون افزودنی شیمیایی پایدار، بدون انتشار ترکیبات فرار — و در پایان عمر ساختمان، قابل خردایش و بازگشت به چرخه.',
         'metric' => '۱۰۰٪', 'metricLabel' => 'ماده اولیه معدنی'],
    ];
@endphp

<section class="relative overflow-hidden bg-ink-950 py-20 text-sand-50 lg:py-28" aria-labelledby="why-heading">
    <div class="pointer-events-none absolute -right-40 top-0 h-[40rem] w-[40rem] rounded-full opacity-50 blur-[120px]"
         style="background: radial-gradient(circle, rgba(180,85,45,.35), transparent 65%)"></div>

    <div class="container-page relative">
        <x-section-heading
            eyebrow="Why ceramic?"
            title="چرا سفال؟"
            lead="پنج ویژگی که هیچ‌کدام افزودنی نیستند — همه از خودِ ماده و هندسه‌ی بلوک می‌آیند."
            light id="why-heading" />

        <div class="mt-14 grid gap-10 lg:grid-cols-12 lg:items-start lg:gap-14">

            {{-- بلوک تعاملی --}}
            <div class="lg:col-span-5 lg:sticky lg:top-28" x-data="blockViewer(@js($interactiveProduct?->cavities->map(fn($c) => [
                    'label' => $c->label,
                    'description' => $c->description,
                    'metricLabel' => $c->metric_label,
                    'metricValue' => $c->metric_value,
                ])->all() ?? []))">

                @if($interactiveProduct)
                    <div class="rounded-[var(--radius-panel)] border border-white/10 bg-white/[0.04] p-6 backdrop-blur-sm">

                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="eyebrow text-clay-400">Interactive</p>
                                <p class="mt-1 font-bold">{{ $interactiveProduct->name }}</p>
                            </div>

                            <div role="tablist" aria-label="نمای بلوک"
                                 class="flex rounded-full border border-white/12 bg-ink-950/50 p-1 text-[0.8125rem]">
                                <button type="button" role="tab" @click="mode = 'solid'"
                                        :aria-selected="mode === 'solid'"
                                        class="rounded-full px-3.5 py-1.5 transition"
                                        :class="mode === 'solid' ? 'bg-clay-500 text-white' : 'text-sand-200/60 hover:text-sand-50'">حجم</button>
                                <button type="button" role="tab" @click="mode = 'section'"
                                        :aria-selected="mode === 'section'"
                                        class="rounded-full px-3.5 py-1.5 transition"
                                        :class="mode === 'section' ? 'bg-clay-500 text-white' : 'text-sand-200/60 hover:text-sand-50'">مقطع</button>
                            </div>
                        </div>

                        <div class="relative mt-6 min-h-[16rem]">
                            <div x-show="mode === 'solid'" x-transition.opacity.duration.300ms class="py-6">
                                <x-block-3d :product="$interactiveProduct" :size="230" />
                                <p class="mt-6 text-center text-xs text-sand-200/45">
                                    بکشید تا بچرخد — یا با کلیدهای جهت
                                </p>
                            </div>

                            <div x-show="mode === 'section'" x-cloak x-transition.opacity.duration.300ms>
                                <x-block-section :product="$interactiveProduct" />

                                <div class="mt-5 min-h-[6.5rem] rounded-xl border border-white/10 bg-ink-950/60 p-4">
                                    <template x-if="cavity">
                                        <div>
                                            <p class="font-bold text-clay-300" x-text="cavity.label"></p>
                                            <p class="mt-1.5 text-[0.875rem] leading-relaxed text-sand-200/70" x-text="cavity.description"></p>
                                            <p class="tech mt-3 flex items-baseline gap-2 border-t border-white/10 pt-3 text-sm">
                                                <span class="text-sand-200/50" x-text="cavity.metricLabel"></span>
                                                <span class="font-bold text-sand-50" x-text="cavity.metricValue"></span>
                                            </p>
                                        </div>
                                    </template>
                                    <p x-show="!cavity" class="text-[0.875rem] text-sand-200/50">
                                        روی نقاط روشن مقطع کلیک کنید تا ساختار داخلی بلوک را ببینید.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ستون‌های ویژگی --}}
            <ol class="lg:col-span-7" data-reveal-stagger="90">
                @foreach($pillars as $i => $pillar)
                    <li data-reveal
                        class="group grid grid-cols-[auto_1fr] gap-5 border-b border-white/[0.08] py-7 first:pt-0 last:border-0">
                        <span class="grid h-12 w-12 place-items-center rounded-xl border border-white/10 bg-white/[0.05] text-clay-400 transition-colors duration-500 group-hover:border-clay-500/40 group-hover:bg-clay-500/15 group-hover:text-clay-300">
                            <x-icon :name="$pillar['icon']" size="22" />
                        </span>

                        <div>
                            <div class="flex flex-wrap items-baseline gap-x-3">
                                <h3 class="text-h3 font-bold text-sand-50">{{ $pillar['title'] }}</h3>
                                <span class="tech text-[0.6875rem] uppercase tracking-[0.16em] text-sand-200/35">{{ $pillar['en'] }}</span>
                            </div>

                            <p class="mt-2.5 max-w-xl leading-relaxed text-sand-200/65">{{ $pillar['text'] }}</p>

                            <p class="mt-4 flex items-baseline gap-2">
                                <span class="tech text-2xl font-extrabold text-clay-300">{{ $pillar['metric'] }}</span>
                                <span class="text-[0.8125rem] text-sand-200/45">{{ $pillar['metricLabel'] }}</span>
                            </p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>
