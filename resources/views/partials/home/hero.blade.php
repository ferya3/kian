@php
    use App\Models\Setting;
    use App\Models\SiteMedia;

    /*
    | سه حالت، به همین ترتیب: ویدئو، تصویر، طرح وکتوری. هیچ‌کدام اجباری نیست —
    | سایت با دیتابیسِ بدون رسانه هم کامل دیده می‌شود.
    */
    $video = Setting::text('hero_video');
    $image = SiteMedia::url('hero.home');
    $poster = SiteMedia::url('hero.home_poster') ?? $image;
@endphp

{{--
    روی گوشی هیرو مربع است: ارتفاعش برابر عرض صفحه، نه یک بلوک کشیده‌ی
    تمام‌قد. min-h به‌جای aspect استفاده شده تا اگر متن بلندتر شد، به‌جای
    بریده‌شدن، کادر کمی رشد کند.
    از lg به بالا به ارتفاع سینمایی تمام‌صفحه برمی‌گردد.
--}}
<section class="relative flex min-h-[100vw] flex-col justify-center overflow-hidden bg-ink-950 pb-6 pt-[4.5rem] text-sand-50 sm:min-h-[min(100vw,36rem)] sm:pb-12 sm:pt-24 lg:min-h-[88svh] lg:pb-16 lg:pt-32">

    @if($video)
        {{--
            ویدئوی سینمایی کارخانه: نمای نزدیک خاک → کوره → خروج محصول → ساختمان.
            پوستر از پنل می‌آید؛ بدون آن تا لحظه‌ی آماده‌شدن ویدئو کادر سیاه است.
        --}}
        <video class="absolute inset-0 h-full w-full object-cover opacity-60"
               autoplay muted loop playsinline preload="metadata"
               @if($poster) poster="{{ $poster }}" @endif aria-hidden="true">
            <source src="{{ $video }}" type="video/mp4">
        </video>
        <div class="absolute inset-0 bg-gradient-to-t from-ink-950 via-ink-950/70 to-ink-950/40"></div>
    @elseif($image)
        {{--
            تصویر با شفافیت کامل — پیش‌تر opacity-60 داشت و یک پرده‌ی تیره روی
            تمام سطحش، یعنی عملاً نصفه دیده می‌شد.

            به‌جای آن دو محافظ موضعی مانده، چون هدر شفاف است و متن روی عکس
            می‌نشیند: یک نوار بالا (روی آسمان، که معمولاً خالی است) و یک شیب از
            سمت راست که تا میانه‌ی کادر کاملاً محو می‌شود. نیمه‌ی چپ عکس
            دست‌نخورده و با رنگ کامل دیده می‌شود.

            اگر عکس نهایی تیره باشد، هر دو محافظ را می‌شود برداشت.
        --}}
        <img src="{{ $image }}" alt="{{ SiteMedia::alt('hero.home') }}"
             fetchpriority="high" decoding="async"
             class="absolute inset-0 h-full w-full object-cover">

        <div class="absolute inset-x-0 top-0 h-36 bg-gradient-to-b from-ink-950/80 to-transparent" aria-hidden="true"></div>
        {{--
            روی گوشی متن تمام عرض را می‌گیرد، پس شیب باید تا لبه‌ی چپ برسد —
            ولی آنجا هم فقط ۲۰٪ است. از lg به بالا متن در نیمه‌ی راست می‌ماند و
            شیب تا ۷۰٪ عرض کاملاً محو می‌شود.
        --}}
        <div class="absolute inset-0 bg-gradient-to-l from-ink-950/85 via-ink-950/60 to-ink-950/20 lg:via-ink-950/45 lg:via-40% lg:to-transparent lg:to-70%" aria-hidden="true"></div>
    @else
        <x-hero-scene />
    @endif

    <div class="container-page relative">
        {{--
            سایه‌ی متن فقط وقتی لازم است که پشت متن عکس باشد؛ روی طرح وکتوری
            زمینه از قبل تیره است و سایه بی‌دلیل متن را کدر می‌کند.
        --}}
        <div class="max-w-4xl {{ $image && ! $video ? '[text-shadow:0_2px_20px_rgb(10_8_6/0.85),0_1px_4px_rgb(10_8_6/0.7)]' : '' }}">
            <p class="eyebrow inline-flex items-center gap-2 rounded-full border border-white/12 bg-white/[0.06] px-3 py-1.5 text-clay-300 backdrop-blur-sm sm:gap-2.5 sm:px-4 sm:py-2"
               data-reveal>
                <span class="relative flex h-1.5 w-1.5">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-ember-500 opacity-75"></span>
                    <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-ember-500"></span>
                </span>
                {{ Setting::text('hero_eyebrow', 'کارخانه سفال کیان') }}
            </p>

            <h1 class="mt-3 text-display sm:mt-7 font-extrabold text-balance text-sand-50" data-reveal style="--reveal-delay: 90ms">
                {{ Setting::text('hero_title', config('kian.brand.tagline')) }}
            </h1>

            <p class="mt-2.5 line-clamp-2 max-w-2xl text-lead text-sand-200/75 sm:mt-6 sm:line-clamp-none" data-reveal style="--reveal-delay: 180ms">
                {{ Setting::text('hero_subtitle', config('kian.seo.default_description')) }}
            </p>

            {{-- دو دکمه کنار هم روی گوشی: متن تک‌خطی، padding کم، ارتفاع ۴۴ --}}
            <div class="mt-4 flex items-center gap-2.5 sm:mt-10 sm:gap-3" data-reveal style="--reveal-delay: 270ms">
                {{-- آیکون روی گوشی پنهان می‌شود: در ۳۶۰ پیکسل، برچسب مهم‌تر از فلش است --}}
                <x-cta :href="route('products.index')" variant="primary" size="lg"
                       class="min-w-0 flex-1 justify-center whitespace-nowrap px-2 text-meta [&_svg]:hidden sm:flex-none sm:gap-2 sm:px-5 sm:text-[0.9375rem] sm:[&_svg]:block">مشاهده محصولات</x-cta>
                <x-cta :href="route('factory')" variant="light" size="lg" icon="play"
                       class="min-w-0 flex-1 justify-center whitespace-nowrap px-2 text-meta [&_svg]:hidden sm:flex-none sm:gap-2 sm:px-5 sm:text-[0.9375rem] sm:[&_svg]:block">آشنایی با کارخانه</x-cta>
            </div>
        </div>

    </div>

    {{--
        اشاره‌گر اسکرول — یک خط عمودی که قطره‌های نور روی آن پایین می‌روند.
        حرکت رو به پایین، جهت را می‌گوید بی‌آنکه فلشی لازم باشد.

        پیوند واقعی است نه تزئین: کلیک و فوکوس صفحه‌کلید هر دو به بخش انتخاب
        محصول می‌برند، پس برای کسی که با Tab حرکت می‌کند هم میان‌بر است.

        شرط هم عرض است و هم ارتفاع. عرض، چون پایین هیروی مربعِ گوشی جا نیست و
        بخش بعدی از همان‌جا پیداست. ارتفاع، چون روی نمایشگر کوتاه (۱۲۸۰×۷۲۰)
        فاصله‌اش تا دکمه‌ها به چند پیکسل می‌رسد و با یک تیتر سه‌خطی روی آن‌ها
        می‌افتد — اشاره‌گری که جا ندارد، بهتر است نباشد.
    --}}
    <a href="#find-your-block"
       class="group absolute inset-x-0 bottom-7 z-10 mx-auto hidden w-12 flex-col items-center [@media(min-width:1024px)_and_(min-height:760px)]:flex"
       aria-label="رفتن به بخش انتخاب محصول">
        <span class="relative block h-12 w-0.5 overflow-hidden rounded-full bg-white/20 transition-colors duration-300 group-hover:bg-white/35"
              aria-hidden="true">
            <span class="hero-rain-drop"></span>
            <span class="hero-rain-drop" style="animation-delay: 1.8s"></span>
        </span>
    </a>

</section>
