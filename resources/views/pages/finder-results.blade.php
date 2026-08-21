<x-layouts.app>
    <x-page-hero
        eyebrow="Result"
        title="محصولات پیشنهادی برای پروژه‌ی شما"
        :lead="$labels ? 'بر اساس: '.implode(' · ', $labels) : 'بر اساس معیارهای انتخاب‌شده'"
        compact />

    <section class="bg-sand-100 pb-20">
        <div class="container-page">
            @include('partials.finder-results')

            <div class="mt-10">
                <h2 class="text-h3 font-extrabold">می‌خواهید معیارها را تغییر دهید؟</h2>
                <div class="mt-5">
                    @include('partials.finder-form')
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
