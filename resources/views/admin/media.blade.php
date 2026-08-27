<x-layouts.admin title="کتابخانه‌ی تصاویر"
                 subtitle="{{ \App\Support\Jalali::digits($totalImages) }} تصویر">

    {{--
        این صفحه جای آپلود نیست. هر تصویر مالک دارد و در فرم همان رکورد
        آپلود و حذف می‌شود؛ اینجا فقط می‌بینید چه چیزی کجا نشسته.
    --}}
    <p class="mb-6 flex items-start gap-2.5 rounded-xl border border-sand-300 bg-sand-50 px-4 py-3 text-[0.9375rem] text-ink-600">
        <x-icon name="sparkle" size="17" class="mt-0.5 shrink-0 text-clay-500" />
        هر تصویر در فرم همان بخش آپلود می‌شود. برای تغییر، روی نام رکورد بزنید.
    </p>

    @forelse($groups as $group)
        <section class="mb-8">
            <div class="flex flex-wrap items-baseline justify-between gap-3 pb-3">
                <h2 class="text-lg font-extrabold">
                    {{ $group['label'] }}
                    <span class="tech mr-2 text-meta font-semibold text-ink-400">
                        {{ \App\Support\Jalali::digits(count($group['items'])) }}
                    </span>
                </h2>

                @if($group['missing'] > 0)
                    <a href="{{ route('admin.resource.index', $group['slug']) }}"
                       class="tap -mx-2 px-2 text-meta font-semibold text-clay-600">
                        {{ \App\Support\Jalali::digits($group['missing']) }} مورد بدون تصویر
                    </a>
                @endif
            </div>

            @if($group['items'])
                <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach($group['items'] as $item)
                        <li class="overflow-hidden rounded-xl border border-sand-300 bg-sand-50">
                            <a href="{{ $item['url'] }}" target="_blank" rel="noopener"
                               class="block aspect-[4/3] bg-sand-200">
                                <img src="{{ $item['url'] }}" alt="" loading="lazy" decoding="async"
                                     class="h-full w-full object-cover">
                            </a>
                            <div class="border-t border-sand-200 p-3">
                                <a href="{{ $item['edit'] }}"
                                   class="tap -mx-2 block truncate px-2 text-meta font-semibold hover:text-clay-600">
                                    {{ $item['owner'] }}
                                </a>
                                <p class="mt-0.5 truncate text-micro text-ink-400">{{ $item['field'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="rounded-xl border border-dashed border-sand-300 px-4 py-8 text-center text-ink-400">
                    هنوز تصویری برای این بخش آپلود نشده است.
                </p>
            @endif
        </section>
    @empty
        <p class="rounded-[var(--radius-panel)] border border-dashed border-sand-300 px-4 py-16 text-center text-ink-400">
            هیچ بخشی فیلد تصویر ندارد.
        </p>
    @endforelse

    @if($orphans->isNotEmpty())
        <section class="mt-10 rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-5">
            <h2 class="text-[0.9375rem] font-extrabold">فایل‌های بی‌مالک</h2>
            <p class="mt-1 text-meta text-ink-500">
                روی دیسک هستند ولی هیچ رکوردی به آن‌ها اشاره نمی‌کند — احتمالاً از
                حذف یا جایگزینی جا مانده‌اند. عمداً حذف خودکار نمی‌شوند.
            </p>
            <ul class="mt-4 space-y-1.5">
                @foreach($orphans as $orphan)
                    <li class="flex items-center gap-3 text-meta">
                        <a href="{{ $orphan['url'] }}" target="_blank" rel="noopener"
                           class="tap -mx-2 min-w-0 flex-1 truncate px-2 text-ink-600 hover:text-clay-600" dir="ltr">
                            {{ $orphan['path'] }}
                        </a>
                        <span class="tech shrink-0 text-micro text-ink-300">
                            {{ \App\Support\Jalali::digits($orphan['size_kb']) }} KB
                        </span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</x-layouts.admin>
