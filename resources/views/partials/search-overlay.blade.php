{{-- جستجوی سراسری --}}
<div x-show="searchOpen"
     x-transition.opacity.duration.200ms
     @click.self="searchOpen = false"
     class="fixed inset-0 z-50 bg-ink-950/70 backdrop-blur-sm"
     style="display: none"
     role="dialog" aria-modal="true" aria-label="جستجو در سایت">
    <div class="container-page pt-28 lg:pt-36"
         x-transition:enter="transition ease-[var(--ease-out-expo)] duration-400"
         x-transition:enter-start="opacity-0 -translate-y-6"
         x-transition:enter-end="opacity-100 translate-y-0">
        <form action="{{ route('search') }}" method="GET"
              class="mx-auto max-w-3xl overflow-hidden rounded-[var(--radius-panel)] bg-sand-50 shadow-float">
            <div class="flex items-center gap-3 px-6">
                <x-icon name="search" size="22" class="text-ink-300" />
                <label for="site-search" class="sr-only">عبارت جستجو</label>
                <input id="site-search" name="q" type="search" x-ref="searchInput"
                       placeholder="نام محصول، ضخامت، پروژه یا فایل فنی…"
                       class="w-full bg-transparent py-6 text-lg outline-none placeholder:text-ink-300">
                <button type="button" @click="searchOpen = false"
                        class="grid h-9 w-9 shrink-0 place-items-center rounded-full text-ink-400 transition hover:bg-sand-200"
                        aria-label="بستن جستجو">
                    <x-icon name="close" size="18" />
                </button>
            </div>
            <div class="flex flex-wrap items-center gap-2 border-t border-sand-200 bg-sand-100 px-6 py-4 text-sm">
                <span class="text-ink-400">جستجوهای پرتکرار:</span>
                @foreach(['بلوک سفالی ۲۰', 'بلوک عایق', 'دیتاشیت', 'فایل BIM', 'راهنمای اجرا'] as $hint)
                    <a href="{{ route('search', ['q' => $hint]) }}"
                       class="rounded-full border border-sand-300 bg-sand-50 px-3 py-1 transition hover:border-clay-300 hover:text-clay-600">{{ $hint }}</a>
                @endforeach
            </div>
        </form>
    </div>
</div>
