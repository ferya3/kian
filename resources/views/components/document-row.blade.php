@props(['document'])

@php
    $formatColors = [
        'pdf'  => 'bg-clay-100 text-clay-700',
        'dwg'  => 'bg-ink-200/60 text-ink-700',
        'dxf'  => 'bg-ink-200/60 text-ink-700',
        'rvt'  => 'bg-ember-500/15 text-ember-600',
        'ifc'  => 'bg-ember-500/15 text-ember-600',
        'xlsx' => 'bg-sand-300 text-ink-600',
    ];
@endphp

<a href="{{ route('documents.download', $document) }}"
   {{ $attributes->merge(['class' => 'group flex items-center gap-4 rounded-xl border border-sand-300 bg-sand-50 p-4 transition-all duration-300 hover:border-clay-300 hover:bg-clay-50']) }}>

    <span class="tech grid h-11 w-11 shrink-0 place-items-center rounded-lg text-micro font-extrabold uppercase {{ $formatColors[$document->format] ?? 'bg-sand-200 text-ink-600' }}">
        {{ $document->format }}
    </span>

    <span class="min-w-0 flex-1">
        <span class="block truncate font-semibold text-ink-900 group-hover:text-clay-700">{{ $document->title }}</span>
        <span class="mt-0.5 flex flex-wrap items-center gap-x-2.5 gap-y-0.5 text-micro text-ink-400">
            <span>{{ $document->categoryLabel() }}</span>
            @if($document->version)
                <span class="h-1 w-1 rounded-full bg-ink-300"></span>
                <span class="tech">{{ $document->version }}</span>
            @endif
            @if($document->file_size_kb)
                <span class="h-1 w-1 rounded-full bg-ink-300"></span>
                <span class="tech">{{ \App\Support\Jalali::digits($document->sizeLabel()) }}</span>
            @endif
        </span>
    </span>

    <x-icon name="download" size="19" class="shrink-0 text-ink-300 transition group-hover:text-clay-500" />
</a>
