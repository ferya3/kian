<x-layouts.app>
    <x-page-hero
        eyebrow="Downloads"
        :title="__('site.product.download_centre')"
        :lead="__('site.downloads.lead')"
        compact />

    <section class="bg-sand-100 section-b">
        <div class="container-page">
            <form method="GET" action="{{ route('technical.downloads') }}"
                  class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-5 lg:p-6">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
                    <div>
                        <label for="q" class="mb-2 block text-meta font-semibold text-ink-600">{{ __('site.nav.search') }}</label>
                        <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="{{ __('site.downloads.name_placeholder') }}"
                               class="w-full rounded-xl border border-sand-300 bg-sand-100 px-4 py-3 outline-none focus:border-clay-400 focus:bg-sand-50">
                    </div>

                    @foreach([
                        ['name' => 'category', 'label' => __('site.downloads.kind'), 'options' => $labels],
                        ['name' => 'audience', 'label' => __('site.downloads.audience'), 'options' => $audiences],
                    ] as $field)
                        <div>
                            <label for="{{ $field['name'] }}" class="mb-2 block text-meta font-semibold text-ink-600">{{ $field['label'] }}</label>
                            <div class="relative">
                                <select id="{{ $field['name'] }}" name="{{ $field['name'] }}"
                                        class="w-full appearance-none rounded-xl border border-sand-300 bg-sand-100 py-3 pr-4 pl-10 outline-none focus:border-clay-400 focus:bg-sand-50">
                                    <option value="">{{ __('site.products.all') }}</option>
                                    @foreach($field['options'] as $value => $label)
                                        <option value="{{ $value }}" @selected(request($field['name']) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-icon name="chevron-down" size="16" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-400" />
                            </div>
                        </div>
                    @endforeach

                    <div>
                        <label for="format" class="mb-2 block text-meta font-semibold text-ink-600">{{ __('site.downloads.format') }}</label>
                        <div class="relative">
                            <select id="format" name="format"
                                    class="w-full appearance-none rounded-xl border border-sand-300 bg-sand-100 py-3 pr-4 pl-10 uppercase outline-none focus:border-clay-400 focus:bg-sand-50">
                                <option value="">{{ __('site.products.all') }}</option>
                                @foreach($formats as $format)
                                    <option value="{{ $format }}" @selected(request('format') === $format)>{{ strtoupper($format) }}</option>
                                @endforeach
                            </select>
                            <x-icon name="chevron-down" size="16" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-400" />
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-sand-200 pt-5">
                    <p class="tech text-meta text-ink-400">
                        {{ __('site.technical.files', ['count' => \App\Support\Jalali::digits($documents->count())]) }}
                    </p>
                    <div class="flex gap-2">
                        @if(request()->hasAny(['q', 'category', 'audience', 'format']))
                            <a href="{{ route('technical.downloads') }}"
                               class="tap rounded-full px-4 py-2.5 text-[0.875rem] text-ink-400 transition hover:text-ink-900">{{ __('site.downloads.clear') }}</a>
                        @endif
                        <button type="submit" class="tap rounded-full bg-ink-900 px-6 py-2.5 text-[0.875rem] font-semibold text-sand-50 transition hover:bg-clay-600">
                            {{ __('site.downloads.apply') }}
                        </button>
                    </div>
                </div>
            </form>

            @if($documents->isEmpty())
                <div class="mt-10 rounded-[var(--radius-panel)] border border-dashed border-sand-300 py-16 text-center">
                    <p class="text-lg font-bold">{{ __('site.downloads.empty') }}</p>
                    <p class="mt-2 text-ink-400">{{ __('site.downloads.empty_hint') }}</p>
                    <x-cta :href="route('contact', ['type' => 'technical'])" variant="ghost" class="mt-6">{{ __('site.downloads.request') }}</x-cta>
                </div>
            @else
                <div class="mt-8 space-y-10">
                    @foreach($documents->groupBy('category') as $category => $group)
                        <div>
                            <h2 class="border-b border-sand-300 pb-3 text-lg font-extrabold">{{ $labels[$category] ?? $category }}</h2>
                            <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3" data-reveal-stagger="50">
                                @foreach($group as $document)
                                    <x-document-row :document="$document" data-reveal />
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-layouts.app>
