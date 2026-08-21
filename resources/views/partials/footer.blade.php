<footer class="no-print mt-24 bg-ink-950 text-sand-200">
    {{-- نوار فراخوان پیش از فوتر --}}
    <div class="border-b border-white/[0.07]">
        <div class="container-page grid grid-cols-1 gap-8 py-14 lg:grid-cols-12 lg:items-center lg:py-16">
            <div class="lg:col-span-7">
                <p class="eyebrow text-clay-400">Talk to an engineer</p>
                <h2 class="mt-3 text-h2 font-extrabold text-sand-50">
                    درباره‌ی پروژه‌تان با واحد فنی حرف بزنید
                </h2>
                <p class="mt-4 max-w-2xl text-[1.0625rem] leading-relaxed text-sand-200/65">
                    محاسبه‌ی مقاومت حرارتی دیوار، انتخاب ضخامت مناسب اقلیم و برآورد مقدار مصالح — بدون هزینه انجام می‌شود.
                </p>
            </div>
            <div class="flex flex-wrap gap-3 lg:col-span-5 lg:justify-end">
                <a href="{{ route('contact') }}"
                   class="inline-flex items-center gap-2 rounded-full bg-clay-500 px-7 py-3.5 font-semibold text-white transition hover:bg-clay-400">
                    درخواست مشاوره فنی
                    <x-icon name="arrow-left" size="18" />
                </a>
                <a href="tel:{{ config('kian.contact.phone_raw') }}"
                   class="inline-flex items-center gap-2 rounded-full border border-white/15 px-7 py-3.5 font-semibold text-sand-100 transition hover:border-white/40">
                    <x-icon name="phone" size="18" />
                    <span class="tech">{{ config('kian.contact.phone') }}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="container-page grid grid-cols-1 gap-10 py-14 md:grid-cols-2 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <x-brand-mark class="h-11 w-11" />
                <span>
                    <span class="block text-lg font-extrabold text-sand-50">{{ config('kian.brand.legal_name') }}</span>
                    <span class="tech block text-[0.6875rem] uppercase tracking-[0.18em] text-sand-200/45">{{ config('kian.brand.name_en') }}</span>
                </span>
            </a>
            <p class="mt-5 max-w-sm text-[0.9375rem] leading-relaxed text-sand-200/55">
                {{ config('kian.brand.tagline') }} — تولید بلوک سفالی مهندسی‌شده از سال {{ config('kian.brand.founded') - 621 }}.
            </p>

            <ul class="mt-6 space-y-3 text-[0.9375rem] text-sand-200/70">
                <li class="flex gap-3">
                    <x-icon name="pin" size="18" class="mt-1 shrink-0 text-clay-400" />
                    <span>{{ config('kian.contact.address') }}</span>
                </li>
                <li class="flex gap-3">
                    <x-icon name="mail" size="18" class="mt-0.5 shrink-0 text-clay-400" />
                    <a href="mailto:{{ config('kian.contact.email') }}" class="tech transition hover:text-sand-50">{{ config('kian.contact.email') }}</a>
                </li>
            </ul>
        </div>

        <nav class="lg:col-span-8" aria-label="ناوبری فوتر">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-sand-50">محصولات</h3>
                    <ul class="space-y-2.5 text-[0.9375rem] text-sand-200/60">
                        @foreach($megaMenu->flatMap->children->take(6) as $child)
                            <li><a href="{{ route('products.index', ['category' => $child->slug]) }}" class="transition hover:text-clay-300">{{ $child->name }}</a></li>
                        @endforeach
                        <li><a href="{{ route('finder.show') }}" class="font-semibold text-clay-400 transition hover:text-clay-300">انتخاب محصول →</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-sand-50">مرکز فنی</h3>
                    <ul class="space-y-2.5 text-[0.9375rem] text-sand-200/60">
                        <li><a href="{{ route('technical.downloads') }}" class="transition hover:text-clay-300">دیتاشیت محصولات</a></li>
                        <li><a href="{{ route('technical.downloads', ['category' => 'cad']) }}" class="transition hover:text-clay-300">فایل‌های CAD</a></li>
                        <li><a href="{{ route('technical.downloads', ['category' => 'bim']) }}" class="transition hover:text-clay-300">آبجکت‌های BIM</a></li>
                        <li><a href="{{ route('technical.installation') }}" class="transition hover:text-clay-300">راهنمای اجرا</a></li>
                        <li><a href="{{ route('technical.certificates') }}" class="transition hover:text-clay-300">گواهی‌نامه‌ها</a></li>
                        <li><a href="{{ route('technical.faq') }}" class="transition hover:text-clay-300">پرسش‌های متداول</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-sand-50">شرکت</h3>
                    <ul class="space-y-2.5 text-[0.9375rem] text-sand-200/60">
                        <li><a href="{{ route('about') }}" class="transition hover:text-clay-300">درباره ما</a></li>
                        <li><a href="{{ route('factory') }}" class="transition hover:text-clay-300">کارخانه</a></li>
                        <li><a href="{{ route('technology') }}" class="transition hover:text-clay-300">فناوری تولید</a></li>
                        <li><a href="{{ route('sustainability') }}" class="transition hover:text-clay-300">پایداری</a></li>
                        <li><a href="{{ route('projects.index') }}" class="transition hover:text-clay-300">پروژه‌ها</a></li>
                        <li><a href="{{ route('articles.index') }}" class="transition hover:text-clay-300">مقالات فنی</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-sand-50">فروش</h3>
                    <ul class="space-y-2.5 text-[0.9375rem] text-sand-200/60">
                        <li><a href="{{ route('contact') }}" class="transition hover:text-clay-300">درخواست قیمت</a></li>
                        <li><a href="{{ route('distributors') }}" class="transition hover:text-clay-300">نمایندگان فروش</a></li>
                        <li><a href="{{ route('contact', ['type' => 'distributor']) }}" class="transition hover:text-clay-300">درخواست نمایندگی</a></li>
                        <li><a href="{{ route('contact', ['type' => 'technical']) }}" class="transition hover:text-clay-300">پشتیبانی فنی</a></li>
                    </ul>

                    <div class="mt-6 flex gap-2">
                        @foreach(['instagram' => 'IG', 'linkedin' => 'in', 'aparat' => 'AP', 'telegram' => 'TG'] as $key => $label)
                            <a href="{{ config("kian.social.$key") }}" rel="noopener noreferrer" target="_blank"
                               class="tech grid h-9 w-9 place-items-center rounded-full border border-white/12 text-[0.6875rem] font-semibold text-sand-200/70 transition hover:border-clay-400 hover:text-clay-300"
                               aria-label="{{ $key }}">{{ $label }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div class="border-t border-white/[0.07]">
        <div class="container-page flex flex-col gap-3 py-6 text-[0.8125rem] text-sand-200/40 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ \App\Support\Jalali::year() }} {{ config('kian.brand.legal_name') }} — تمام حقوق محفوظ است.</p>
            <p class="tech">{{ config('kian.brand.tagline_en') }}</p>
        </div>
    </div>
</footer>
