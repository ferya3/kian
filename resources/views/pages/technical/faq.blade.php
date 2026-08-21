<x-layouts.app>
    @php
        $groupLabels = [
            'technical' => 'مشخصات فنی',
            'installation' => 'اجرا و کارگاه',
            'order' => 'سفارش و تحویل',
            'general' => 'عمومی',
        ];
    @endphp

    <x-page-hero
        eyebrow="FAQ"
        title="پرسش‌های متداول"
        lead="پاسخ‌های کوتاه و صریح کارشناسان فنی به پرسش‌هایی که بیشتر از همه می‌شنویم." />

    <section class="bg-sand-100 pb-20">
        <div class="container-page grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-14">
            <nav class="lg:col-span-3" aria-label="دسته‌های پرسش">
                <ul class="sticky top-28 space-y-1">
                    @foreach($groups as $group => $items)
                        <li>
                            <a href="#faq-{{ $group }}"
                               class="flex items-center justify-between rounded-lg px-4 py-3 text-[0.9375rem] font-semibold text-ink-600 transition hover:bg-sand-200 hover:text-clay-600">
                                {{ $groupLabels[$group] ?? $group }}
                                <span class="tech text-micro text-ink-300">{{ \App\Support\Jalali::digits($items->count()) }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="lg:col-span-9">
                @foreach($groups as $group => $items)
                    <section id="faq-{{ $group }}" class="mb-12 last:mb-0">
                        <h2 class="text-h3 font-extrabold">{{ $groupLabels[$group] ?? $group }}</h2>

                        <div x-data="accordion({{ $loop->first ? $items->first()->id : 'null' }})"
                             class="mt-5 divide-y divide-sand-300 border-y border-sand-300">
                            @foreach($items as $faq)
                                <div>
                                    <h3>
                                        <button type="button" @click="toggle({{ $faq->id }})"
                                                :aria-expanded="isOpen({{ $faq->id }}) ? 'true' : 'false'"
                                                aria-controls="faq-answer-{{ $faq->id }}"
                                                class="flex w-full items-center justify-between gap-4 py-5 text-right font-bold transition hover:text-clay-600">
                                            {{ $faq->question }}
                                            <x-icon name="chevron-down" size="18"
                                                    class="shrink-0 text-ink-400 transition-transform duration-300"
                                                    ::class="isOpen({{ $faq->id }}) && 'rotate-180'" />
                                        </button>
                                    </h3>
                                    <div id="faq-answer-{{ $faq->id }}" x-show="isOpen({{ $faq->id }})" x-cloak
                                         x-transition:enter="transition ease-[var(--ease-out-expo)] duration-300"
                                         x-transition:enter-start="opacity-0 -translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         class="pb-5 leading-relaxed text-ink-500">
                                        {{ $faq->answer }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach

                <div class="rounded-[var(--radius-panel)] bg-ink-950 p-7 text-sand-50">
                    <h2 class="text-h3 font-extrabold">پاسخ پرسشتان را پیدا نکردید؟</h2>
                    <p class="mt-3 text-sand-200/65">کارشناسان فنی ما حداکثر تا یک روز کاری پاسخ می‌دهند.</p>
                    <x-cta :href="route('contact', ['type' => 'technical'])" variant="primary" class="mt-6">پرسش خود را بفرستید</x-cta>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
