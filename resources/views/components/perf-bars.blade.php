@props(['product', 'compact' => false, 'light' => false])

{{-- میله‌های عملکردی — همان چیزی که هنگام hover روی کارت محصول باز می‌شود --}}
<dl {{ $attributes->merge(['class' => 'space-y-2.5']) }}>
    @foreach($product->performanceBars() as $bar)
        <div class="flex items-center gap-3">
            <dt class="w-24 shrink-0 {{ $compact ? 'text-xs' : 'text-meta' }} {{ $light ? 'text-sand-200/70' : 'text-ink-400' }}">
                {{ $bar['label'] }}
            </dt>
            <dd class="flex flex-1 items-center gap-2">
                <div class="h-1.5 flex-1 overflow-hidden rounded-full {{ $light ? 'bg-white/12' : 'bg-sand-300' }}"
                     role="meter" aria-valuenow="{{ $bar['value'] }}" aria-valuemin="0" aria-valuemax="100"
                     aria-label="{{ $bar['label'] }} {{ $bar['value'] }} از ۱۰۰">
                    <div class="h-full rounded-full bg-gradient-to-l from-ember-500 to-clay-500 transition-[width] duration-700 ease-[var(--ease-out-expo)]"
                         style="width: {{ $bar['value'] }}%"></div>
                </div>
                <span class="tech w-8 shrink-0 text-left text-xs {{ $light ? 'text-sand-200/60' : 'text-ink-400' }}">
                    {{ \App\Support\Jalali::digits($bar['value']) }}
                </span>
            </dd>
        </div>
    @endforeach
</dl>
