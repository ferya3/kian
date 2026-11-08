@props(['stats', 'light' => false])

<dl {{ $attributes->merge(['class' => 'grid grid-cols-2 gap-px overflow-hidden rounded-[var(--radius-panel)] lg:grid-cols-4 '.($light ? 'border border-white/10 bg-white/10' : 'border border-sand-300 bg-sand-300')]) }}>
    @foreach($stats as $stat)
        {{--
            dt پیش از dd و توضیح در dd ِ دوم — قاعده‌ی dl. ترتیبِ دیداری (عدد
            بالا، برچسب زیرش) با order حفظ می‌شود؛ صفحه‌خوان «برچسب: عدد» می‌خواند.
        --}}
        <div class="flex flex-col {{ $light ? 'bg-ink-950/60' : 'bg-sand-50' }} px-5 py-6 lg:px-7 lg:py-8">
            <dt class="order-2 mt-2 font-semibold {{ $light ? 'text-sand-100' : 'text-ink-800' }}">{{ $stat->label }}</dt>
            <dd class="order-1 text-3xl font-extrabold lg:text-4xl {{ $light ? 'text-sand-50' : 'text-ink-900' }}">
                <bdi dir="ltr" class="tech inline-block whitespace-nowrap">
                    <span data-countup="{{ $stat->value }}" data-decimals="{{ $stat->decimals }}"
                          @if($stat->value >= 1000) data-separated @endif>۰</span><span class="text-clay-500">{{ $stat->suffix }}</span>
                </bdi>
            </dd>
            @if($stat->description)
                <dd class="order-3 mt-1.5 text-meta leading-relaxed {{ $light ? 'text-sand-200/70' : 'text-ink-400' }}">{{ $stat->description }}</dd>
            @endif
        </div>
    @endforeach
</dl>
