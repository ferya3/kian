<x-layouts.app>
    <article>
        <section class="relative overflow-hidden bg-ink-950 text-sand-50">
            <div class="absolute inset-0 opacity-45">
                <x-project-cover :project="$project" />
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-ink-950 via-ink-950/85 to-ink-950/55"></div>

            <div class="container-page relative section-lg">
                <x-breadcrumbs class="[&_*]:text-sand-200/60" />

                <p class="eyebrow mt-6 text-clay-400">{{ $project->category?->name }} · {{ $project->category?->name_en }}</p>
                <h1 class="mt-3 max-w-4xl text-h1 font-extrabold text-balance">{{ $project->title }}</h1>
                <p class="mt-5 max-w-3xl text-lead text-sand-200/75">{{ $project->summary }}</p>

                <dl class="mt-10 grid grid-cols-2 gap-px overflow-hidden rounded-[var(--radius-panel)] border border-white/10 bg-white/10 lg:grid-cols-4">
                    @foreach(array_filter([
                        [__('site.projects.location'), $project->city.($project->province && $project->province !== $project->city ? '، '.$project->province : '')],
                        [__('site.projects.year_built'), \App\Support\Jalali::digits($project->year)],
                        $project->area_sqm ? [__('site.projects.area'), __('site.projects.sqm', ['value' => \App\Support\Jalali::digits(number_format($project->area_sqm))])] : null,
                        $project->blocks_used ? [__('site.projects.blocks'), __('site.projects.units', ['value' => \App\Support\Jalali::digits(number_format($project->blocks_used))])] : null,
                    ]) as [$label, $value])
                        <div class="bg-ink-950/60 px-5 py-5">
                            <dt class="text-meta text-sand-200/50">{{ $label }}</dt>
                            <dd class="tech mt-1 text-lg font-bold">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </section>

        <x-gallery :images="$project->gallery" :title="__('site.projects.gallery')" eyebrow="Gallery" class="bg-sand-100 section" />

        <section class="bg-sand-50 section">
            <div class="container-page grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-14">
                <div class="lg:col-span-7">
                    <h2 class="text-h3 font-extrabold">{{ __('site.projects.problem') }}</h2>
                    <div class="mt-5 space-y-4 text-lead text-ink-600">
                        @foreach(preg_split('/\n+/', $project->description ?? '') as $paragraph)
                            @if(trim($paragraph))
                                <p>{{ $paragraph }}</p>
                            @endif
                        @endforeach
                    </div>
                </div>

                <aside class="lg:col-span-5">
                    <div class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-100 p-6">
                        <h2 class="font-bold">{{ __('site.projects.info') }}</h2>
                        <dl class="mt-4 space-y-3 text-[0.9375rem]">
                            @foreach(array_filter([
                                [__('site.projects.client'), $project->client],
                                [__('site.projects.architect'), $project->architect],
                                [__('site.projects.city'), $project->city],
                                [__('site.projects.year'), \App\Support\Jalali::digits($project->year)],
                            ], fn($r) => filled($r[1])) as [$label, $value])
                                <div class="flex justify-between gap-4 border-b border-sand-300 pb-3 last:border-0 last:pb-0">
                                    <dt class="text-ink-400">{{ $label }}</dt>
                                    <dd class="font-semibold">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>

                    @if($project->products->isNotEmpty())
                        <div class="mt-4 rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-6">
                            <h2 class="font-bold">{{ __('site.projects.products_used') }}</h2>
                            <ul class="mt-4 space-y-2">
                                @foreach($project->products as $product)
                                    <li>
                                        <a href="{{ route('products.show', $product) }}"
                                           class="group flex items-center justify-between gap-3 rounded-lg border border-sand-200 px-4 py-3 transition hover:border-clay-300 hover:bg-clay-50">
                                            <span>
                                                <span class="block font-semibold group-hover:text-clay-700">{{ $product->name }}</span>
                                                <span class="tech block text-micro text-ink-400">{{ \App\Support\Jalali::digits($product->dimensionLabel()) }} cm</span>
                                            </span>
                                            <x-icon name="chevron-left" size="16" class="text-ink-300 transition group-hover:text-clay-500" />
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </aside>
            </div>
        </section>

        @if($more->isNotEmpty())
            <section class="bg-sand-100 section">
                <div class="container-page">
                    <x-section-heading eyebrow="More" :title="__('site.projects.similar')" />
                    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" data-reveal-stagger="100">
                        @foreach($more as $item)
                            <x-project-card :project="$item" data-reveal />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </article>
    <x-mobile-action-bar
        :primary-href="route('contact', ['type' => 'quote'])"
        :primary-label="__('site.projects.similar_cta')"
        :secondary-href="route('projects.index')"
        :secondary-label="__('site.nav.items.projects_index')"
        secondary-icon="grid" />
</x-layouts.app>
