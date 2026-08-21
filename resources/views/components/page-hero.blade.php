@props([
    'eyebrow' => null,
    'title',
    'lead' => null,
    'variant' => 'light',   // light | dark
    'compact' => false,
])

@php $dark = $variant === 'dark'; @endphp

<section {{ $attributes->merge(['class' => 'relative overflow-hidden '.($dark ? 'bg-ink-950 text-sand-50' : 'bg-sand-50')]) }}>
    @if($dark)
        <div class="pointer-events-none absolute -left-32 -top-32 h-[34rem] w-[34rem] rounded-full opacity-60 blur-[110px]"
             style="background: radial-gradient(circle, rgba(180,85,45,.4), transparent 65%)" aria-hidden="true"></div>
    @else
        <div class="pointer-events-none absolute inset-0 opacity-[0.55]" aria-hidden="true"
             style="background: radial-gradient(70% 60% at 88% 0%, rgba(180,85,45,.12), transparent 60%)"></div>
    @endif

    <div class="container-page relative {{ $compact ? 'py-12 lg:py-16' : 'py-16 lg:py-24' }}">
        <x-breadcrumbs class="{{ $dark ? '[&_*]:text-sand-200/60' : '' }}" />

        <div class="mt-6 max-w-4xl">
            @if($eyebrow)
                <p class="eyebrow {{ $dark ? 'text-clay-400' : 'text-clay-600' }}"><bdi dir="ltr">{{ $eyebrow }}</bdi></p>
            @endif

            <h1 class="mt-3 text-h1 font-extrabold text-balance">{{ $title }}</h1>

            @if($lead)
                <p class="mt-5 max-w-3xl text-lead {{ $dark ? 'text-sand-200/70' : 'text-ink-500' }}">{{ $lead }}</p>
            @endif
        </div>

        {{ $slot }}
    </div>
</section>
