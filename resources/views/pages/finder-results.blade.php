<x-layouts.app>
    <x-page-hero
        eyebrow="Result"
        :title="__('site.finder.results_title')"
        :lead="$labels ? __('site.finder.based_on', ['criteria' => implode(' · ', $labels)]) : __('site.finder.based_on_chosen')"
        compact />

    <section class="bg-sand-100 section-b">
        <div class="container-page">
            @include('partials.finder-results')

            <div class="mt-10">
                <h2 class="text-h3 font-extrabold">{{ __('site.finder.change') }}</h2>
                <div class="mt-5">
                    @include('partials.finder-form')
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
