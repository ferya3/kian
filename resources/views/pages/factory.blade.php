<x-layouts.app>
    <x-page-hero
        eyebrow="Our factory"
        title="کارخانه"
        lead="شهرک صنعتی مبارکه، دو خط اکستروژن موازی، کوره‌ی تونلی صد و ده متری و آزمایشگاهی که هر بچ تولید را پیش از بارگیری تأیید می‌کند."
        variant="dark" />

    <section class="bg-sand-100 py-16 lg:py-20">
        <div class="container-page">
            <x-stat-band :stats="$stats" />

            <div x-data="factoryMap(@js($sections->map(fn($s) => [
                    'title' => $s->title,
                    'titleEn' => $s->title_en,
                    'description' => $s->description,
                    'stats' => $s->stats ?? [],
                ])->all()))"
                 class="mt-12 grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8">

                <div class="relative overflow-hidden rounded-[var(--radius-panel)] border border-ink-900/10 bg-ink-950 lg:col-span-8">
                    <x-factory-plan class="aspect-[16/10] w-full" />

                    @foreach($sections as $index => $section)
                        <button type="button" x-ref="spot{{ $index }}" @click="select({{ $index }})"
                                @keydown.arrow-left.prevent="move(1)" @keydown.arrow-right.prevent="move(-1)"
                                :aria-pressed="active === {{ $index }} ? 'true' : 'false'"
                                :tabindex="active === {{ $index }} ? 0 : -1"
                                class="group absolute -translate-x-1/2 -translate-y-1/2"
                                style="left: {{ $section->hotspot_x }}%; top: {{ $section->hotspot_y }}%"
                                aria-label="{{ $section->title }}">
                            <span class="relative grid h-7 w-7 place-items-center">
                                <span class="absolute inset-0 rounded-full transition-all duration-500"
                                      :class="active === {{ $index }} ? 'bg-clay-500/30 scale-150' : 'bg-white/10 group-hover:bg-clay-500/25'"></span>
                                <span class="absolute inset-0 animate-ping rounded-full bg-clay-500/40"
                                      x-show="active === {{ $index }}" style="animation-duration: 2s"></span>
                                <span class="relative h-2.5 w-2.5 rounded-full border-2 transition-colors"
                                      :class="active === {{ $index }} ? 'bg-sand-50 border-sand-50' : 'bg-clay-500 border-clay-300'"></span>
                            </span>
                            <span class="pointer-events-none absolute left-9 top-1/2 hidden -translate-y-1/2 whitespace-nowrap rounded-lg bg-ink-900/90 px-2.5 py-1 text-[0.75rem] text-sand-50 opacity-0 backdrop-blur transition-opacity group-hover:opacity-100 lg:block"
                                  :class="active === {{ $index }} && 'opacity-100'">{{ $section->title }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="lg:col-span-4">
                    <div class="sticky top-28 rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-6 lg:p-7">
                        <p class="eyebrow text-clay-600" x-text="current.titleEn"></p>
                        <h2 class="mt-2 text-h3 font-extrabold" x-text="current.title"></h2>
                        <p class="mt-4 leading-relaxed text-ink-500" x-text="current.description"></p>
                        <ul class="mt-6 flex flex-wrap gap-2 border-t border-sand-200 pt-5">
                            <template x-for="stat in current.stats" :key="stat">
                                <li class="tech rounded-full border border-sand-300 bg-sand-100 px-3 py-1 text-[0.8125rem] text-ink-600" x-text="stat"></li>
                            </template>
                        </ul>
                        <nav class="mt-6 flex flex-wrap gap-1.5" aria-label="بخش‌های کارخانه">
                            @foreach($sections as $index => $section)
                                <button type="button" @click="select({{ $index }})"
                                        class="rounded-full px-3 py-1.5 text-[0.8125rem] font-semibold transition"
                                        :class="active === {{ $index }} ? 'bg-ink-900 text-sand-50' : 'bg-sand-200 text-ink-500 hover:bg-sand-300'">
                                    {{ $section->title }}
                                </button>
                            @endforeach
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-sand-50 py-16 lg:py-20">
        <div class="container-page">
            <x-section-heading eyebrow="Certificates" title="گواهی‌نامه‌ها و تأییدیه‌ها"
                lead="اعداد این صفحه بی‌معنی‌اند اگر کسی آن‌ها را تأیید نکرده باشد." />

            <ul class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3" data-reveal-stagger="80">
                @foreach($certificates as $certificate)
                    <li data-reveal class="flex gap-4 rounded-2xl border border-sand-300 bg-sand-100 p-5">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-clay-100 text-clay-600">
                            <x-icon name="shield" size="21" />
                        </span>
                        <div>
                            <h3 class="font-bold leading-snug">{{ $certificate->title }}</h3>
                            <p class="mt-1 text-[0.8125rem] text-ink-400">{{ $certificate->issuer }}</p>
                            @if($certificate->year)
                                <p class="tech mt-1.5 text-[0.75rem] text-ink-300">{{ \App\Support\Jalali::digits($certificate->year) }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="mt-10 flex flex-wrap gap-3">
                <x-cta :href="route('technical.certificates')" variant="dark">دانلود گواهی‌نامه‌ها</x-cta>
                <x-cta :href="route('contact')" variant="ghost" icon="pin">درخواست بازدید از کارخانه</x-cta>
            </div>
        </div>
    </section>
</x-layouts.app>
