<section class="bg-sand-100 section-lg" aria-labelledby="factory-heading">
    <div class="container-page">
        <x-section-heading
            eyebrow="Our factory"
            :title="__('site.home.factory.title')"
            :lead="__('site.home.factory.lead')"
            id="factory-heading" />

        <div x-data="factoryMap(@js($factorySections->map(fn($s) => [
                'title' => $s->title,
                'titleEn' => $s->title_en,
                'description' => $s->description,
                'stats' => $s->stats ?? [],
                'slug' => $s->slug,
                // مختصات، برای یافتنِ نزدیک‌ترین بخش به ضربه‌ی انگشت روی گوشی
                'hotspotX' => (float) $s->hotspot_x,
                'hotspotY' => (float) $s->hotspot_y,
            ])->all()))"
             class="mt-12 grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8">

            {{-- نقشه --}}
            {{--
                lg:self-start لازم است و تزیینی نیست: خانه‌ی گرید به‌طور
                پیش‌فرض تا قدِ بلندترین همسایه کش می‌آید، و چون SVG کلاسِ
                h-full دارد، همان کشیدگی را می‌گرفت و نسبتِ کادر را می‌شکست —
                در ۱۰۲۴ نقشه ۵۲۷×۵۲۳ می‌شد به‌جای ۵۲۷×۳۲۷. آن‌وقت تصویر درونِ
                کادر وسط‌چین می‌ماند ولی نشانه‌ها که با درصدِ کادر جای
                می‌گیرند، تا ۵۰ پیکسل از ساختمان‌ها فاصله می‌گرفتند.
            --}}
            <div class="relative overflow-hidden rounded-[var(--radius-panel)] border border-ink-900/10 bg-ink-950 lg:col-span-8 lg:self-start">
                {{--
                    نسبتِ کادر همان نسبتِ viewBox نقشه است (۱۰۰۰×۶۲۰) و این
                    لازم است، نه سلیقه: نشانه‌ها با درصدِ کادر جای می‌گیرند،
                    ولی SVG با preserveAspectRatio پیش‌فرض داخلِ کادر جا
                    می‌شود. تا وقتی دو نسبت یکی نباشند، نقشه در کادر نواری
                    خالی می‌گذارد و نشانه‌ها از روی ساختمان‌ها سُر می‌خورند —
                    روی کادرِ ۴:۳ پیشین تا ۱۲ پیکسل.
                --}}
                <x-factory-plan class="aspect-[1000/620] w-full" />

                {{--
                    نشانه‌های روی نقشه — فقط از lg به بالا.

                    هدف لمسی‌شان ۴۴ پیکسل است، ولی نقشه روی گوشی ۳۵۰ پیکسل عرض
                    دارد و دو نشانه‌ی همسایه ۰٫۴ پیکسل از هم فاصله می‌گرفتند:
                    انگشت عملاً نمی‌توانست بینشان انتخاب کند. همان انتخاب،
                    درست پایین‌تر، با بیضی‌های تمام‌اندازه در دسترس است — پس
                    روی موبایل نقشه تصویر می‌ماند و بیضی‌ها کنترل.

                    display: none و نه pointer-events: none — وگرنه دکمه‌ها از
                    دسترس ماوس خارج می‌شدند ولی در ترتیب Tab و درخت دسترس‌پذیری
                    می‌ماندند.
                --}}
                <div class="pointer-events-none absolute inset-0 hidden lg:block">
                @foreach($factorySections as $index => $section)
                    <button type="button"
                            x-ref="spot{{ $index }}"
                            @click="select({{ $index }})"
                            @keydown.arrow-left.prevent="move(1)"
                            @keydown.arrow-right.prevent="move(-1)"
                            :aria-pressed="active === {{ $index }} ? 'true' : 'false'"
                            :tabindex="active === {{ $index }} ? 0 : -1"
                            class="tap-icon group pointer-events-auto absolute -translate-x-1/2 -translate-y-1/2"
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
                </div>

                {{--
                    همان نشانه‌ها روی گوشی — دیدنی، ولی نه دکمه.
                    انتخاب با یک لایه‌ی لمسی روی کلِ نقشه انجام می‌شود که هر
                    ضربه را به نزدیک‌ترین نشانه می‌رساند (selectNearest در
                    factory-map.js توضیحش را دارد).

                    لایه aria-hidden است و نه یک دکمه‌ی واقعی: همان شش انتخاب،
                    کمی پایین‌تر، به‌صورت بیضی‌های تمام‌اندازه در دسترسِ
                    صفحه‌کلید و صفحه‌خوان هستند. دو مسیرِ موازی برای یک کار،
                    فهرستِ Tab را شلوغ می‌کند بی‌آنکه چیزی اضافه کند.
                --}}
                <div class="absolute inset-0 lg:hidden">
                    <div x-ref="tapLayer" @click="selectNearest($event)"
                         class="absolute inset-0" aria-hidden="true"></div>

                    @foreach($factorySections as $index => $section)
                        <span class="pointer-events-none absolute grid h-7 w-7 -translate-x-1/2 -translate-y-1/2 place-items-center"
                              style="left: {{ $section->hotspot_x }}%; top: {{ $section->hotspot_y }}%"
                              aria-hidden="true">
                            <span class="absolute inset-0 rounded-full transition-all duration-500"
                                  :class="active === {{ $index }} ? 'bg-clay-500/30 scale-150' : 'bg-white/10'"></span>
                            <span class="absolute inset-0 animate-ping rounded-full bg-clay-500/40"
                                  x-show="active === {{ $index }}" style="animation-duration: 2s"></span>
                            <span class="relative h-2.5 w-2.5 rounded-full border-2 transition-colors duration-300"
                                  :class="active === {{ $index }} ? 'bg-sand-50 border-sand-50' : 'bg-clay-500 border-clay-300'"></span>
                        </span>
                    @endforeach
                </div>

                <p class="pointer-events-none absolute bottom-4 right-5 text-micro text-sand-200/40">
                    <span x-show="autoplay">{{ __('site.home.factory.autoplay') }}</span>
                    {{-- راهنمای کلیدهای جهت فقط آنجا که صفحه‌کلید هست --}}
                    <span x-show="!autoplay" x-cloak class="hidden lg:inline">{{ __('site.home.factory.keys') }}</span>
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

                    <nav class="tap-row mt-6" aria-label="{{ __('site.home.factory.sections') }}">
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
            <x-cta :href="route('factory')" variant="dark">{{ __('site.home.factory.full') }}</x-cta>
            <x-cta :href="route('contact', ['type' => 'general'])" variant="ghost" icon="pin">{{ __('site.home.factory.visit') }}</x-cta>
        </div>
    </div>
</section>
