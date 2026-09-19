@php
    use App\Support\Jalali;
    use App\Support\Media;

    /*
    | پانل‌های کاروسل: نُه مرحله‌ی تولید، و در انتها کارتِ مقصد.
    |
    | tint وقتی به کار می‌آید که هنوز عکسی برای آن مرحله آپلود نشده — و در
    | دیتابیس تازه‌نصب، هیچ‌کدام عکس ندارند. طیف عمداً همان قوسی را می‌رود که
    | خود فرایند می‌رود: خاکِ سرد معدن، گرم‌شدن تدریجی، آتش کوره، و بعد
    | سرد شدن تا محصولِ تیره‌ی بسته‌بندی‌شده. رگه‌های مورب هم برای این است که
    | پانلِ بی‌عکس مثل یک مستطیل رنگیِ تخت دیده نشود.
    */
    $strata = 'repeating-linear-gradient(115deg, rgb(255 255 255 / 0.05) 0 2px, transparent 2px 17px)';

    $tints = collect([
        ['#7d7667', '#4b463d'],
        ['#8a7c66', '#57503f'],
        ['#9a8262', '#655640'],
        ['#ac8352', '#74532f'],
        ['#bd7f42', '#834c21'],
        ['#cf7a33', '#8f4517'],
        ['#e2732f', '#9a4524'],
        ['#a05a36', '#5c2815'],
        ['#514741', '#1f1c1a'],
    ])->map(fn ($pair) => $strata.', linear-gradient(155deg,'.$pair[0].','.$pair[1].')')->all();

    $slides = $processSteps->values()->map(fn ($step, $i) => [
        'label' => Jalali::digits($step->paddedNumber()),
        'title' => $step->title,
        'title_en' => $step->title_en,
        'description' => $step->description,
        'duration' => $step->duration,
        'metric_label' => $step->metric_label,
        'metric_value' => $step->metric_value,
        'image' => Media::url($step->image),
        'tint' => $tints[$i % count($tints)],
        'cta' => false,
    ])->push([
        'label' => '',
        'title' => 'و بعد، ساختمان',
        'title_en' => 'Output',
        'description' => 'هر پالتی که از این خط خارج می‌شود، شناسه‌ی بچ تولید دارد — تا اگر ده سال بعد پرسشی درباره‌ی آن پیش آمد، بتوانیم پاسخ بدهیم.',
        'duration' => null,
        'metric_label' => null,
        'metric_value' => null,
        'image' => null,
        'tint' => $strata.', linear-gradient(155deg,#2b2b2b,#0e0e0e)',
        'cta' => true,
    ]);

    $labels = $slides->pluck('label')->all();
    $total = Jalali::digits(str_pad((string) $processSteps->count(), 2, '0', STR_PAD_LEFT));
@endphp

{{--
    «از خاک تا سازه» — کاروسل فشرده.

    دسکتاپ: یک ردیف که پانلِ باز بیشترین جا را می‌گیرد و سه ستون بعدی به‌ترتیب
    باریک می‌شوند تا به تیغه‌های نازک ته صف برسند. با رفتن به مرحله‌ی بعد،
    مرحله‌ی قبل خودش به تیغه فشرده می‌شود و کنار لبه‌ی راست می‌ماند — پس دو سر
    نوار قرینه‌اند: راست، مسیرِ طی‌شده؛ چپ، مراحلِ نرسیده.

    موبایل: همان پانل‌ها به‌صورت ریل قابل swipe؛ متن زیرِ ریل با هر swipe
    عوض می‌شود. محتوا یکی است، فقط چیدمان فرق می‌کند.
--}}
<section class="relative overflow-hidden bg-sand-50 section" aria-labelledby="process-heading">

    <div class="container-page relative"
         x-data="squeezeCarousel(@js($labels))"
         x-id="['process']"
         style="--sq-h: clamp(190px, 26cqi, 320px); --sq-hero: calc(var(--sq-h) * 16 / 9); container-type: inline-size">

        {{-- سربرگ --}}
        <div class="flex flex-wrap items-end justify-between gap-x-6 gap-y-5">
            <div class="max-w-2xl">
                <p class="eyebrow text-clay-600" data-reveal>From earth to architecture</p>
                <h2 id="process-heading" class="mt-3 text-h2 font-extrabold text-balance" data-reveal>از خاک تا سازه</h2>
                <p class="mt-4 text-lead text-ink-500" data-reveal>
                    نُه مرحله، از برداشت خاک رس معدن تا پالت شرینک‌پیچ‌شده‌ی آماده‌ی بارگیری.
                </p>
            </div>

            <div class="flex items-center gap-5">
                {{-- شمارنده روی کارتِ پایانی عدد ندارد، پس محو می‌شود و جایش می‌ماند --}}
                <p class="tech hidden items-baseline gap-1.5 transition-opacity duration-300 sm:flex"
                   :class="label === '' && 'opacity-0'" aria-hidden="true">
                    <span class="text-4xl font-extrabold leading-none text-clay-500" x-text="label || '{{ $total }}'">{{ $labels[0] }}</span>
                    <span class="text-xl font-bold leading-none text-sand-300">/</span>
                    <span class="text-xl font-bold leading-none text-ink-400">{{ $total }}</span>
                </p>

                {{-- در RTL «بعدی» فلشِ چپ است --}}
                <div class="hidden gap-2 lg:flex">
                    <button type="button" @click="step(-1)" :disabled="open === 0"
                            class="tap-icon grid size-10 place-items-center rounded-full bg-ink-900 text-sand-50 transition hover:bg-clay-500 disabled:pointer-events-none disabled:opacity-25"
                            aria-label="مرحله‌ی قبل">
                        <x-icon name="arrow-right" size="17" />
                    </button>
                    <button type="button" @click="step(1)" :disabled="open === count - 1"
                            class="tap-icon grid size-10 place-items-center rounded-full bg-ink-900 text-sand-50 transition hover:bg-clay-500 disabled:pointer-events-none disabled:opacity-25"
                            aria-label="مرحله‌ی بعد">
                        <x-icon name="arrow-left" size="17" />
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-7 h-0.5 overflow-hidden rounded-full bg-sand-300">
            <div class="h-full origin-right bg-clay-500 transition-transform duration-700 ease-[var(--ease-out-expo)]"
                 :style="{ transform: `scaleX(${progress})` }"
                 style="transform: scaleX({{ round(1 / max(1, $slides->count()), 4) }})"></div>
        </div>

        {{-- نوار پانل‌ها --}}
        <div class="mt-7 lg:overflow-hidden">
            <ol x-ref="strip"
                @scroll.passive="onScroll"
                @keydown="onKey"
                @mouseleave="hover = -1"
                :style="stripStyle"
                role="tablist"
                aria-label="مراحل تولید"
                aria-orientation="horizontal"
                class="sq-strip -mx-5 flex h-[var(--sq-h)] snap-x snap-mandatory gap-3 overflow-x-auto overscroll-x-contain scroll-px-5 px-5 md:-mx-8 md:scroll-px-8 md:px-8 lg:mx-0 lg:w-full lg:gap-0 lg:overflow-visible lg:px-0 lg:snap-none">

                @foreach($slides as $i => $slide)
                    <li role="presentation"
                        :style="panelStyle({{ $i }})"
                        class="sq-panel bg-sand-200">

                        {{--
                            تصویر همیشه در یک بلوک ۱۶:۹ و وسط‌چین کشیده می‌شود، نه به
                            عرض پانل. اگر به عرض پانل بود، object-fit با باریک‌شدن
                            پانل مقیاس را عوض می‌کرد و عکس هر فریم دوباره نمونه‌برداری
                            می‌شد. یک بلوک یعنی یک مقیاس؛ پانل فقط تعیین می‌کند چقدر
                            از آن دیده شود.
                        --}}
                        @if($slide['image'])
                            <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}"
                                 loading="lazy" decoding="async" draggable="false"
                                 class="absolute inset-y-0 left-1/2 h-full max-w-none -translate-x-1/2 object-cover"
                                 style="width: var(--sq-hero); min-width: 100%">
                        @else
                            <span aria-hidden="true"
                                  class="absolute inset-y-0 left-1/2 -translate-x-1/2"
                                  style="width: var(--sq-hero); min-width: 100%; background: {{ $slide['tint'] }}"></span>
                        @endif

                        {{--
                            برچسب روی پانلِ باز؛ و روی هر پانلی که نشانگر رویش
                            است — وگرنه ستون‌های باریک بی‌نام‌اند و کاربر نمی‌داند
                            پیش از کلیک به کجا می‌رود.
                        --}}
                        <span aria-hidden="true"
                              class="pointer-events-none absolute inset-x-0 bottom-0 flex items-end gap-3 whitespace-nowrap bg-gradient-to-t from-black/60 to-transparent p-5 pt-16"
                              :style="{ opacity: open === {{ $i }} || hover === {{ $i }} ? 1 : 0, transition: 'opacity var(--sq-ms) var(--ease-out-expo)' }"
                              style="opacity: {{ $i === 0 ? 1 : 0 }}">
                            @if($slide['label'])
                                <span class="tech text-3xl font-extrabold leading-none text-white/45">{{ $slide['label'] }}</span>
                            @endif
                            <span class="text-card font-bold text-white">{{ $slide['title'] }}</span>
                        </span>

                        <button type="button" role="tab"
                                :id="$id('process') + '-tab-{{ $i }}'"
                                :aria-selected="open === {{ $i }} ? 'true' : 'false'"
                                :aria-controls="$id('process') + '-panel'"
                                :tabindex="open === {{ $i }} ? 0 : -1"
                                @click="select({{ $i }})"
                                @mousemove="hover = {{ $i }}"
                                @focus="go({{ $i }})"
                                class="absolute inset-0 z-10 h-full w-full cursor-pointer rounded-[inherit] outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-clay-500"
                                aria-label="{{ $slide['label'] ? 'مرحله‌ی '.$slide['label'].'، ' : '' }}{{ $slide['title'] }}"></button>
                    </li>
                @endforeach
            </ol>
        </div>

        {{--
            متنِ زیر نوار. همه‌ی پانل‌ها در یک خانه‌ی grid روی هم می‌نشینند تا
            ارتفاع بلندترینشان ثابت بماند و با عوض‌شدن مرحله، بقیه‌ی صفحه بالا
            و پایین نپرد.
        --}}
        <div :id="$id('process') + '-panel'" role="tabpanel" aria-live="polite" class="mt-6 grid lg:mt-7">
            @foreach($slides as $i => $slide)
                <div class="col-start-1 row-start-1 flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between lg:gap-12"
                     :aria-hidden="open === {{ $i }} ? 'false' : 'true'"
                     :style="{
                         opacity: open === {{ $i }} ? 1 : 0,
                         visibility: open === {{ $i }} ? 'visible' : 'hidden',
                         transition: 'opacity var(--sq-ms) var(--ease-out-expo), visibility var(--sq-ms)',
                     }"
                     style="{{ $i === 0 ? '' : 'opacity: 0; visibility: hidden;' }}">

                    <div class="max-w-2xl">
                        <h3 class="text-card font-bold">{{ $slide['title'] }}</h3>
                        <p class="tech mt-1 text-micro uppercase tracking-[0.14em] text-ink-300">{{ $slide['title_en'] }}</p>
                        <p class="mt-3 text-[0.9375rem] leading-[1.75] text-ink-500">{{ $slide['description'] }}</p>
                    </div>

                    @if($slide['cta'])
                        <div class="flex shrink-0 flex-wrap gap-2">
                            {{-- پانل‌های پنهان visibility: hidden دارند، پس از ترتیب Tab هم بیرون‌اند --}}
                            <x-cta :href="route('technology')" variant="primary" size="sm">جزئیات فناوری</x-cta>
                            <x-cta :href="route('factory')" variant="ghost" size="sm">بازدید از کارخانه</x-cta>
                        </div>
                    @elseif($slide['metric_value'] || $slide['duration'])
                        <dl class="flex shrink-0 flex-wrap items-center gap-2.5">
                            @if($slide['duration'])
                                <div class="flex items-center gap-2 rounded-full border border-sand-300 bg-sand-100 px-3.5 py-1.5">
                                    <x-icon name="clock" size="14" class="text-ink-400" />
                                    <dt class="sr-only">مدت</dt>
                                    <dd class="text-meta text-ink-500">{{ $slide['duration'] }}</dd>
                                </div>
                            @endif
                            @if($slide['metric_value'])
                                <div class="flex items-baseline gap-2 rounded-full border border-sand-300 bg-sand-100 px-3.5 py-1.5">
                                    <dt class="text-meta text-ink-400">{{ $slide['metric_label'] }}</dt>
                                    <dd class="text-[0.9375rem] font-extrabold text-clay-600"><x-num :value="$slide['metric_value']" /></dd>
                                </div>
                            @endif
                        </dl>
                    @endif
                </div>
            @endforeach
        </div>

        <p class="mt-5 flex items-center gap-2 text-meta text-ink-400 lg:hidden">
            <x-icon name="arrow-right" size="15" />
            مراحل را بکشید
        </p>
    </div>
</section>
