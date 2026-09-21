<x-layouts.app>
    <article>
        <x-page-hero :image="$article->cover_image" :image-alt="$article->title"
                     :eyebrow="$article->category" :title="$article->title" :lead="$article->excerpt">
            <p class="mt-8 flex flex-wrap items-center gap-3 text-[0.875rem] text-ink-400">
                <span>{{ $article->author }}</span>
                <span class="h-1 w-1 rounded-full bg-ink-300"></span>
                <span>{{ \App\Support\Jalali::format($article->published_at) }}</span>
                <span class="h-1 w-1 rounded-full bg-ink-300"></span>
                <span class="tech">{{ __('site.articles.reading', ['minutes' => \App\Support\Jalali::digits($article->reading_time)]) }}</span>
            </p>
        </x-page-hero>

        <section class="bg-sand-100 section-b">
            <div class="container-page">
                <div class="mx-auto max-w-3xl">
                    @foreach(preg_split('/\n+/', $article->body ?? '') as $block)
                        @php $block = trim($block); @endphp
                        @continue($block === '')

                        @if(str_starts_with($block, '### '))
                            <h2 class="mt-10 text-h3 font-extrabold">{{ substr($block, 4) }}</h2>
                        @else
                            <p class="mt-5 text-lead text-ink-600">{{ $block }}</p>
                        @endif
                    @endforeach

                    <div class="mt-12 rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-6">
                        <h2 class="font-bold">{{ __('site.articles.question') }}</h2>
                        <p class="mt-2 text-ink-500">{{ __('site.articles.question_lead') }}</p>
                        <x-cta :href="route('contact', ['type' => 'technical'])" variant="primary" size="sm" class="mt-5">{{ __('site.product.advice') }}</x-cta>
                    </div>
                </div>
            </div>
        </section>

        @if($more->isNotEmpty())
            <section class="bg-sand-50 section">
                <div class="container-page">
                    <x-section-heading eyebrow="More" :title="__('site.articles.more')" />
                    <ul class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        @foreach($more as $item)
                            <li>
                                <a href="{{ route('articles.show', $item) }}"
                                   class="group flex h-full flex-col rounded-2xl border border-sand-300 bg-sand-100 p-5 transition hover:border-clay-300">
                                    <span class="eyebrow text-clay-600">{{ $item->category }}</span>
                                    <span class="mt-2 font-bold leading-snug group-hover:text-clay-700">{{ $item->title }}</span>
                                    <span class="tech mt-auto pt-4 text-micro text-ink-400">{{ __('site.articles.minutes', ['minutes' => \App\Support\Jalali::digits($item->reading_time)]) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif
    </article>
</x-layouts.app>
