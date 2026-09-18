{{--
    یک ویژگی در ستون‌های کناری «چرا سفال؟».

    تایپوگرافی عمداً از مقیاس سکشن جدا شده. عنوان پیش‌تر text-h3 بود که روی
    دسکتاپ ۳۲ پیکسل رندر می‌شد — اندازه‌ی یک تیتر واقعی، نه عنوان کارتی که در
    ستونی ۴۰۰ پیکسلی می‌نشیند. همین یک قلم، بیشترین سهم را در ارتفاع بخش داشت.

    برچسب انگلیسی هم به خط خودش رفته بود؛ حالا هم‌تراز با عنوان است و اگر جا
    نشد خودش می‌شکند — در حالت عادی یک خط کامل صرفه‌جویی می‌شود.
--}}
<li data-reveal
    class="group grid grid-cols-[auto_1fr] gap-3.5 border-b border-white/[0.08] py-5">
    <span class="grid h-10 w-10 place-items-center rounded-lg border border-white/10 bg-white/[0.05] text-clay-400 transition-colors duration-500 group-hover:border-clay-500/40 group-hover:bg-clay-500/15 group-hover:text-clay-300">
        <x-icon :name="$pillar['icon']" size="18" />
    </span>

    <div class="min-w-0">
        <div class="flex flex-wrap items-baseline gap-x-2">
            <h3 class="text-card font-bold text-sand-50">{{ $pillar['title'] }}</h3>
            <span class="tech text-micro uppercase tracking-[0.14em] text-sand-200/35">{{ $pillar['en'] }}</span>
        </div>

        <p class="mt-2 text-[0.9375rem] leading-[1.7] text-sand-200/65">{{ $pillar['text'] }}</p>

        <p class="mt-2.5 flex flex-wrap items-baseline gap-x-2">
            <span class="tech text-[1.0625rem] font-extrabold leading-tight text-clay-300">{{ $pillar['metric'] }}</span>
            <span class="text-meta text-sand-200/45">{{ $pillar['metricLabel'] }}</span>
        </p>
    </div>
</li>
