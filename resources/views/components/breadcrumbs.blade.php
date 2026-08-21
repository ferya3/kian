@props(['items' => null])

@php $items = $items ?? $seo->breadcrumbs; @endphp

@if(count($items) > 1)
    <nav aria-label="مسیر صفحه" {{ $attributes->merge(['class' => 'text-meta']) }}>
        <ol class="flex flex-wrap items-center gap-x-1.5 gap-y-1 text-ink-400">
            @foreach($items as $item)
                <li class="flex items-center gap-1.5">
                    @if(!$loop->first)
                        <x-icon name="chevron-left" size="13" class="text-ink-300" />
                    @endif

                    @if($item['url'] && !$loop->last)
                        <a href="{{ $item['url'] }}" class="tap px-1 transition hover:text-clay-600">{{ $item['label'] }}</a>
                    @else
                        <span class="font-semibold text-ink-700" aria-current="page">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
