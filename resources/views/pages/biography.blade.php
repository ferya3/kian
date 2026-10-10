<x-layouts.app>
    <x-page-hero
        eyebrow="Biography"
        :title="__('site.nav.items.biography')"
        :lead="$lead"
        variant="dark" />

    {{-- ما که هستیم --}}
    <section class="bg-sand-50 section-lg">
        <div class="container-page grid grid-cols-1 gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-5">
                <x-section-heading eyebrow="Who we are" :title="__('site.biography.intro_title')" />
            </div>
            <div class="space-y-5 text-lead text-ink-600 lg:col-span-7">
                <p>{{ __('site.biography.intro_1') }}</p>
                <p>{{ __('site.biography.intro_2') }}</p>
            </div>
        </div>
    </section>

    {{--
        خطِ زمان. به ترتیبِ سال چیده می‌شود و «امروز» همیشه آخر است؛ مدیر
        برای افزودنِ رویدادی در وسط لازم نیست بقیه را از نو شماره بزند.
    --}}
    @if($milestones->isNotEmpty())
        <section class="bg-sand-100 section">
            <div class="container-page">
                <x-section-heading eyebrow="Timeline" :title="__('site.biography.timeline_title')"
                    :lead="__('site.biography.timeline_lead')" />

                <ol class="relative mt-12 max-w-4xl space-y-10 border-s-2 border-clay-200 ps-8 lg:ps-12" data-reveal-stagger="120">
                    @foreach($milestones as $milestone)
                        <li data-reveal data-milestone class="relative">
                            {{-- نقطه روی خط --}}
                            <span aria-hidden="true"
                                  class="absolute -start-[calc(2rem+7px)] top-2 h-3 w-3 rounded-full bg-clay-500 ring-4 ring-sand-100 lg:-start-[calc(3rem+7px)]"></span>

                            <p class="tech text-h3 font-extrabold leading-none text-clay-600">{{ $milestone->yearLabel() }}</p>
                            <h3 class="mt-3 text-card font-bold text-ink-900">{{ $milestone->title }}</h3>

                            @if(filled($milestone->text))
                                <p class="mt-2 max-w-2xl leading-relaxed text-ink-500">{{ $milestone->text }}</p>
                            @endif

                            @if($src = \App\Support\Media::url($milestone->image))
                                <img src="{{ $src }}" alt="{{ $milestone->title }}" loading="lazy" decoding="async"
                                     class="mt-5 aspect-[16/10] w-full max-w-xl rounded-[var(--radius-panel)] object-cover">
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    {{-- بنیان‌گذاران و مدیران — فقط اگر کسی نمایش داده شود --}}
    @if($people->isNotEmpty())
        <section class="bg-sand-50 section">
            <div class="container-page">
                <x-section-heading eyebrow="People" :title="__('site.biography.people_title')"
                    :lead="__('site.biography.people_lead')" />

                <ul class="mt-10 grid grid-cols-1 gap-5 md:grid-cols-2" data-reveal-stagger="100">
                    @foreach($people as $person)
                        <li data-reveal data-person class="flex flex-col gap-5 rounded-[var(--radius-panel)] border border-sand-300 bg-sand-100 p-6 sm:flex-row lg:p-7">
                            @if($photo = \App\Support\Media::url($person->photo))
                                <img src="{{ $photo }}" alt="{{ $person->name }}" loading="lazy" decoding="async"
                                     class="h-28 w-28 shrink-0 rounded-2xl object-cover">
                            @else
                                <span aria-hidden="true"
                                      class="grid h-28 w-28 shrink-0 place-items-center rounded-2xl bg-clay-100 text-h2 font-extrabold text-clay-600">
                                    {{ $person->initial() }}
                                </span>
                            @endif

                            <div class="min-w-0">
                                <h3 class="text-card font-bold text-ink-900">{{ $person->name }}</h3>
                                @if(filled($person->role))
                                    <p class="mt-1 text-meta font-semibold text-clay-600">{{ $person->role }}</p>
                                @endif
                                <div class="mt-3 space-y-3 leading-relaxed text-ink-500">
                                    @foreach($person->paragraphs() as $paragraph)
                                        <p>{{ $paragraph }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{--
        روشن و نه تیره: فوتر خودش با نوارِ تیره‌ی «درباره‌ی پروژه‌تان…» شروع
        می‌شود، و دو نوارِ تیره پشتِ هم با شکافی روشن میانشان تکراری می‌نمود.
    --}}
    <section class="border-t border-sand-300 bg-sand-100 section">
        <div class="container-page flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
            <div class="max-w-2xl">
                <h2 class="text-h2 font-extrabold text-ink-900">{{ __('site.biography.cta_title') }}</h2>
                <p class="mt-3 text-lead text-ink-500">{{ __('site.biography.cta_text') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <x-cta :href="route('contact')" variant="primary" icon="pin">{{ __('site.biography.cta_visit') }}</x-cta>
                <x-cta :href="route('about')" variant="ghost">{{ __('site.biography.cta_about') }}</x-cta>
            </div>
        </div>
    </section>
</x-layouts.app>
