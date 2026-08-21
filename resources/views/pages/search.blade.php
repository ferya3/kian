<x-layouts.app>
    <x-page-hero
        eyebrow="Search"
        :title="$term ? 'نتیجه جستجوی «'.$term.'»' : 'جستجو'"
        compact>
        <form action="{{ route('search') }}" method="GET" class="relative mt-8 max-w-2xl">
            <label for="page-search" class="sr-only">عبارت جستجو</label>
            <input id="page-search" name="q" type="search" value="{{ $term }}" autofocus
                   placeholder="نام محصول، ضخامت، پروژه یا فایل فنی…"
                   class="w-full rounded-full border border-sand-300 bg-sand-100 py-4 pr-14 pl-32 text-[1.0625rem] outline-none focus:border-clay-400 focus:bg-sand-50">
            <x-icon name="search" size="20" class="pointer-events-none absolute right-5 top-1/2 -translate-y-1/2 text-ink-300" />
            <button type="submit" class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-ink-900 px-6 py-2.5 text-[0.875rem] font-semibold text-sand-50 transition hover:bg-clay-600">
                جستجو
            </button>
        </form>
    </x-page-hero>

    <section class="bg-sand-100 pb-20">
        <div class="container-page">
            @php $total = collect($results)->sum(fn($c) => $c->count()); @endphp

            @if(!$term)
                <p class="text-ink-400">برای شروع، عبارتی را در کادر بالا بنویسید.</p>
            @elseif($total === 0)
                <div class="rounded-[var(--radius-panel)] border border-dashed border-sand-300 py-16 text-center">
                    <p class="text-lg font-bold">چیزی پیدا نشد</p>
                    <p class="mt-2 text-ink-400">شاید املای دیگری را امتحان کنید، یا از موتور انتخاب محصول استفاده کنید.</p>
                    <x-cta :href="route('finder.show')" variant="primary" class="mt-6">انتخاب محصول</x-cta>
                </div>
            @else
                <p class="tech text-[0.875rem] text-ink-400">{{ \App\Support\Jalali::digits($total) }} نتیجه</p>

                @if($results['products']->isNotEmpty())
                    <h2 class="mt-8 text-h3 font-extrabold">محصولات</h2>
                    <div class="mt-5 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($results['products'] as $product)
                            <x-product-card :product="$product" :showBars="false" />
                        @endforeach
                    </div>
                @endif

                @if($results['projects']->isNotEmpty())
                    <h2 class="mt-12 text-h3 font-extrabold">پروژه‌ها</h2>
                    <div class="mt-5 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($results['projects'] as $project)
                            <x-project-card :project="$project" />
                        @endforeach
                    </div>
                @endif

                @if($results['documents']->isNotEmpty())
                    <h2 class="mt-12 text-h3 font-extrabold">فایل‌های فنی</h2>
                    <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($results['documents'] as $document)
                            <x-document-row :document="$document" />
                        @endforeach
                    </div>
                @endif

                @if($results['articles']->isNotEmpty())
                    <h2 class="mt-12 text-h3 font-extrabold">مقالات</h2>
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
