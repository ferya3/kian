<x-layouts.app>
    <x-page-hero
        eyebrow="Downloads"
        title="مرکز دانلود"
        lead="همه‌ی فایل‌های فنی در یک صفحه، با فیلتر بر اساس نوع فایل، مخاطب و فرمت."
        compact />

    <section class="bg-sand-100 pb-20">
        <div class="container-page">
            <form method="GET" action="{{ route('technical.downloads') }}"
                  class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-5 lg:p-6">
                <div class="grid gap-4 lg:grid-cols-4">
                    <div>
                        <label for="q" class="mb-2 block text-[0.8125rem] font-semibold text-ink-600">جستجو</label>
                        <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="نام فایل…"
                               class="w-full rounded-xl border border-sand-300 bg-sand-100 px-4 py-3 text-[0.9375rem] outline-none focus:border-clay-400 focus:bg-sand-50">
                    </div>

                    @foreach([
                        ['name' => 'category', 'label' => 'نوع فایل', 'options' => $labels],
                        ['name' => 'audience', 'label' => 'مخاطب', 'options' => $audiences],
                    ] as $field)
                        <div>
                            <label for="{{ $field['name'] }}" class="mb-2 block text-[0.8125rem] font-semibold text-ink-600">{{ $field['label'] }}</label>
                            <div class="relative">
                                <select id="{{ $field['name'] }}" name="{{ $field['name'] }}"
                                        class="w-full appearance-none rounded-xl border border-sand-300 bg-sand-100 py-3 pr-4 pl-10 text-[0.9375rem] outline-none focus:border-clay-400 focus:bg-sand-50">
                                    <option value="">همه</option>
                                    @foreach($field['options'] as $value => $label)
                                        <option value="{{ $value }}" @selected(request($field['name']) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-icon name="chevron-down" size="16" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-400" />
                            </div>
                        </div>
                    @endforeach

                    <div>
                        <label for="format" class="mb-2 block text-[0.8125rem] font-semibold text-ink-600">فرمت</label>
                        <div class="relative">
                            <select id="format" name="format"
                                    class="w-full appearance-none rounded-xl border border-sand-300 bg-sand-100 py-3 pr-4 pl-10 text-[0.9375rem] uppercase outline-none focus:border-clay-400 focus:bg-sand-50">
                                <option value="">همه</option>
                                @foreach($formats as $format)
                                    <option value="{{ $format }}" @selected(request('format') === $format)>{{ strtoupper($format) }}</option>
                                @endforeach
                            </select>
                            <x-icon name="chevron-down" size="16" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-400" />
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-sand-200 pt-5">
                    <p class="tech text-[0.8125rem] text-ink-400">
                        {{ \App\Support\Jalali::digits($documents->count()) }} فایل
                    </p>
                    <div class="flex gap-2">
                        @if(request()->hasAny(['q', 'category', 'audience', 'format']))
                            <a href="{{ route('technical.downloads') }}"
                               class="rounded-full px-4 py-2.5 text-[0.875rem] text-ink-400 transition hover:text-ink-900">حذف فیلترها</a>
                        @endif
                        <button type="submit" class="rounded-full bg-ink-900 px-6 py-2.5 text-[0.875rem] font-semibold text-sand-50 transition hover:bg-clay-600">
                            اعمال فیلتر
                        </button>
                    </div>
                </div>
            </form>

            @if($documents->isEmpty())
                <div class="mt-10 rounded-[var(--radius-panel)] border border-dashed border-sand-300 py-16 text-center">
                    <p class="text-lg font-bold">فایلی با این فیلترها پیدا نشد</p>
                    <p class="mt-2 text-ink-400">فیلترها را بردارید یا از واحد فنی درخواست کنید.</p>
                    <x-cta :href="route('contact', ['type' => 'technical'])" variant="ghost" class="mt-6">درخواست فایل</x-cta>
                </div>
            @else
                <div class="mt-8 space-y-10">
                    @foreach($documents->groupBy('category') as $category => $group)
                        <div>
                            <h2 class="border-b border-sand-300 pb-3 text-lg font-extrabold">{{ $labels[$category] ?? $category }}</h2>
                            <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3" data-reveal-stagger="50">
                                @foreach($group as $document)
                                    <x-document-row :document="$document" data-reveal />
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-layouts.app>
