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
