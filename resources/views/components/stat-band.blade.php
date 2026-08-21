@props(['stats', 'light' => false])

<dl {{ $attributes->merge(['class' => 'grid grid-cols-2 gap-px overflow-hidden rounded-[var(--radius-panel)] lg:grid-cols-4 '.($light ? 'border border-white/10 bg-white/10' : 'border border-sand-300 bg-sand-300')]) }}>
    @foreach($stats as $stat)
        <div class="{{ $light ? 'bg-ink-950/60' : 'bg-sand-50' }} px-5 py-6 lg:px-7 lg:py-8">
            <dd class="text-3xl font-extrabold lg:text-4xl {{ $light ? 'text-sand-50' : 'text-ink-900' }}">
                <bdi dir="ltr" class="tech inline-block whitespace-nowrap">
                    <span data-countup="{{ $stat->value }}" data-decimals="{{ $stat->decimals }}"
                          @if($stat->value >= 1000) data-separated @endif>۰</span><span class="text-clay-500">{{ $stat->suffix }}</span>
                </bdi>
            </dd>
            <dt class="mt-2 font-semibold {{ $light ? 'text-sand-100' : 'text-ink-800' }}">{{ $stat->label }}</dt>
            @if($stat->description)
                <p class="mt-1.5 text-meta leading-relaxed {{ $light ? 'text-sand-200/50' : 'text-ink-400' }}">{{ $stat->description }}</p>
            @endif
        </div>
    @endforeach
</dl>
