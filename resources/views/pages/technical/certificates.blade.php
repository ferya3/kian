<x-layouts.app>
    <x-page-hero
        eyebrow="Certificates & standards"
        title="گواهی‌نامه‌ها و استانداردها"
        lead="هر عددی که در دیتاشیت‌های ما نوشته شده، پشتوانه‌ی آزمون دارد. این صفحه فهرست همان پشتوانه‌هاست." />

    <section class="bg-sand-100 pb-20">
        <div class="container-page grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-14">
            <div class="lg:col-span-7">
                <h2 class="text-h3 font-extrabold">گواهی‌نامه‌های شرکت</h2>
                <ul class="mt-6 space-y-3" data-reveal-stagger="70">
                    @foreach($certificates as $certificate)
                        <li data-reveal class="flex gap-4 rounded-2xl border border-sand-300 bg-sand-50 p-5">
                            <span class="grid h-12 w-12 shrink-0 place-items-center overflow-hidden rounded-xl bg-clay-100 text-clay-600">
                                <x-media :path="$certificate->image" :alt="$certificate->title">
                                    <x-icon name="shield" size="22" />
                                </x-media>
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold leading-snug">{{ $certificate->title }}</h3>
                                <p class="mt-1 text-[0.875rem] text-ink-400">{{ $certificate->issuer }}</p>
                                <p class="tech mt-1.5 flex flex-wrap gap-x-3 text-micro text-ink-300">
                                    @if($certificate->number)<span>شماره {{ $certificate->number }}</span>@endif
                                    @if($certificate->year)<span>{{ \App\Support\Jalali::digits($certificate->year) }}</span>@endif
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-5">
                <h2 class="text-h3 font-extrabold">ضوابط و استانداردهای مرجع</h2>
                <div class="mt-6 space-y-3">
                    @foreach($standards as $standard)
                        <x-document-row :document="$standard" />
                    @endforeach
                </div>

                <div class="mt-6 rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-6">
                    <h3 class="font-bold">گزارش آزمون اختصاصی می‌خواهید؟</h3>
                    <p class="mt-2 leading-relaxed text-ink-500">
                        برای پروژه‌های بزرگ، گزارش آزمون بچ تولیدی همان پروژه را از آزمایشگاه داخلی یا آزمایشگاه معتمد ارائه می‌کنیم.
                    </p>
                    <x-cta :href="route('contact', ['type' => 'technical'])" variant="ghost" size="sm" class="mt-4">درخواست گزارش آزمون</x-cta>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
