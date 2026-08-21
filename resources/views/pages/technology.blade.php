<x-layouts.app>
    <x-page-hero
        eyebrow="From earth to architecture"
        title="از خاک تا سازه"
        lead="تولید سفال ترکیبی است از سه چیز ساده: خاک، آب و آتش. آنچه کارخانه‌ی مدرن را از کوره‌ی سنتی جدا می‌کند، کنترل دقیق هر سه است."
        variant="dark">

        <x-stat-band :stats="$stats" light class="mt-12" />
    </x-page-hero>

    <section class="bg-sand-50 py-16 lg:py-24">
        <div class="container-page">
            <x-section-heading eyebrow="Process" title="نُه مرحله، از معدن تا پالت"
                lead="هر مرحله یک متغیر کنترلی دارد که اگر از پنجره‌ی مجاز خارج شود، محصول نهایی را خراب می‌کند." />

            <ol class="mt-14 space-y-0" data-reveal-stagger="70">
                @foreach($steps as $step)
                    <li data-reveal class="group grid grid-cols-1 gap-6 border-t border-sand-300 py-8 lg:grid-cols-12 lg:gap-10">
                        <div class="flex items-baseline gap-4 lg:col-span-3">
                            <span class="tech text-5xl font-extrabold leading-none text-clay-500/25 transition-colors duration-500 group-hover:text-clay-500/60">
                                {{ \App\Support\Jalali::digits($step->paddedNumber()) }}
                            </span>
                            <div>
                                <h3 class="text-h3 font-bold leading-tight">{{ $step->title }}</h3>
                                <p class="tech mt-1 text-micro uppercase tracking-[0.14em] text-ink-300">{{ $step->title_en }}</p>
                            </div>
                        </div>

                        <p class="leading-relaxed text-ink-500 lg:col-span-6">{{ $step->description }}</p>

                        <dl class="flex flex-wrap items-start gap-6 lg:col-span-3 lg:justify-end">
                            @if($step->metric_value)
                                <div class="text-right lg:text-left">
                                    <dt class="text-micro text-ink-400">{{ $step->metric_label }}</dt>
                                    <dd class="mt-0.5 text-xl font-extrabold text-clay-600"><x-num :value="$step->metric_value" /></dd>
                                </div>
                            @endif
                            @if($step->duration)
                                <div class="text-right lg:text-left">
                                    <dt class="text-micro text-ink-400">زمان</dt>
                                    <dd class="mt-0.5 text-xl font-extrabold text-ink-700"><x-num :value="$step->duration" /></dd>
                                </div>
                            @endif
                        </dl>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="bg-ink-950 py-16 text-sand-50 lg:py-20">
        <div class="container-page grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-14">
            <div class="lg:col-span-5">
                <x-section-heading eyebrow="Why 900°C" title="چرا دقیقاً نهصد درجه؟" light />
            </div>
            <div class="lg:col-span-7 space-y-5 text-lead text-sand-200/70">
                <p>زیر هشتصد درجه، تبدیل کانی‌شناسی خاک کامل نمی‌شود و محصول در برابر رطوبت آسیب‌پذیر می‌ماند. بالای هزار درجه، تغییر شکل و ذوب موضعی شروع می‌شود و رواداری ابعادی از دست می‌رود.</p>
                <p>پنجره‌ی کاری باریک است. کوره‌ی تونلی با هجده زون کنترل مستقل دما، دقیقاً برای ماندن در همین پنجره طراحی شده — و حرارت خروجی‌اش، به‌جای هدررفت، به خشک‌کن بازمی‌گردد.</p>
                <p class="text-sand-200/50">همین یک تصمیم، مصرف انرژی هر تن محصول را نسبت به سال ۱۳۹۵ حدود سی‌وهشت درصد کاهش داده است.</p>
            </div>
        </div>
    </section>

    <section class="bg-sand-100 py-16 lg:py-20">
        <div class="container-page flex flex-wrap items-center justify-between gap-6">
            <div>
                <h2 class="text-h3 font-extrabold">می‌خواهید خط تولید را از نزدیک ببینید؟</h2>
                <p class="mt-2 text-ink-500">بازدید کارشناسی برای مهندسان مشاور، کارفرمایان و دانشجویان با هماهنگی قبلی امکان‌پذیر است.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <x-cta :href="route('factory')" variant="dark">نقشه‌ی کارخانه</x-cta>
                <x-cta :href="route('contact')" variant="ghost">درخواست بازدید</x-cta>
            </div>
        </div>
    </section>
</x-layouts.app>
