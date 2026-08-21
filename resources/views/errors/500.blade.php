<x-layouts.app>
    <section class="bg-sand-100 py-24 lg:py-32">
        <div class="container-page">
            <div class="mx-auto max-w-2xl text-center">
                <p class="tech text-7xl font-extrabold text-clay-500/30">۵۰۰</p>
                <h1 class="mt-4 text-h1 font-extrabold">خطایی در سرور رخ داد</h1>
                <p class="mt-5 text-lead text-ink-500">
                    مشکل از سمت ماست و به آن رسیدگی می‌شود. لطفاً چند دقیقه دیگر دوباره تلاش کنید
                    یا مستقیم با ما تماس بگیرید.
                </p>
                <div class="mt-9 flex flex-wrap justify-center gap-3">
                    <x-cta :href="route('home')" variant="dark">صفحه اصلی</x-cta>
                    <x-cta href="tel:{{ config('kian.contact.phone_raw') }}" variant="ghost" icon="phone">
                        {{ config('kian.contact.phone') }}
                    </x-cta>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
