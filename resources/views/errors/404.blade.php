<x-layouts.app>
    <section class="bg-sand-100 py-24 lg:py-32">
        <div class="container-page">
            <div class="mx-auto max-w-2xl text-center">
                <p class="tech text-7xl font-extrabold text-clay-500/30">۴۰۴</p>
                <h1 class="mt-4 text-h1 font-extrabold">این صفحه پیدا نشد</h1>
                <p class="mt-5 text-lead text-ink-500">
                    ممکن است آدرس تغییر کرده باشد یا محصول از کاتالوگ خارج شده باشد.
                    از اینجا می‌توانید ادامه بدهید:
                </p>

                <div class="mt-9 flex flex-wrap justify-center gap-3">
                    <x-cta :href="route('home')" variant="dark">صفحه اصلی</x-cta>
                    <x-cta :href="route('products.index')" variant="primary">محصولات</x-cta>
                    <x-cta :href="route('technical.downloads')" variant="ghost">مرکز دانلود</x-cta>
                </div>

                <form action="{{ route('search') }}" method="GET" class="relative mx-auto mt-10 max-w-md">
                    <label for="e404-search" class="sr-only">جستجو</label>
                    <input id="e404-search" name="q" type="search" placeholder="دنبال چه چیزی می‌گشتید؟"
                           class="w-full rounded-full border border-sand-300 bg-sand-50 py-3.5 pr-12 pl-5 outline-none focus:border-clay-400">
                    <x-icon name="search" size="18" class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-ink-300" />
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>
