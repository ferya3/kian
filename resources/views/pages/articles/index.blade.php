<x-layouts.app>
    <x-page-hero
        eyebrow="Knowledge"
        title="دانش فنی"
        lead="مقالاتی که واحد فنی ما می‌نویسد: مقایسه‌ی صادقانه‌ی مصالح، محاسبات مبحث ۱۹، و آنچه در کارگاه‌ها بیشتر از همه اشتباه اجرا می‌شود." />

    <section class="bg-sand-100 pb-20">
        <div class="container-page">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-reveal-stagger="90">
                @foreach($articles as $article)
                    <article data-reveal class="group relative flex flex-col overflow-hidden rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 transition hover:-translate-y-1 hover:border-clay-300 hover:shadow-lift">
                        <div class="relative aspect-[16/9] overflow-hidden bg-gradient-to-bl from-clay-100 via-sand-200 to-sand-300">
                            <div class="absolute inset-0 opacity-70"
                                 style="background-image: repeating-linear-gradient(-45deg, rgba(180,85,45,.1) 0 12px, transparent 12px 34px)"></div>
                            <span class="eyebrow absolute bottom-4 right-4 rounded-full bg-sand-50/90 px-3 py-1 text-[0.625rem] text-clay-700 backdrop-blur">
                                {{ $article->category }}
                            </span>
                        </div>

                        <div class="flex flex-1 flex-col p-5">
                            <h2 class="text-lg font-extrabold leading-snug">
                                <a href="{{ route('articles.show', $article) }}" class="after:absolute after:inset-0">{{ $article->title }}</a>
                            </h2>
                            <p class="mt-3 line-clamp-3 flex-1 text-[0.9375rem] leading-relaxed text-ink-500">{{ $article->excerpt }}</p>
                            <p class="mt-5 flex items-center gap-3 border-t border-sand-200 pt-4 text-[0.75rem] text-ink-400">
                                <span>{{ \App\Support\Jalali::format($article->published_at) }}</span>
                                <span class="h-1 w-1 rounded-full bg-ink-300"></span>
                                <span class="tech">{{ \App\Support\Jalali::digits($article->reading_time) }} دقیقه مطالعه</span>
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $articles->links() }}
            </div>
        </div>
    </section>
</x-layouts.app>
