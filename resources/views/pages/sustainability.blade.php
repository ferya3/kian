<x-layouts.app>
    <x-page-hero
        eyebrow="Sustainability"
        :title="__('site.sustainability.title')"
        :lead="__('site.sustainability.lead')"
        variant="dark">
        <x-stat-band :stats="$stats" light class="mt-12" />
    </x-page-hero>

    <section class="bg-sand-50 section-lg">
        <div class="container-page grid grid-cols-1 gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-5">
                <x-section-heading eyebrow="Life cycle" :title="__('site.sustainability.lifecycle')" />
            </div>

            <ol class="lg:col-span-7" data-reveal-stagger="90">
                @foreach(['extraction', 'production', 'use', 'end_of_life'] as $i => $phase)
                    @php
                        $title = __("site.sustainability.phase.{$phase}.title");
                        $text = __("site.sustainability.phase.{$phase}.text");
                    @endphp
                    <li data-reveal class="grid grid-cols-[auto_1fr] gap-5 border-b border-sand-200 py-6 first:pt-0 last:border-0">
                        <span class="tech grid h-9 w-9 place-items-center rounded-full bg-clay-100 text-sm font-bold text-clay-700">
                            {{ \App\Support\Jalali::digits($i + 1) }}
                        </span>
                        <div>
                            <h3 class="text-card font-bold">{{ $title }}</h3>
                            <p class="mt-2 leading-relaxed text-ink-500">{{ $text }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="bg-sand-100 section">
        <div class="container-page">
            <div class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-7 lg:p-10">
                <p class="eyebrow text-clay-600">Honest note</p>
                <h2 class="mt-3 text-h3 font-extrabold">{{ __('site.sustainability.unsolved') }}</h2>
                <p class="mt-4 max-w-3xl text-lead text-ink-600">
                    {{ __('site.sustainability.unsolved_text') }}
                </p>
                <x-cta :href="route('contact', ['type' => 'technical'])" variant="ghost" class="mt-7">{{ __('site.sustainability.request_report') }}</x-cta>
            </div>
        </div>
    </section>
</x-layouts.app>
