@php
    /*
    | نشانِ هر دسته.
    |
    | روی پانلِ جمع‌شده تنها چیزی است که دیده می‌شود، پس کارش تزیین نیست:
    | باید بشود از روی همان باریکه حدس زد پشتش چه پروژه‌ای است. نشان‌ها از
    | همان مجموعه‌ی درون‌خطیِ x-icon می‌آیند و درخواست اضافه‌ای نمی‌سازند.
    */
    $marks = [
        'residential' => 'grid',
        'commercial' => 'blueprint',
        'industrial' => 'factory',
        'mass-housing' => 'layers',
        'public' => 'compass',
    ];
@endphp

<section class="bg-sand-50 section-lg" aria-labelledby="projects-heading">
    <div class="container-page">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-section-heading
                eyebrow="Reference projects"
                title="پروژه‌های اجراشده"
                lead="از برج اداری بیست‌ودو طبقه تا هزار و دویست واحد مسکن ملی — هر پروژه یک مسئله‌ی متفاوت داشت."
                id="projects-heading" class="lg:max-w-2xl" />

            <div data-reveal>
                <x-cta :href="route('projects.index')" variant="ghost">همه پروژه‌ها</x-cta>
            </div>
        </div>

        {{--
            آکاردئون پروژه‌ها.

            روی گوشی ستونی است و از md به بعد ردیفی — همان تعامل، چرخیده.
            دلیلش ساده است: آکاردئونِ افقی در ۳۶۰ پیکسل یعنی شش باریکه‌ی
            شصت‌پیکسلی که هیچ‌کدام تصویری نشان نمی‌دهند.

            هر پانل دو کنترل دارد و نه یکی: دکمه‌ای که تمام سطح را می‌گیرد و
            پانل را باز می‌کند، و عنوانِ پیونددار که فقط روی پانلِ باز قابل
            کلیک است. تودرتو کردنشان نامعتبر بود، و یکی‌کردنشان یعنی کاربرِ
            لمسی برای دیدنِ تصویر مجبور شود صفحه را ترک کند.
        --}}
        <ul x-data="projectSelector({{ $projects->count() }})"
            x-ref="rail"
            @keydown="onKey"
            class="pj-rail mt-12"
            data-reveal-stagger="140">
            @foreach($projects as $i => $project)
                <li class="pj-slot" :style="{ flexGrow: active({{ $i }}) ? 7 : 1 }">
                  <div class="pj-enter" data-reveal>
                    <div class="pj-face" :class="{ 'is-open': active({{ $i }}) }">
                        <div class="pj-cover">
                            <x-project-cover :project="$project" />
                        </div>

                        <div class="pj-scrim" aria-hidden="true"></div>

                        {{-- تمام سطح، تا روی باریکه هم هدفِ لمسِ درستی باشد --}}
                        <button type="button"
                                class="pj-hit"
                                @click="select({{ $i }})"
                                @focus="select({{ $i }})"
                                :aria-expanded="active({{ $i }})"
                                aria-controls="pj-label-{{ $project->id }}">
                            <span class="sr-only">نمایش پروژه‌ی {{ $project->title }}</span>
                        </button>

                        <div id="pj-label-{{ $project->id }}" class="pj-label">
                            <span class="pj-mark" aria-hidden="true">
                                <x-icon :name="$marks[$project->category?->slug] ?? 'grid'" size="20" />
                            </span>

                            <div class="pj-text">
                                <h3 class="pj-title">
                                    <a href="{{ route('projects.show', $project) }}">{{ $project->title }}</a>
                                </h3>
                                <p class="pj-meta">
                                    <span>{{ $project->city }}</span>
                                    <span class="pj-dot"></span>
                                    <span class="tech">{{ \App\Support\Jalali::digits($project->year) }}</span>
                                    @if($project->area_sqm)
                                        <span class="pj-dot"></span>
                                        <span class="tech"><bdi dir="ltr">{{ \App\Support\Jalali::digits(number_format($project->area_sqm)) }} m²</bdi></span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                  </div>
                </li>
            @endforeach
        </ul>
    </div>
</section>
