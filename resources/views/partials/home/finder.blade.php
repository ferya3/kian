<section id="find-your-block" class="relative bg-sand-100 py-20 lg:py-28" aria-labelledby="finder-heading">
    <div class="container-page">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7">
                <p class="eyebrow text-clay-600" data-reveal>Find your block</p>
                <h2 id="finder-heading" class="mt-3 text-h2 font-extrabold text-balance" data-reveal>
                    محصول مناسب پروژه‌ی خود را پیدا کنید
                </h2>
                <p class="mt-5 max-w-2xl text-lead text-ink-500" data-reveal>
                    چهار پرسش، سه پیشنهاد. به‌جای مرور کاتالوگ، از نیاز واقعی پروژه شروع کنید:
                    نوع سازه، جای دیوار در پلان، ضخامت مجاز و انتظار حرارتی.
                </p>
            </div>

            <div class="lg:col-span-5 lg:pb-2" data-reveal>
                <ul class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:grid-cols-1">
                    @foreach(config('kian.audiences') as $key => $audience)
                        <li>
                            <a href="{{ route($audience['route']) }}"
                               class="group flex items-center gap-3 rounded-xl border border-sand-300 bg-sand-50 px-4 py-3 transition hover:border-clay-300 hover:bg-clay-50">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-clay-100 text-clay-600 transition group-hover:bg-clay-500 group-hover:text-white">
                                    <x-icon :name="$audience['icon']" size="18" />
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-meta font-bold">{{ $audience['label'] }}</span>
                                    <span class="block truncate text-xs text-ink-400">{{ $audience['question'] }}</span>
                                </span>
                                <x-icon name="chevron-left" size="16" class="mr-auto shrink-0 text-ink-300 transition group-hover:text-clay-500" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-10" data-reveal>
            @include('partials.finder-form')
        </div>
    </div>
</section>
