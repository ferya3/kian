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
        ['key' => 'weight',    'icon' => 'weight',   'en' => 'Reduced Dead Load', 'title' => 'سبکی و بار مرده',
         'text' => 'نیروی جانبی زلزله متناسب با جرم سازه است. دیوار سبک‌تر یعنی برش پایه‌ی کمتر — و در اضافه طبقه و مقاوم‌سازی، همین چند درصد تعیین‌کننده است.',
         'metric' => 'تا ۲۸٪', 'metricLabel' => 'کاهش وزن دیوار'],
        ['key' => 'durability','icon' => 'shield',   'en' => 'Durability', 'title' => 'دوام',
         'text' => 'جمع‌شدگی بلندمدت ندارد، پوسیده نمی‌شود و در برابر رطوبت و یخبندان پایدار می‌ماند. عمرش برابر عمر ساختمان است.',
         'metric' => '۵۰+', 'metricLabel' => 'سال عمر مفید'],
        ['key' => 'natural',   'icon' => 'leaf',     'en' => 'Natural Material', 'title' => 'ماده‌ی طبیعی',
         'text' => 'خاک، آب، آتش. بدون افزودنی شیمیایی پایدار، بدون انتشار ترکیبات فرار — و در پایان عمر ساختمان، قابل خردایش و بازگشت به چرخه.',
         'metric' => '۱۰۰٪', 'metricLabel' => 'ماده اولیه معدنی'],
    ];

    // سه‌تای اول سمت راستِ بلوک می‌نشینند و سه‌تای دوم سمت چپ
    $rightPillars = array_slice($pillars, 0, 3);
    $leftPillars = array_slice($pillars, 3);
@endphp

<section class="relative overflow-hidden bg-ink-950 py-20 text-sand-50 lg:py-28" aria-labelledby="why-heading">
    <div class="pointer-events-none absolute -right-40 top-0 h-[40rem] w-[40rem] rounded-full opacity-50 blur-[120px]"
         style="background: radial-gradient(circle, rgba(180,85,45,.35), transparent 65%)"></div>

    <div class="container-page relative">
        <x-section-heading
            eyebrow="Why ceramic?"
            title="چرا سفال؟"
            lead="شش ویژگی که هیچ‌کدام افزودنی نیستند — همه از خودِ ماده و هندسه‌ی بلوک می‌آیند."
            light id="why-heading" />

        {{--
            بلوک تعاملی وسط می‌نشیند و ویژگی‌ها دو طرفش. در RTL اولین آیتم گرید
            سمت راست می‌افتد، پس ترتیب سورس یعنی: سه ویژگی راست، بلوک، سه ویژگی چپ.

            زیر lg همه‌چیز تک‌ستونی می‌شود و بلوک با order اول می‌آید: روی گوشی،
            نمایشگر تعاملی نباید وسط فهرست دفن شود.
        --}}
        {{--
            فاصله‌ی عمودی صفر است: زیر lg دو فهرست پشت سر هم می‌آیند و باید یک
            فهرست پیوسته دیده شوند، نه دو بلوک با شکاف وسطشان. جدایی بلوک
            تعاملی با margin خودش تأمین می‌شود.
        --}}
        <div @class([
            'mt-14 grid grid-cols-1 gap-y-0 lg:items-start lg:gap-8 xl:gap-12',
            'lg:grid-cols-[1fr_minmax(0,24rem)_1fr]' => $interactiveProduct,
            'lg:grid-cols-2' => ! $interactiveProduct,
        ])>

            {{-- خط پایانی فقط روی دسکتاپ برداشته می‌شود؛ زیر lg ادامه‌ی فهرست است --}}
            <ul data-reveal-stagger="90" class="lg:[&>li:last-child]:border-b-0">
                @foreach($rightPillars as $pillar)
                    @include('partials.home.pillar', ['pillar' => $pillar])
                @endforeach
            </ul>

            @if($interactiveProduct)
                <div class="order-first mb-10 lg:order-none lg:mb-0 lg:sticky lg:top-28"
                     x-data="blockViewer(@js($interactiveProduct->cavities->map(fn($c) => [
                        'label' => $c->label,
                        'description' => $c->description,
                        'metricLabel' => $c->metric_label,
                        'metricValue' => $c->metric_value,
                     ])->all()))">

                    <div class="rounded-[var(--radius-panel)] border border-white/10 bg-white/[0.04] p-5 backdrop-blur-sm lg:p-6">

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="eyebrow text-clay-400">Interactive</p>
                                <p class="mt-1 font-bold">{{ $interactiveProduct->name }}</p>
                            </div>

                            <div role="tablist" aria-label="نمای بلوک"
                                 class="flex rounded-full border border-white/12 bg-ink-950/50 p-1 text-meta">
                                <button type="button" role="tab" @click="mode = 'solid'"
                                        :aria-selected="mode === 'solid'"
                                        class="tap rounded-full px-4 py-2 transition"
                                        :class="mode === 'solid' ? 'bg-clay-500 text-white' : 'text-sand-200/60 hover:text-sand-50'">حجم</button>
                                <button type="button" role="tab" @click="mode = 'section'"
                                        :aria-selected="mode === 'section'"
                                        class="tap rounded-full px-4 py-2 transition"
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
                </div>
            @endif

            <ul data-reveal-stagger="90" class="[&>li:last-child]:border-b-0">
                @foreach($leftPillars as $pillar)
                    @include('partials.home.pillar', ['pillar' => $pillar])
                @endforeach
            </ul>

        </div>
    </div>
</section>
