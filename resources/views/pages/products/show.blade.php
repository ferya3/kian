<x-layouts.app>
    @php
        $benefits = [
            ['icon' => 'thermal',  'en' => 'Thermal',    'label' => 'عایق حرارتی',
             'value' => 'λ = '.$product->thermal_conductivity,
             'note' => 'ضریب هدایت حرارتی بر حسب وات بر متر کلوین'],
            ['icon' => 'acoustic', 'en' => 'Acoustic',   'label' => 'عایق صوتی',
             'value' => $product->sound_reduction_db.' dB',
             'note' => 'کاهش صوت هوابرد در دیوار اجراشده'],
            ['icon' => 'fire',     'en' => 'Fire',       'label' => 'مقاومت آتش',
             'value' => $product->fire_resistance_min.' دقیقه',
             'note' => 'ماده‌ی غیرقابل اشتعال، بدون انتشار گاز سمی'],
        ];
    @endphp

    {{-- ==================== سربرگ محصول ==================== --}}
    <section class="relative overflow-hidden bg-sand-50 pb-16 pt-10 lg:pb-20">
        <div class="pointer-events-none absolute inset-0 opacity-60" aria-hidden="true"
             style="background: radial-gradient(60% 55% at 78% 8%, rgba(180,85,45,.14), transparent 62%)"></div>

        <div class="container-page relative">
            <x-breadcrumbs />

            <div class="mt-8 grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-14">

                {{-- نمایشگر محصول --}}
                <div class="lg:col-span-6"
                     x-data="blockViewer(@js($product->cavities->map(fn($c) => [
                        'label' => $c->label,
                        'description' => $c->description,
                        'metricLabel' => $c->metric_label,
                        'metricValue' => $c->metric_value,
                     ])->all()))">

                    <div class="relative overflow-hidden rounded-[var(--radius-panel)] border border-sand-300 bg-gradient-to-bl from-sand-200 via-sand-100 to-sand-300">
                        <div class="absolute right-5 top-5 z-10 flex rounded-full border border-ink-900/10 bg-sand-50/90 p-1 text-meta backdrop-blur"
                             role="tablist" aria-label="نمای محصول">
                            <button type="button" role="tab" @click="mode = 'solid'" :aria-selected="mode === 'solid'"
                                    class="tap rounded-full px-4 py-2 font-semibold transition"
                                    :class="mode === 'solid' ? 'bg-ink-900 text-sand-50' : 'text-ink-500 hover:text-ink-900'">نمای حجمی</button>
                            <button type="button" role="tab" @click="mode = 'section'" :aria-selected="mode === 'section'"
                                    class="tap rounded-full px-4 py-2 font-semibold transition"
                                    :class="mode === 'section' ? 'bg-ink-900 text-sand-50' : 'text-ink-500 hover:text-ink-900'">مقطع داخلی</button>
                        </div>

                        <div class="grid min-h-[24rem] place-items-center p-8 lg:min-h-[28rem]">
                            <div x-show="mode === 'solid'" x-transition.opacity.duration.300ms class="w-full">
                                <x-block-3d :product="$product" :size="300" />
                                <p class="mt-8 text-center text-meta text-ink-400">
                                    برای چرخاندن بکشید — یا از کلیدهای جهت استفاده کنید
                                </p>
                            </div>

                            <div x-show="mode === 'section'" x-cloak x-transition.opacity.duration.300ms class="w-full">
                                <x-block-section :product="$product" />
                                <p class="mt-5 text-center text-meta text-ink-400">
                                    روی نقاط مقطع کلیک کنید
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- توضیح نقطه‌ی انتخاب‌شده روی مقطع --}}
                    <div x-show="mode === 'section'" x-cloak class="mt-4 rounded-2xl border border-sand-300 bg-sand-100 p-5">
                        <template x-if="cavity">
                            <div>
                                <p class="font-bold text-clay-700" x-text="cavity.label"></p>
                                <p class="mt-1.5 leading-relaxed text-ink-500" x-text="cavity.description"></p>
                                <p class="tech mt-3 flex items-baseline gap-2 border-t border-sand-300 pt-3">
                                    <span class="text-meta text-ink-400" x-text="cavity.metricLabel"></span>
                                    <span class="font-bold" x-text="cavity.metricValue"></span>
                                </p>
                            </div>
                        </template>
                        <p x-show="!cavity" class="text-[0.9375rem] text-ink-400">
                            نقاط روشن روی مقطع، ساختار داخلی بلوک را توضیح می‌دهند.
                        </p>
                    </div>
                </div>

                {{-- اطلاعات و مشخصات --}}
                <div class="lg:col-span-6">
                    <p class="eyebrow text-clay-600">{{ $product->category?->name }}</p>

                    <div class="mt-3 flex flex-wrap items-baseline gap-x-4 gap-y-2">
                        <h1 class="text-h1 font-extrabold">{{ $product->name }}</h1>
                        <span class="tech rounded-full border border-sand-300 bg-sand-100 px-3 py-1 text-sm font-semibold text-ink-500">{{ $product->sku }}</span>
                    </div>

                    <p class="tech mt-2 text-lg text-ink-400">
                        {{ \App\Support\Jalali::digits($product->dimensionLabel()) }} سانتی‌متر
                    </p>

                    <p class="mt-5 text-lead text-ink-600">{{ $product->summary }}</p>

                    {{-- سه مزیت اصلی --}}
                    <ul class="mt-8 grid grid-cols-3 gap-3">
                        @foreach($benefits as $benefit)
                            <li class="rounded-2xl border border-sand-300 bg-sand-100 p-4 text-center">
                                <x-icon :name="$benefit['icon']" size="22" class="mx-auto text-clay-500" />
                                <p class="tech mt-2 text-micro uppercase tracking-[0.12em] text-ink-300">{{ $benefit['en'] }}</p>
                                <p class="mt-1 font-extrabold"><x-num :value="$benefit['value']" /></p>
                                <p class="mt-1 text-micro leading-snug text-ink-400">{{ $benefit['label'] }}</p>
                            </li>
                        @endforeach
                    </ul>

                    {{-- جدول مشخصات فنی --}}
                    <h2 class="mt-9 flex items-center gap-2 text-lg font-bold">
                        <x-icon name="ruler" size="19" class="text-clay-500" />
                        مشخصات فنی
                    </h2>
                    <x-spec-table :product="$product" class="mt-4" dense />

                    {{-- اقدام --}}
                    <div class="mt-7 flex flex-wrap gap-3">
                        @php $datasheet = $product->documents->firstWhere('category', 'datasheet'); @endphp
                        @if($datasheet)
                            <x-cta :href="route('documents.download', $datasheet)" variant="dark" icon="download">
                                دانلود دیتاشیت
                            </x-cta>
                        @endif
                        <x-cta :href="route('contact', ['type' => 'quote', 'product' => $product->id])" variant="primary">
                            درخواست قیمت
                        </x-cta>
                        <x-cta :href="route('contact', ['type' => 'technical', 'product' => $product->id])" variant="ghost">
                            مشاوره فنی
                        </x-cta>
                    </div>

                    @if($product->standards)
                        <ul class="mt-6 flex flex-wrap gap-2 border-t border-sand-200 pt-5">
                            @foreach($product->standards as $standard)
                                <li class="flex items-center gap-1.5 rounded-full bg-sand-200 px-3 py-1.5 text-meta text-ink-600">
                                    <x-icon name="check" size="14" class="text-clay-500" />
                                    {{ $standard }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== عملکرد و کاربرد ==================== --}}
    <section class="bg-ink-950 py-16 text-sand-50 lg:py-20">
        <div class="container-page grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-14">
            <div class="lg:col-span-5">
                <h2 class="text-h2 font-extrabold">چرا این محصول؟</h2>
                <p class="mt-4 leading-relaxed text-sand-200/65">{{ $product->description ?: $product->summary }}</p>

                <x-perf-bars :product="$product" light class="mt-8" />
            </div>

            <div class="lg:col-span-7">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-6">
                        <h3 class="flex items-center gap-2 font-bold">
                            <x-icon name="sparkle" size="18" class="text-clay-400" />
                            ویژگی‌ها
                        </h3>
                        <ul class="mt-4 space-y-2.5">
                            @foreach($product->features ?? [] as $feature)
                                <li class="flex gap-2.5 text-[0.9375rem] leading-relaxed text-sand-200/75">
                                    <x-icon name="check" size="16" class="mt-1 shrink-0 text-clay-400" />
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-6">
                        <h3 class="flex items-center gap-2 font-bold">
                            <x-icon name="compass" size="18" class="text-clay-400" />
                            کاربردها
                        </h3>
                        <ul class="mt-4 space-y-2.5">
                            @foreach($product->applications ?? [] as $application)
                                <li class="flex gap-2.5 text-[0.9375rem] leading-relaxed text-sand-200/75">
                                    <x-icon name="check" size="16" class="mt-1 shrink-0 text-clay-400" />
                                    {{ $application }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                @if($product->solutions->isNotEmpty())
                    <div class="mt-4 rounded-2xl border border-white/10 bg-gradient-to-bl from-clay-900/50 to-transparent p-6">
                        <h3 class="font-bold">راهکارهایی که این محصول در آن‌ها به‌کار می‌رود</h3>
                        <ul class="mt-4 flex flex-wrap gap-2">
                            @foreach($product->solutions as $solution)
                                <li>
                                    <a href="{{ route('solutions.show', $solution) }}"
                                       class="inline-flex items-center gap-1.5 rounded-full border border-white/15 px-4 py-2 text-[0.875rem] transition hover:border-clay-400 hover:text-clay-300">
                                        {{ $solution->title }}
                                        <x-icon name="arrow-left" size="14" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ==================== فایل‌های فنی ==================== --}}
    @if($product->documents->isNotEmpty())
        <section class="bg-sand-100 py-16 lg:py-20">
            <div class="container-page">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <x-section-heading eyebrow="Downloads" title="فایل‌های فنی این محصول"
                        lead="دیتاشیت، مقطع DWG و آبجکت‌های BIM با پارامترهای حرارتی و صوتی." class="lg:max-w-2xl" />
                    <div data-reveal>
                        <x-cta :href="route('technical.downloads')" variant="ghost" size="sm">مرکز دانلود</x-cta>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4" data-reveal-stagger="70">
                    @foreach($product->documents as $document)
                        <x-document-row :document="$document" data-reveal />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ==================== روش اجرا ==================== --}}
    <section class="bg-sand-50 py-16 lg:py-20">
        <div class="container-page grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-14">
            <div class="lg:col-span-4">
                <x-section-heading eyebrow="Installation" title="روش اجرا"
                    lead="پنج نکته‌ای که بیشترین تأثیر را روی عملکرد نهایی دیوار دارد." />
                <x-cta :href="route('technical.installation')" variant="ghost" class="mt-7">راهنمای کامل اجرا</x-cta>
            </div>

            <ol class="lg:col-span-8" data-reveal-stagger="80">
                @foreach([
                    ['بستر را تراز کنید', 'رگ اول روی بستر ملاتی کاملاً تراز اجرا می‌شود. هر میلی‌متر انحراف در رگ اول، در ارتفاع دیوار چند برابر می‌شود.'],
                    ['بلوک را مرطوب کنید، نه اشباع', 'سطح بلوک باید نم‌دار باشد تا آب ملات را نمکد. بلوک خیس، برعکس، چسبندگی را از بین می‌برد.'],
                    ['درز افقی را کامل پر کنید', 'درز ناقص هم مقاومت را کم می‌کند و هم مسیر عبور صوت و حرارت می‌سازد. با درز نر و ماده، درز قائم حذف می‌شود.'],
                    ['بازشو بدون نعل درگاه اجرا نشود', 'حتی دهانه‌های کوچک نیاز به نعل درگاه دارند. نعل درگاه سفالی، پیوستگی حرارتی نما را هم حفظ می‌کند.'],
                    ['حداکثر یک و نیم متر در روز', 'اجرای بلندتر در یک نوبت، ملات تازه‌ی رگ‌های پایین را می‌فشارد و شاقولی دیوار را به هم می‌زند.'],
                ] as $i => [$title, $text])
                    <li data-reveal class="flex gap-5 border-b border-sand-200 py-5 first:pt-0 last:border-0">
                        <span class="tech grid h-9 w-9 shrink-0 place-items-center rounded-full bg-clay-100 text-sm font-bold text-clay-700">
                            {{ \App\Support\Jalali::digits($i + 1) }}
                        </span>
                        <div>
                            <h3 class="font-bold">{{ $title }}</h3>
                            <p class="mt-1.5 leading-relaxed text-ink-500">{{ $text }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ==================== پروژه‌ها ==================== --}}
    @if($product->projects->isNotEmpty())
        <section class="bg-sand-100 py-16 lg:py-20">
            <div class="container-page">
                <x-section-heading eyebrow="Used in" title="پروژه‌هایی که با این محصول اجرا شده‌اند" />
                <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" data-reveal-stagger="100">
                    @foreach($product->projects->take(3) as $project)
                        <x-project-card :project="$project" data-reveal />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- نوار اقدام چسبان موبایل — تماس و استعلام همیشه در دسترس --}}
    @php $datasheetDoc = $product->documents->firstWhere('category', 'datasheet'); @endphp
    <x-mobile-action-bar
        :primary-href="route('contact', ['type' => 'quote', 'product' => $product->id])"
        primary-label="استعلام قیمت"
        :secondary-href="$datasheetDoc ? route('documents.download', $datasheetDoc) : null"
        secondary-label="دیتاشیت" />

    {{-- ==================== محصولات مشابه ==================== --}}
    <section class="bg-sand-50 py-16 lg:py-20">
        <div class="container-page">
            <x-section-heading eyebrow="Related" title="محصولات مشابه" />
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" data-reveal-stagger="100">
                @foreach($related as $item)
                    <x-product-card :product="$item" data-reveal />
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.app>
