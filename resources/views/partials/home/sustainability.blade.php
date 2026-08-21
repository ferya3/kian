<section class="bg-sand-100 py-20 lg:py-28" aria-labelledby="sustainability-heading">
    <div class="container-page">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-12 lg:items-center lg:gap-16">
            <div class="lg:col-span-5">
                <x-section-heading
                    eyebrow="Sustainability"
                    title="خاک، محصول، ساختمان"
                    lead="سفال چرخه‌ی بسته‌ای دارد که در آن هیچ ماده‌ی غریبه‌ای وارد نمی‌شود: از زمین برداشته می‌شود، پخته می‌شود، پنجاه سال کار می‌کند و در پایان دوباره خاک است."
                    id="sustainability-heading" />

                <x-cta :href="route('sustainability')" variant="ghost" class="mt-8">گزارش پایداری</x-cta>
            </div>

            <div class="lg:col-span-7">
                {{-- چرخه‌ی حیات — سه گام --}}
                <ol class="relative grid grid-cols-1 gap-4 sm:grid-cols-3" data-reveal-stagger="120">
                    @foreach([
                        ['خاک رس', 'Extraction', 'برداشت از معدن اختصاصی، با طرح بازسازی محل برداشت.', 'leaf'],
                        ['محصول', 'Production', 'پخت با حرارت بازیافتی؛ ضایعات خام صددرصد به خط برمی‌گردد.', 'factory'],
                        ['ساختمان', 'In use', 'پنجاه سال عملکرد بدون افت، و کاهش دائمی مصرف انرژی ساختمان.', 'shield'],
                    ] as $i => [$title, $en, $text, $icon])
                        <li data-reveal class="relative rounded-2xl border border-sand-300 bg-sand-50 p-5">
                            <span class="tech text-[0.6875rem] uppercase tracking-[0.14em] text-ink-300">{{ $en }}</span>
                            <p class="mt-2 flex items-center gap-2 text-lg font-extrabold">
                                <x-icon :name="$icon" size="19" class="text-clay-500" />
                                {{ $title }}
                            </p>
                            <p class="mt-2 text-[0.9375rem] leading-relaxed text-ink-500">{{ $text }}</p>

                            @if($i < 2)
                                <x-icon name="arrow-left" size="18"
                                        class="absolute -left-3 top-1/2 hidden -translate-y-1/2 text-clay-400 sm:block" />
                            @endif
                        </li>
                    @endforeach
                </ol>

                <dl class="mt-6 grid grid-cols-2 gap-px overflow-hidden rounded-2xl border border-sand-300 bg-sand-300 lg:grid-cols-4">
                    @foreach(\App\Models\Stat::query()->group('sustainability')->get() as $stat)
                        <div class="bg-sand-50 p-5">
                            <dd class="text-2xl font-extrabold text-clay-600">
                                <bdi dir="ltr" class="tech inline-block whitespace-nowrap">
                                    <span data-countup="{{ $stat->value }}" data-decimals="{{ $stat->decimals }}">۰</span>{{ $stat->suffix }}
                                </bdi>
                            </dd>
                            <dt class="mt-1 text-[0.8125rem] leading-snug text-ink-500">{{ $stat->label }}</dt>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </div>
</section>
