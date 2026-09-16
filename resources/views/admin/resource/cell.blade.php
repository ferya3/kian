@php
    /** @var \App\Support\Admin\Field $field */
    $value = $record->{$field->key};
@endphp

@switch($field->type)
    @case('boolean')
        <span @class([
            'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-micro font-semibold',
            'bg-clay-100 text-clay-700' => (bool) $value,
            'bg-sand-200 text-ink-400' => ! $value,
        ])>
            {{ $value ? 'بله' : 'خیر' }}
        </span>
        @break

    @case('relation')
        @php
            // نام صریح، وگرنه نام مشتق‌شده از ستون، وگرنه جستجوی مستقیم روی
            // مدل مرتبط — تا هیچ ستون رابطه‌ای بی‌صدا «—» نشود.
            $relation = $field->relationName ?: \Illuminate\Support\Str::camel(str_replace('_id', '', $field->key));
            $related = $record->relationLoaded($relation) || method_exists($record, $relation)
                ? $record->{$relation}
                : ($value ? $field->relatedModel::find($value) : null);
        @endphp
        <span class="text-ink-600">{{ $related?->{$field->relatedLabel} ?? '—' }}</span>
        @break

    @case('select')
        <span class="text-ink-600">{{ $field->options[$value] ?? $value ?? '—' }}</span>
        @break

    @case('date')
        <span class="tech text-ink-600">{{ $value ? \App\Support\Jalali::format($value) : 'پیش‌نویس' }}</span>
        @break

    @case('number')
    @case('decimal')
        <span class="tech text-ink-700">
            {{ $value === null ? '—' : \App\Support\Jalali::digits(rtrim(rtrim(number_format((float) $value, 3), '0'), '.')) }}
            @if($field->suffix)<span class="text-micro text-ink-300">{{ $field->suffix }}</span>@endif
        </span>
        @break

    @case('image')
        @php $url = \App\Support\Media::url($value); @endphp
        @if($url)
            <img src="{{ $url }}" alt="" loading="lazy" decoding="async"
                 class="h-11 w-16 rounded-lg border border-sand-300 bg-sand-100 object-cover">
        @else
            {{-- «تصویری ندارد» باید در فهرست دیده شود، وگرنه جای خالی گم می‌شود --}}
            <span class="inline-flex h-11 w-16 items-center justify-center rounded-lg border border-dashed border-sand-300 text-ink-300">
                <x-icon name="image" size="16" />
            </span>
        @endif
        @break

    @case('gallery')
        @php $count = count(array_filter((array) $value)); @endphp
        <span class="tech text-ink-500">
            {{ $count ? \App\Support\Jalali::digits($count).' تصویر' : '—' }}
        </span>
        @break

    @case('readonly')
        <span class="text-ink-500">
            @if($value instanceof \DateTimeInterface)
                <span class="tech">{{ \App\Support\Jalali::format($value, 'd MMM y') }}</span>
            @else
                {{ \Illuminate\Support\Str::limit((string) $value, 60) ?: '—' }}
            @endif
        </span>
        @break

    @default
        {{ \Illuminate\Support\Str::limit((string) $value, 70) ?: '—' }}
@endswitch
