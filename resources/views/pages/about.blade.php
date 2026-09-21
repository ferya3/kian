<x-layouts.app>
    <x-page-hero
        eyebrow="About us"
        :title="__('site.nav.items.about')"
        :lead="\App\Models\Setting::text('about_lead')"
        variant="dark">
        <x-stat-band :stats="$stats" light class="mt-12" />
    </x-page-hero>

    <section class="bg-sand-50 section-lg">
        <div class="container-page grid grid-cols-1 gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-5">
                <x-section-heading eyebrow="Story" :title="__('site.about.story')" />
            </div>

            <div class="space-y-5 text-lead text-ink-600 lg:col-span-7">
                {{--
                    سال‌ها در متنِ ترجمه نمی‌نشینند، پارامتر می‌شوند: در فارسی
                    ۱۳۸۰ و در انگلیسی ۲۰۰۱ — همان لحظه، در دو تقویم.
                --}}
                @php
                    $founded = \App\Support\Jalali::digits(\App\Support\Locales::calendar() === 'jalali' ? 1380 : 2001);
                    $turning = \App\Support\Jalali::digits(\App\Support\Locales::calendar() === 'jalali' ? 1392 : 2013);
                @endphp
                <p>{{ __('site.about.p1', ['year' => $founded]) }}</p>
                <p>{{ __('site.about.p2', ['year' => $turning]) }}</p>
                <p>{{ __('site.about.p3') }}</p>
            </div>
        </div>
    </section>

    <section class="bg-sand-100 section">
        <div class="container-page">
            <x-section-heading eyebrow="Principles" :title="__('site.about.principles')"
                :lead="__('site.about.principles_lead')" />

            <ul class="mt-10 grid grid-cols-1 gap-4 lg:grid-cols-3" data-reveal-stagger="100">
                @foreach(['tolerance' => 'ruler', 'honesty' => 'shield', 'support' => 'trowel'] as $key => $icon)
                    @php
                        $title = __("site.about.principle.{$key}.title");
                        $text = __("site.about.principle.{$key}.text");
                    @endphp
                    <li data-reveal class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-6">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-clay-100 text-clay-600">
                            <x-icon :name="$icon" size="21" />
                        </span>
                        <h3 class="mt-4 text-card font-bold">{{ $title }}</h3>
                        <p class="mt-2 leading-relaxed text-ink-500">{{ $text }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="bg-sand-50 section">
        <div class="container-page grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-14">
            <div class="lg:col-span-5">
                <x-section-heading eyebrow="Certificates" :title="__('site.about.approvals')" />
                <x-cta :href="route('technical.certificates')" variant="ghost" class="mt-7">{{ __('site.about.certificates_page') }}</x-cta>
            </div>
            <ul class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:col-span-7">
                @foreach($certificates->take(6) as $certificate)
                    <li class="rounded-xl border border-sand-300 bg-sand-100 p-4">
                        <p class="font-semibold leading-snug">{{ $certificate->title }}</p>
                        <p class="mt-1 text-meta text-ink-400">{{ $certificate->issuer }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
</x-layouts.app>
