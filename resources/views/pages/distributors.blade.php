<x-layouts.app>
    <x-page-hero
        eyebrow="Distributors"
        title="نمایندگان فروش"
        lead="برای مقادیر کمتر از یک تریلی، نزدیک‌ترین نمایندگی سریع‌تر و ارزان‌تر از تحویل مستقیم کارخانه است." />

    <section class="bg-sand-100 pb-20">
        <div class="container-page">
            <div class="flex flex-wrap items-center gap-1.5 border-b border-sand-300 pb-5">
                <span class="ml-2 text-meta font-semibold text-ink-400">استان:</span>
                <a href="{{ route('distributors') }}"
                   class="tap rounded-full px-4 py-2 text-[0.875rem] font-semibold transition {{ $selected ? 'bg-sand-200 text-ink-600 hover:bg-sand-300' : 'bg-ink-900 text-sand-50' }}">همه</a>
                @foreach($provinces as $province)
                    <a href="{{ route('distributors', ['province' => $province]) }}"
                       class="tap rounded-full px-4 py-2 text-[0.875rem] font-semibold transition {{ $selected === $province ? 'bg-ink-900 text-sand-50' : 'bg-sand-200 text-ink-600 hover:bg-sand-300' }}">{{ $province }}</a>
                @endforeach
            </div>

            <div class="mt-8 space-y-8">
                @foreach($distributors as $province => $group)
                    <div>
                        <h2 class="text-h3 font-extrabold">{{ $province }}</h2>
                        <ul class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3" data-reveal-stagger="70">
                            @foreach($group as $distributor)
                                <li data-reveal class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-5">
                                    <p class="font-bold">{{ $distributor->name }}</p>
                                    <p class="mt-0.5 text-[0.875rem] text-ink-400">{{ $distributor->city }} — {{ $distributor->manager }}</p>
                                    @if($distributor->address)
                                        <p class="mt-3 flex gap-2 text-[0.9375rem] leading-relaxed text-ink-500">
                                            <x-icon name="pin" size="16" class="mt-1 shrink-0 text-clay-500" />
                                            {{ $distributor->address }}
                                        </p>
                                    @endif
                                    <div class="mt-4 flex flex-wrap gap-2 border-t border-sand-200 pt-4">
                                        @if($distributor->phone)
                                            <a href="tel:{{ $distributor->phone }}" class="tech tap gap-1.5 rounded-full bg-sand-200 px-4 py-2 text-meta font-semibold transition hover:bg-sand-300">
                                                <x-icon name="phone" size="14" />
                                                {{ $distributor->phone }}
                                            </a>
                                        @endif
                                        @if($distributor->mobile)
                                            <a href="tel:{{ $distributor->mobile }}" class="tech tap gap-1.5 rounded-full bg-sand-200 px-4 py-2 text-meta font-semibold transition hover:bg-sand-300">
                                                <x-icon name="phone" size="14" />
                                                {{ $distributor->mobile }}
                                            </a>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>

            <div class="mt-12 rounded-[var(--radius-panel)] bg-ink-950 p-7 text-sand-50 lg:p-10">
                <div class="flex flex-wrap items-center justify-between gap-6">
                    <div class="max-w-2xl">
                        <h2 class="text-h3 font-extrabold">در استان شما نماینده نداریم؟</h2>
                        <p class="mt-3 text-sand-200/65">
                            اگر در حوزه‌ی مصالح ساختمانی فعال هستید و انبار و شبکه‌ی توزیع دارید، شرایط نمایندگی را بررسی می‌کنیم.
                        </p>
                    </div>
                    <x-cta :href="route('contact', ['type' => 'distributor'])" variant="primary">درخواست نمایندگی</x-cta>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
