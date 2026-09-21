<x-layouts.app>
    <section class="bg-sand-100 section-lg">
        <div class="container-page">
            <div class="mx-auto max-w-2xl text-center">
                <p class="tech text-7xl font-extrabold text-clay-500/30">{{ \App\Support\Jalali::digits(404) }}</p>
                <h1 class="mt-4 text-h1 font-extrabold">{{ __('site.error.404.title') }}</h1>
                <p class="mt-5 text-lead text-ink-500">
                    {{ __('site.error.404.lead') }}
                </p>

                <div class="mt-9 flex flex-wrap justify-center gap-3">
                    <x-cta :href="route('home')" variant="dark">{{ __('site.nav.home') }}</x-cta>
                    <x-cta :href="route('products.index')" variant="primary">{{ __('site.nav.items.products_index') }}</x-cta>
                    <x-cta :href="route('technical.downloads')" variant="ghost">{{ __('site.product.download_centre') }}</x-cta>
                </div>

                <form action="{{ route('search') }}" method="GET" class="relative mx-auto mt-10 max-w-md">
                    <label for="e404-search" class="sr-only">{{ __('site.nav.search') }}</label>
                    <input id="e404-search" name="q" type="search" placeholder="{{ __('site.error.404.search') }}"
                           class="w-full rounded-full border border-sand-300 bg-sand-50 py-3.5 pr-12 pl-5 outline-none focus:border-clay-400">
                    <x-icon name="search" size="18" class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-ink-300" />
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>
