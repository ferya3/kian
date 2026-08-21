<section class="bg-sand-100 py-20 lg:py-28" aria-labelledby="factory-heading">
    <div class="container-page">
        <x-section-heading
            eyebrow="Our factory"
            title="کارخانه، نه یک عکس روی صفحه"
            lead="روی هر بخش کلیک کنید تا ببینید آنجا دقیقاً چه اتفاقی می‌افتد و با چه ظرفیتی."
            id="factory-heading" />

        <div x-data="factoryMap(@js($factorySections->map(fn($s) => [
                'title' => $s->title,
                'titleEn' => $s->title_en,
                'description' => $s->description,
                'stats' => $s->stats ?? [],
                'slug' => $s->slug,
            ])->all()))"
             class="mt-12 grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8">

            {{-- نقشه --}}
            <div class="relative overflow-hidden rounded-[var(--radius-panel)] border border-ink-900/10 bg-ink-950 lg:col-span-8">
                <x-factory-plan class="aspect-[4/3] w-full sm:aspect-[16/10]" />

                @foreach($factorySections as $index => $section)
                    <button type="button"
                            x-ref="spot{{ $index }}"
                            @click="select({{ $index }})"
                            @keydown.arrow-left.prevent="move(1)"
                            @keydown.arrow-right.prevent="move(-1)"
                            :aria-pressed="active === {{ $index }} ? 'true' : 'false'"
                            :tabindex="active === {{ $index }} ? 0 : -1"
                            class="tap-icon group absolute -translate-x-1/2 -translate-y-1/2"
                            style="left: {{ $section->hotspot_x }}%; top: {{ $section->hotspot_y }}%"
                            aria-label="{{ $section->title }}">

                        <span class="relative grid h-7 w-7 place-items-center">
                            <span class="absolute inset-0 rounded-full transition-all duration-500"
                                  :class="active === {{ $index }} ? 'bg-clay-500/30 scale-150' : 'bg-white/10 group-hover:bg-clay-500/25'"></span>
                            <span class="absolute inset-0 animate-ping rounded-full bg-clay-500/40"
                                  x-show="active === {{ $index }}" style="animation-duration: 2s"></span>
                            <span class="relative h-2.5 w-2.5 rounded-full border-2 transition-colors duration-300"
                                  :class="active === {{ $index }} ? 'bg-sand-50 border-sand-50' : 'bg-clay-500 border-clay-300'"></span>
                        </span>

                        <span class="pointer-events-none absolute left-9 top-1/2 hidden -translate-y-1/2 whitespace-nowrap rounded-lg bg-ink-900/90 px-2.5 py-1 text-micro text-sand-50 opacity-0 backdrop-blur transition-opacity duration-300 group-hover:opacity-100 lg:block"
                              :class="active === {{ $index }} && 'opacity-100'">
                            {{ $section->title }}
                        </span>
                    </button>
                @endforeach

                <p class="absolute bottom-4 right-5 text-micro text-sand-200/40">
                    <span x-show="autoplay">در حال پیمایش خودکار — برای کنترل، کلیک کنید</span>
                    <span x-show="!autoplay" x-cloak>با کلیدهای جهت هم می‌توانید جابه‌جا شوید</span>
                </p>
            </div>

            {{-- پنل توضیح --}}
            <div class="lg:col-span-4">
                <div class="sticky top-28 flex h-full flex-col rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-6 lg:p-7">
                    <p class="eyebrow text-clay-600" x-text="current.titleEn"></p>
                    <h3 class="mt-2 text-h3 font-extrabold" x-text="current.title"></h3>
                    <p class="mt-4 flex-1 leading-relaxed text-ink-500" x-text="current.description"></p>

                    <ul class="mt-6 flex flex-wrap gap-2 border-t border-sand-200 pt-5">
                        <template x-for="stat in current.stats" :key="stat">
                            <li class="tech rounded-full border border-sand-300 bg-sand-100 px-3 py-1 text-meta text-ink-600" x-text="stat"></li>
                        </template>
                    </ul>

                    <nav class="mt-6 flex flex-wrap gap-1.5" aria-label="بخش‌های کارخانه">
                        @foreach($factorySections as $index => $section)
                            <button type="button" @click="select({{ $index }})"
                                    class="tap rounded-full px-4 py-2 text-meta font-semibold transition"
                                    :class="active === {{ $index }} ? 'bg-ink-900 text-sand-50' : 'bg-sand-200 text-ink-500 hover:bg-sand-300'">
                                {{ $section->title }}
                            </button>
                        @endforeach
                    </nav>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            <x-cta :href="route('factory')" variant="dark">صفحه کامل کارخانه</x-cta>
            <x-cta :href="route('contact', ['type' => 'general'])" variant="ghost" icon="pin">درخواست بازدید</x-cta>
        </div>
    </div>
</section>
