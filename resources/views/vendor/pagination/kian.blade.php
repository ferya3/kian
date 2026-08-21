@if ($paginator->hasPages())
    <nav role="navigation" aria-label="صفحه‌بندی" class="flex items-center justify-center gap-1.5">
        @if ($paginator->onFirstPage())
            <span class="tap-icon rounded-full text-ink-300" aria-disabled="true">
                <x-icon name="chevron-left" size="18" />
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="صفحه قبل"
               class="tap-icon rounded-full text-ink-600 transition hover:bg-sand-200">
                <x-icon name="chevron-left" size="18" />
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 text-ink-300">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="tech tap-icon rounded-full bg-ink-900 px-3 font-bold text-sand-50">
                            {{ \App\Support\Jalali::digits($page) }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="tech tap-icon rounded-full px-3 font-semibold text-ink-600 transition hover:bg-sand-200">
                            {{ \App\Support\Jalali::digits($page) }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="صفحه بعد"
               class="tap-icon rounded-full text-ink-600 transition hover:bg-sand-200">
                <x-icon name="chevron-left" size="18" class="rotate-180" />
            </a>
        @else
            <span class="tap-icon rounded-full text-ink-300" aria-disabled="true">
                <x-icon name="chevron-left" size="18" class="rotate-180" />
            </span>
        @endif
    </nav>
@endif
