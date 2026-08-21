<x-layouts.app>
    <x-page-hero
        eyebrow="Find your block"
        title="محصول مناسب پروژه‌ی خود را پیدا کنید"
        lead="چهار پرسش درباره‌ی پروژه، و سه پیشنهاد با دلیل. اگر جوابی را نمی‌دانید، خالی بگذارید — موتور انتخاب با اطلاعات ناقص هم کار می‌کند." />

    <section class="bg-sand-100 pb-20">
        <div class="container-page">
            @include('partials.finder-form')

            <div class="mt-14 grid grid-cols-1 gap-6 lg:grid-cols-3">
                @foreach([
                    ['ruler', 'ضخامت را از الزام پروژه بگیرید', 'ضخامت دیوار خارجی معمولاً از محاسبه‌ی مبحث ۱۹ می‌آید و ضخامت دیوار داخلی از الزام صوتی مبحث ۱۸.'],
                    ['thermal', 'عایق حرارتی یعنی هزینه‌ی جاری', 'هر پله بالاتر رفتن در سطح عایق، هزینه‌ی اولیه را کمی بالا می‌برد و مصرف انرژی را برای پنجاه سال پایین می‌آورد.'],
                    ['weight', 'وزن، مسئله‌ی سازه است', 'در اضافه طبقه و مقاوم‌سازی، سبک‌بودن دیوار می‌تواند تعیین‌کننده‌تر از عایق‌بودن آن باشد.'],
                ] as [$icon, $title, $text])
                    <div class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-6" data-reveal>
                        <x-icon :name="$icon" size="22" class="text-clay-500" />
                        <h2 class="mt-3 font-bold">{{ $title }}</h2>
                        <p class="mt-2 leading-relaxed text-ink-500">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.app>
