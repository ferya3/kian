@php
    use App\Models\SiteMedia;
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

    /*
    | آیکون هر مرحله. به ترتیبِ مرحله بسته شده و نه به اسمش، چون اسم را مدیر
    | می‌تواند عوض کند؛ اگر روزی مرحله‌ای اضافه شود، فهرست دور می‌زند.
    */
    $icons = ['layers', 'compass', 'trowel', 'blueprint', 'ruler', 'thermal', 'fire', 'check', 'grid'];

    /*
    | جمله‌ی اول شرح.
    |
    | روی کارتِ مربع جا برای پاراگراف نیست، و بریدنِ متن وسط جمله «...» به جا
    | می‌گذارد که مثل خطا دیده می‌شود. یک جمله‌ی کامل می‌ماند و شرحِ کامل سر
    | جایش در صفحه‌های فناوری و کارخانه هست.
    |
    | «:» و «؛» هم پایانِ جمله حساب می‌شوند و جایشان نقطه می‌نشیند. آنچه پیش
    | از دونقطه می‌آید در فارسی جمله‌ی مستقلی است که فهرست را معرفی می‌کند، و
    | بدون این، شرحِ «کنترل کیفیت» — که یک جمله‌ی بلندِ بی‌نقطه است — تمام
    | کارت را روی گوشی می‌پوشاند.
    */
    $lead = function (?string $text): string {
        $text = trim((string) $text);

        $cuts = collect(['. ', ': ', '؛ '])
            ->map(fn (string $mark) => mb_strpos($text, $mark))
            ->filter(fn ($at) => $at !== false);

        return $cuts->isEmpty() ? $text : mb_substr($text, 0, $cuts->min()).'.';
    };

    $slides = $processSteps->values()->map(fn ($step, $i) => [
        'label' => Jalali::digits($step->paddedNumber()),
        'title' => $step->title,
        'title_en' => $step->title_en,
        'summary' => $lead($step->description ?: $step->summary),
        'metric_label' => $step->metric_label,
        'metric_value' => $step->metric_value,
        'duration' => $step->duration,
        'icon' => $icons[$i % count($icons)],
        'image' => Media::url($step->image),
        'alt' => $step->title,
        'tint' => $tints[$i % count($tints)],
        'cta' => false,
    ])->push([
        'label' => '',
        'title' => 'و بعد، ساختمان',
        'title_en' => 'Output',
        'summary' => 'هر پالتی که از این خط خارج می‌شود شناسه‌ی بچ تولید دارد — تا ده سال بعد هم قابل ردیابی باشد.',
        'metric_label' => null,
        'metric_value' => null,
        'duration' => null,
        'icon' => 'factory',
        'image' => SiteMedia::url('process.outcome'),
        'alt' => SiteMedia::alt('process.outcome', 'ساختمان اجراشده با بلوک سفالی کیان'),
        'tint' => $strata.', linear-gradient(155deg,#2b2b2b,#0e0e0e)',
        'cta' => true,
    ]);
@endphp

{{--
    «از خاک تا سازه» — کاروسل دو‌پانله.

    سمت راست چرخی از چیپ‌هاست که عمودی می‌چرخد و مرحله‌ی فعال وسطش می‌نشیند؛
    سمت چپ دسته‌ای از کارت‌ها که مرحله‌ی فعال رو به جلو می‌آید و همسایه‌هایش
    کمی کوچک و کج پشتش می‌مانند. خودش هر چند ثانیه جلو می‌رود و با رسیدن
    نشانگر یا فوکوس می‌ایستد.

    روی گوشی همان دو پانل روی هم می‌نشینند: چرخ بالا، دسته پایین.
--}}
<section class="bg-sand-50 section" aria-labelledby="process-heading">
    <div class="container-page">

        <div class="max-w-2xl">
            <p class="eyebrow text-clay-600" data-reveal>From earth to architecture</p>
            <h2 id="process-heading" class="mt-3 text-h2 font-extrabold text-balance" data-reveal>از خاک تا سازه</h2>
            <p class="mt-4 text-lead text-ink-500" data-reveal>
                نُه مرحله، از برداشت خاک رس معدن تا پالت شرینک‌پیچ‌شده‌ی آماده‌ی بارگیری.
            </p>
        </div>

        <div class="mt-9"
             x-data="featureCarousel({{ $slides->count() }})"
             x-id="['process']"
             @mouseenter="paused = true"
             @mouseleave="paused = false"
             @focusin="paused = true"
             @focusout="paused = false">

            <div class="flex min-h-[34rem] flex-col overflow-hidden rounded-[2rem] border border-sand-300 lg:min-h-0 lg:flex-row lg:rounded-[2.5rem] lg:aspect-[2/1]">

                {{-- چرخِ مرحله‌ها --}}
                {{--
                    چرخ با inset-0 کشیده می‌شود و نه با h-full: چیپ‌ها همه
                    absolute‌اند، پس ارتفاعِ محتوا صفر است و درصدْ چیزی برای
                    اندازه‌گیری ندارد. روی گوشی همین باعث می‌شد چرخ اصلاً
                    ارتفاع نگیرد.
                --}}
                <div class="relative min-h-[20rem] w-full bg-clay-600 lg:w-[46%] lg:min-h-0">
                    {{-- تابشِ ملایم از بالا، تا بلوک رنگی تخت نباشد --}}
                    <div class="pointer-events-none absolute inset-0" aria-hidden="true"
                         style="background: radial-gradient(90% 60% at 30% 0%, rgb(255 255 255 / 0.16), transparent 70%)"></div>

                    <div class="fc-wheel absolute inset-0 flex items-center px-6 md:px-10 lg:px-9"
                         x-ref="wheel" @keydown="onKey"
                         role="tablist" aria-label="مراحل تولید" aria-orientation="vertical">
                        @foreach($slides as $i => $slide)
                            {{--
                                هر پله‌ی چرخ یک ردیف تمام‌عرض است: بیضی، و روبه‌رویش
                                شرح همان مرحله.

                                شرح بلندتر از ۶۴ پیکسلِ ردیف است و از بالا و پایین
                                بیرون می‌زند — اشکالی ندارد، چون ردیف‌های همسایه
                                شرحِ نامرئی دارند و چیزی آنجا دیده نمی‌شود. شفافیتِ
                                خودِ شرح جداست و نه ارثی از ردیف، وگرنه شرحِ
                                همسایه‌ها هم نیمه‌جان پیدا می‌شد.
                            --}}
                            <div class="fc-chip absolute inset-x-0 flex items-center gap-4 md:gap-5" style="height: 64px" :style="chipStyle({{ $i }})">
                                <button type="button" role="tab"
                                        :id="$id('process') + '-tab-{{ $i }}'"
                                        :aria-selected="active({{ $i }}) ? 'true' : 'false'"
                                        :aria-controls="$id('process') + '-panel'"
                                        :tabindex="active({{ $i }}) ? 0 : -1"
                                        @click="select({{ $i }})"
                                        class="flex shrink-0 items-center gap-3 rounded-full border px-5 py-3 transition-colors duration-500 md:px-7 lg:px-6"
                                        :class="active({{ $i }})
                                            ? 'border-sand-50 bg-sand-50 text-clay-600'
                                            : 'border-white/25 text-white/65 hover:border-white/50 hover:text-white'">
                                    <x-icon :name="$slide['icon']" size="18" />
                                    <span class="whitespace-nowrap text-meta font-semibold">{{ $slide['title'] }}</span>
                                </button>

                                <p class="pointer-events-none min-w-0 flex-1 text-[0.8125rem] leading-[1.6] text-white/85 transition-opacity duration-500 md:text-[0.875rem]"
                                   :style="{ opacity: active({{ $i }}) ? 1 : 0 }"
                                   style="{{ $i === 0 ? '' : 'opacity: 0;' }}">
                                    {{ $slide['summary'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- دسته‌ی کارت‌ها --}}
                <div :id="$id('process') + '-panel'" role="tabpanel" aria-live="polite"
                     class="relative flex flex-1 items-center justify-center overflow-hidden border-t border-sand-300 bg-sand-100 px-6 py-12 md:px-10 lg:border-s lg:border-t-0 lg:py-10">

                    {{--
                        کارت مربع است، پس ارتفاعش برابر عرضش می‌شود و نسبت به
                        حالت کشیده کوتاه‌تر. برای همین حداکثر عرض بالاتر رفته:
                        جای عمودیِ پانل ثابت است و مربعِ کوچک وسطش گم می‌شد.
                    --}}
                    <div class="relative aspect-square w-full max-w-[21rem] md:max-w-[26rem] lg:h-full lg:w-auto lg:max-w-none">
                        @foreach($slides as $i => $slide)
                            <div class="fc-card absolute inset-0 origin-center overflow-hidden rounded-[1.75rem] border-4 border-sand-50 bg-sand-50 shadow-float md:rounded-[2.25rem] md:border-8"
                                 :style="cardStyle({{ $i }})"
                                 style="{{ $i === 0 ? '' : 'opacity: 0;' }}">

                                @if($slide['image'])
                                    <img src="{{ $slide['image'] }}" alt="{{ $slide['alt'] }}"
                                         loading="lazy" decoding="async" draggable="false"
                                         class="h-full w-full object-cover transition-[filter] duration-700"
                                         :class="active({{ $i }}) ? '' : 'grayscale brightness-75 blur-[2px]'">
                                @else
                                    <span aria-hidden="true" class="block h-full w-full transition-[filter] duration-700"
                                          :class="active({{ $i }}) ? '' : 'grayscale brightness-75 blur-[2px]'"
                                          style="background: {{ $slide['tint'] }}"></span>
                                @endif

                                {{-- نشانِ «خط در حال کار» — فقط روی کارت فعال --}}
                                <div class="pointer-events-none absolute start-7 top-7 flex items-center gap-2.5 transition-opacity duration-300"
                                     :style="{ opacity: active({{ $i }}) ? 1 : 0 }" aria-hidden="true">
                                    <span class="relative flex h-2 w-2">
                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white/80"></span>
                                        <span class="relative inline-flex h-2 w-2 rounded-full bg-white shadow-[0_0_10px_rgb(255_255_255/0.9)]"></span>
                                    </span>
                                    <span class="tech text-micro uppercase tracking-[0.3em] text-white/75">{{ $slide['title_en'] }}</span>
                                </div>

                                {{--
                                    پایین کارت فقط عددهای همان مرحله می‌ماند.
                                    شرح رفته کنار بیضیِ فعال، و نامِ مرحله هم
                                    روی خودِ بیضی هست — تکرارش اینجا فقط تصویر
                                    را می‌پوشاند.
                                --}}
                                <div class="pointer-events-none absolute inset-x-0 bottom-0 flex flex-col justify-end bg-gradient-to-t from-black/85 via-black/35 to-transparent p-4 pt-12 transition-opacity duration-500 sm:p-5 md:p-7 md:pt-16"
                                     :style="{ opacity: active({{ $i }}) ? 1 : 0 }">

                                    <div class="flex flex-wrap items-center gap-2">
                                        @if($slide['metric_value'])
                                            <span class="rounded-full border border-white/25 bg-black/25 px-3 py-1 text-micro text-white/80 backdrop-blur-sm">
                                                {{ $slide['metric_label'] }}
                                                <x-num :value="$slide['metric_value']" class="font-bold text-white" />
                                            </span>
                                        @endif
                                        @if($slide['duration'])
                                            <span class="flex items-center gap-1.5 rounded-full border border-white/25 bg-black/25 px-3 py-1 text-micro text-white/80 backdrop-blur-sm">
                                                <x-icon name="clock" size="13" />
                                                {{ $slide['duration'] }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($slide['cta'])
                                        {{--
                                            دکمه‌ی دوم زیر sm پنهان می‌شود. روی
                                            کارتِ مربعِ ۲۷۰ پیکسلی، دو هدف لمسیِ
                                            ۴۴ پیکسلی به دو ردیف می‌شکنند و کل
                                            تصویر را می‌پوشانند؛ و کوچک‌کردنشان
                                            یعنی شکستن حداقلِ هدف لمسی. همین
                                            پیوند در منو و فوتر هم هست.
                                        --}}
                                        <div class="pointer-events-auto mt-4 flex flex-wrap gap-2">
                                            <x-cta :href="route('technology')" variant="primary" size="sm">جزئیات فناوری</x-cta>
                                            <x-cta :href="route('factory')" variant="light" size="sm" class="max-sm:hidden">بازدید از کارخانه</x-cta>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
