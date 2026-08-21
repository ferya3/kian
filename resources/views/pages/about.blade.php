<x-layouts.app>
    <x-page-hero
        eyebrow="About us"
        title="درباره ما"
        :lead="\App\Models\Setting::text('about_lead')"
        variant="dark">
        <x-stat-band :stats="$stats" light class="mt-12" />
    </x-page-hero>

    <section class="bg-sand-50 py-16 lg:py-24">
        <div class="container-page grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-5">
                <x-section-heading eyebrow="Story" title="از یک کوره تا دو خط اکستروژن" />
            </div>

            <div class="space-y-5 text-lead text-ink-600 lg:col-span-7">
                <p>سال {{ \App\Support\Jalali::digits(1380) }} کار با یک کوره‌ی سنتی و پنج نفر شروع شد. آن‌موقع بازار ایران هنوز بلوک سفالی را جایگزین آجر نمی‌دانست و بیشتر سفارش‌ها از پیمانکارانی می‌آمد که یک‌بار امتحان کرده بودند.</p>
                <p>نقطه‌ی چرخش سال {{ \App\Support\Jalali::digits(1392) }} بود: خط اکستروژن با کنترل خلأ و کوره‌ی تونلی جایگزین روش قبلی شد. رواداری ابعادی از چند میلی‌متر به کمتر از دو میلی‌متر رسید — و همین یک عدد بود که در ورود به پروژه‌های بزرگ تفاوت ایجاد کرد.</p>
                <p>امروز دو خط موازی، ظرفیت سالانه‌ی صد و بیست هزار تن و شبکه‌ی نمایندگی در بیش از چهل استان داریم. اما آنچه از روز اول تغییر نکرده این است: هر بچ تولید، پیش از بارگیری آزمون می‌شود.</p>
            </div>
        </div>
    </section>

    <section class="bg-sand-100 py-16 lg:py-20">
        <div class="container-page">
            <x-section-heading eyebrow="Principles" title="سه اصلی که سرِ آن‌ها مذاکره نمی‌کنیم"
                lead="این‌ها شعار نیستند؛ اگر رعایتشان نکنیم، مشتری‌مان یک بار بیشتر از ما خرید نمی‌کند." />

            <ul class="mt-10 grid gap-4 lg:grid-cols-3" data-reveal-stagger="100">
                @foreach([
                    ['ruler', 'رواداری ابعادی', 'اگر ابعاد بلوک‌ها یکنواخت نباشد، سرعت اجرا و مصرف ملات به هم می‌ریزد. رواداری ما زیر دو میلی‌متر کنترل می‌شود — و شاهدش در گزارش هر بچ هست.'],
                    ['shield', 'شفافیت عددی', 'هیچ عددی را گرد نمی‌کنیم تا بهتر به‌نظر برسد. دیتاشیت‌های ما نتیجه‌ی آزمون‌اند، نه ادعای بازاریابی.'],
                    ['trowel', 'پشتیبانی بعد از فروش', 'اگر در کارگاه مشکلی پیش بیاید، کارشناس فنی ما می‌آید — چه تقصیر ما باشد چه نباشد.'],
                ] as [$icon, $title, $text])
                    <li data-reveal class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-6">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-clay-100 text-clay-600">
                            <x-icon :name="$icon" size="21" />
                        </span>
                        <h3 class="mt-4 text-h3 font-bold">{{ $title }}</h3>
                        <p class="mt-2 leading-relaxed text-ink-500">{{ $text }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="bg-sand-50 py-16 lg:py-20">
        <div class="container-page grid gap-10 lg:grid-cols-12 lg:gap-14">
            <div class="lg:col-span-5">
                <x-section-heading eyebrow="Certificates" title="تأییدیه‌ها" />
                <x-cta :href="route('technical.certificates')" variant="ghost" class="mt-7">صفحه گواهی‌نامه‌ها</x-cta>
            </div>
            <ul class="grid gap-3 sm:grid-cols-2 lg:col-span-7">
                @foreach($certificates->take(6) as $certificate)
                    <li class="rounded-xl border border-sand-300 bg-sand-100 p-4">
                        <p class="font-semibold leading-snug">{{ $certificate->title }}</p>
                        <p class="mt-1 text-[0.8125rem] text-ink-400">{{ $certificate->issuer }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
</x-layouts.app>
