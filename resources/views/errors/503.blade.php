<x-layouts.app>
    <section class="bg-sand-100 section-lg">
        <div class="container-page">
            <div class="mx-auto max-w-2xl text-center">
                <p class="tech text-7xl font-extrabold text-clay-500/30">{{ \App\Support\Jalali::digits(503) }}</p>
                <h1 class="mt-4 text-h1 font-extrabold">{{ __('site.error.503.title') }}</h1>
                <p class="mt-5 text-lead text-ink-500">
                    {{ __('site.error.503.lead') }}
                </p>
                <div class="mt-9 flex flex-wrap justify-center gap-3">
                    <x-cta :href="route('home')" variant="dark">{{ __('site.nav.home') }}</x-cta>
                    <x-cta href="tel:{{ config('kian.contact.phone_raw') }}" variant="ghost" icon="phone">
                        {{ \App\Support\Brand::phone() }}
                    </x-cta>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
