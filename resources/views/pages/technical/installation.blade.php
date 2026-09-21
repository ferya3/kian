<x-layouts.app>
    <x-page-hero
        eyebrow="Installation"
        :title="__('site.nav.items.technical_installation')"
        :lead="__('site.installation.lead')" />

    <section class="bg-sand-100 section-b">
        <div class="container-page grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-14">

            <div class="lg:col-span-8">
                <h2 class="text-h3 font-extrabold">{{ __('site.installation.stages') }}</h2>

                <ol class="mt-8" data-reveal-stagger="70">
                    @foreach(['bed', 'mortar', 'damp', 'coursing', 'lintel', 'frame', 'chasing', 'finish'] as $i => $stage)
                        @php
                            $title = __("site.installation.stage.{$stage}.title");
                            $text = __("site.installation.stage.{$stage}.text");
                        @endphp
                        <li data-reveal class="grid grid-cols-[auto_1fr] gap-5 border-b border-sand-300 py-6 first:pt-0 last:border-0">
                            <span class="tech grid h-10 w-10 place-items-center rounded-full bg-clay-500 text-sm font-bold text-white">
                                {{ \App\Support\Jalali::digits(str_pad($i + 1, 2, '0', STR_PAD_LEFT)) }}
                            </span>
                            <div>
                                <h3 class="text-card font-bold">{{ $title }}</h3>
                                <p class="mt-2 leading-relaxed text-ink-500">{{ $text }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>

                @if($faqs->isNotEmpty())
                    <h2 class="mt-10 text-h3 font-extrabold">{{ __('site.installation.faq') }}</h2>
                    <div x-data="accordion()" class="mt-6 divide-y divide-sand-300 border-y border-sand-300">
                        @foreach($faqs as $faq)
                            <div>
                                <h3>
                                    <button type="button" @click="toggle({{ $faq->id }})"
                                            :aria-expanded="isOpen({{ $faq->id }}) ? 'true' : 'false'"
                                            class="flex w-full items-center justify-between gap-4 py-5 text-right font-bold transition hover:text-clay-600">
                                        {{ $faq->question }}
                                        <x-icon name="chevron-down" size="18"
                                                class="shrink-0 text-ink-400 transition-transform duration-300"
                                                ::class="isOpen({{ $faq->id }}) && 'rotate-180'" />
                                    </button>
                                </h3>
                                <div x-show="isOpen({{ $faq->id }})" x-cloak
                                     x-transition:enter="transition ease-[var(--ease-out-expo)] duration-300"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="pb-5 leading-relaxed text-ink-500">
                                    {{ $faq->answer }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="lg:col-span-4">
                <div class="sticky top-28 space-y-4">
                    <div class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-6">
                        <h2 class="font-bold">{{ __('site.installation.files') }}</h2>
                        <div class="mt-4 space-y-3">
                            @foreach($guides as $guide)
                                <x-document-row :document="$guide" />
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-[var(--radius-panel)] bg-ink-950 p-6 text-sand-50">
                        <x-icon name="phone" size="22" class="text-clay-400" />
                        <h2 class="mt-3 font-bold">{{ __('site.installation.on_site') }}</h2>
                        <p class="mt-2 text-[0.9375rem] leading-relaxed text-sand-200/65">
                            {{ __('site.installation.on_site_lead') }}
                        </p>
                        <x-cta :href="route('contact', ['type' => 'technical'])" variant="primary" size="sm" class="mt-5">
                            {{ __('site.installation.request') }}
                        </x-cta>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.app>
