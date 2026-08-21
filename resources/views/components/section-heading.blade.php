@props([
    'eyebrow' => null,
    'title',
    'lead' => null,
    'align' => 'start',
    'light' => false,
    'as' => 'h2',
])

<div {{ $attributes->merge(['class' => 'max-w-3xl '.($align === 'center' ? 'mx-auto text-center' : '')]) }}>
    @if($eyebrow)
        <p data-reveal class="eyebrow {{ $light ? 'text-clay-400' : 'text-clay-600' }}"><bdi dir="ltr">{{ $eyebrow }}</bdi></p>
    @endif

    <{{ $as }} data-reveal class="mt-3 text-h2 font-extrabold text-balance {{ $light ? 'text-sand-50' : 'text-ink-900' }}">
        {{ $title }}
    </{{ $as }}>

    @if($lead)
        <p data-reveal class="mt-5 text-lead {{ $light ? 'text-sand-200/70' : 'text-ink-500' }}">
            {{ $lead }}
        </p>
    @endif

    {{ $slot }}
</div>
