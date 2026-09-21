@php
    // نشان و قالبِ فایل کدند؛ برچسب از پرونده‌ی زبان می‌آید
    $tiles = [
        ['icon' => 'file',      'key' => 'datasheet',    'meta' => 'PDF', 'route' => ['technical.downloads', ['category' => 'datasheet']]],
        ['icon' => 'blueprint', 'key' => 'catalog',      'meta' => 'PDF', 'route' => ['technical.downloads', ['category' => 'catalog']]],
        ['icon' => 'grid',      'key' => 'cad',          'meta' => 'DWG', 'route' => ['technical.downloads', ['category' => 'cad']]],
        ['icon' => 'layers',    'key' => 'bim',          'meta' => 'RVT · IFC', 'route' => ['technical.downloads', ['category' => 'bim']]],
        ['icon' => 'trowel',    'key' => 'installation', 'meta' => 'PDF', 'route' => ['technical.installation', []]],
        ['icon' => 'shield',    'key' => 'certificates', 'meta' => 'PDF', 'route' => ['technical.certificates', []]],
    ];
@endphp

<section class="bg-ink-950 section-lg text-sand-50" aria-labelledby="technical-heading">
    <div class="container-page">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-5">
                <x-section-heading
                    eyebrow="Technical center"
                    :title="__('site.home.technical.title')"
                    :lead="__('site.home.technical.lead')"
                    light id="technical-heading" />

                <ul class="mt-8 space-y-4">
                    @foreach(\App\Support\Options::audiences() as $key => $audience)
                        <li class="flex gap-4 border-b border-white/[0.08] pb-4 last:border-0">
                            <span class="mt-0.5 grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-white/10 bg-white/[0.05] text-clay-400">
                                <x-icon :name="$audience['icon']" size="19" />
                            </span>
                            <div>
                                <p class="font-bold">{{ $audience['label'] }}</p>
                                <p class="mt-0.5 text-[0.9375rem] text-sand-200/60">«{{ $audience['question'] }}»</p>
                                <a href="{{ route($audience['route']) }}"
                                   class="tap mt-2 gap-1.5 text-meta font-semibold text-clay-400 transition hover:text-clay-300">
                                    {{ $audience['cta'] }}
                                    <x-icon name="arrow-left" size="14" />
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-7">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2" data-reveal-stagger="70">
                    @foreach($tiles as $tile)
                        <a href="{{ route($tile['route'][0], $tile['route'][1]) }}" data-reveal
                           class="group flex items-center gap-4 rounded-2xl border border-white/10 bg-white/[0.04] p-5 transition-all duration-300 hover:border-clay-500/40 hover:bg-clay-500/10">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white/[0.06] text-clay-400 transition group-hover:bg-clay-500 group-hover:text-white">
                                <x-icon :name="$tile['icon']" size="20" />
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block font-semibold">{{ __('site.home.technical.tile.'.$tile['key']) }}</span>
                                <span class="tech block text-micro uppercase tracking-wider text-sand-200/45">{{ $tile['meta'] }}</span>
                            </span>
                            <x-icon name="download" size="18" class="shrink-0 text-sand-200/30 transition group-hover:text-clay-300" />
                        </a>
                    @endforeach
                </div>

                <div class="mt-6 rounded-2xl border border-white/10 bg-gradient-to-bl from-clay-900/60 to-transparent p-6">
                    <p class="text-[0.9375rem] leading-relaxed text-sand-200/75">
                        {{ __('site.home.technical.missing') }}
                    </p>
                    <x-cta :href="route('contact', ['type' => 'technical'])" variant="primary" size="sm" class="mt-4">
                        {{ __('site.home.technical.request') }}
                    </x-cta>
                </div>
            </div>
        </div>
    </div>
</section>
