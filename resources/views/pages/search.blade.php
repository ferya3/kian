<x-layouts.app>
    <x-page-hero
        eyebrow="Search"
        :title="$term ? __('site.search.results_for', ['term' => $term]) : __('site.nav.search')"
        compact>
        <form action="{{ route('search') }}" method="GET" class="relative mt-8 max-w-2xl">
            <label for="page-search" class="sr-only">{{ __('site.search.term') }}</label>
            <input id="page-search" name="q" type="search" value="{{ $term }}" autofocus
                   placeholder="{{ __('site.search.placeholder') }}"
                   class="w-full rounded-full border border-sand-300 bg-sand-100 py-4 pr-14 pl-32 outline-none focus:border-clay-400 focus:bg-sand-50">
            <x-icon name="search" size="20" class="pointer-events-none absolute right-5 top-1/2 -translate-y-1/2 text-ink-300" />
            <button type="submit" class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-ink-900 px-6 py-2.5 text-[0.875rem] font-semibold text-sand-50 transition hover:bg-clay-600">
                {{ __('site.nav.search') }}
            </button>
        </form>
    </x-page-hero>

    <section class="bg-sand-100 section-b">
        <div class="container-page">
            @php $total = collect($results)->sum(fn($c) => $c->count()); @endphp

            @if(!$term)
                <p class="text-ink-400">{{ __('site.search.start') }}</p>
            @elseif($total === 0)
                <div class="rounded-[var(--radius-panel)] border border-dashed border-sand-300 py-16 text-center">
                    <p class="text-lg font-bold">{{ __('site.search.none') }}</p>
                    <p class="mt-2 text-ink-400">{{ __('site.search.none_hint') }}</p>
                    <x-cta :href="route('finder.show')" variant="primary" class="mt-6">{{ __('site.actions.finder') }}</x-cta>
                </div>
            @else
                <p class="tech text-[0.875rem] text-ink-400">{{ __('site.search.count', ['count' => \App\Support\Jalali::digits($total)]) }}</p>

                @if($results['products']->isNotEmpty())
                    <h2 class="mt-8 text-h3 font-extrabold">{{ __('site.nav.items.products_index') }}</h2>
                    <div class="mt-5 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($results['products'] as $product)
                            <x-product-card :product="$product" :showBars="false" />
                        @endforeach
                    </div>
                @endif

                @if($results['projects']->isNotEmpty())
                    <h2 class="mt-12 text-h3 font-extrabold">{{ __('site.nav.items.projects_index') }}</h2>
                    <div class="mt-5 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($results['projects'] as $project)
                            <x-project-card :project="$project" />
                        @endforeach
                    </div>
                @endif

                @if($results['documents']->isNotEmpty())
                    <h2 class="mt-12 text-h3 font-extrabold">{{ __('site.search.files') }}</h2>
                    <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($results['documents'] as $document)
                            <x-document-row :document="$document" />
                        @endforeach
                    </div>
                @endif

                @if($results['articles']->isNotEmpty())
                    <h2 class="mt-12 text-h3 font-extrabold">{{ __('site.nav.items.articles_index') }}</h2>
                    <ul class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($results['articles'] as $article)
                            <li>
                                <a href="{{ route('articles.show', $article) }}"
                                   class="group flex h-full flex-col rounded-2xl border border-sand-300 bg-sand-50 p-5 transition hover:border-clay-300">
                                    <span class="eyebrow text-clay-600">{{ $article->category }}</span>
                                    <span class="mt-2 font-bold leading-snug group-hover:text-clay-700">{{ $article->title }}</span>
                                    <span class="mt-2 line-clamp-2 text-[0.875rem] text-ink-500">{{ $article->excerpt }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endif
        </div>
    </section>
</x-layouts.app>
