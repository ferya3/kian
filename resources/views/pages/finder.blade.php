<x-layouts.app>
    <x-page-hero
        eyebrow="Find your block"
        :title="__('site.home.finder.title')"
        :lead="__('site.finder.page_lead')" />

    <section class="bg-sand-100 section-b">
        <div class="container-page">
            @include('partials.finder-form')

            <div class="mt-10 grid grid-cols-1 gap-6 lg:grid-cols-3">
                @foreach(['thickness' => 'ruler', 'insulation' => 'thermal', 'weight' => 'weight'] as $tip => $icon)
                    @php
                        $title = __("site.finder.tip.{$tip}.title");
                        $text = __("site.finder.tip.{$tip}.text");
                    @endphp
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
