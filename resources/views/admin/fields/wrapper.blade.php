@php
    /** @var \App\Support\Admin\Field $field */
    $id = 'f_'.$field->key;
    $value = old($field->key, $record->{$field->key} ?? $field->default);
    $invalid = $errors->has($field->key);
    $base = 'w-full rounded-xl border bg-sand-100 px-4 py-3 outline-none transition focus:bg-sand-50 '
        .($invalid ? 'border-red-400 focus:border-red-500' : 'border-sand-300 focus:border-clay-400');
@endphp

@if($field->type === 'boolean')
    {{-- چک‌باکس غایب یعنی «خاموش»؛ فیلد پنهان تضمین می‌کند مقدار همیشه ارسال شود --}}
    <label for="{{ $id }}" class="flex cursor-pointer items-start gap-3 rounded-xl border border-sand-300 bg-sand-100 p-4 transition has-[:checked]:border-clay-400 has-[:checked]:bg-clay-50">
        <input type="hidden" name="{{ $field->key }}" value="0">
        <input id="{{ $id }}" type="checkbox" name="{{ $field->key }}" value="1"
               @checked((bool) $value)
               class="mt-0.5 h-5 w-5 shrink-0 accent-[var(--color-clay-500)]">
        <span>
            <span class="block font-semibold">{{ $field->label }}</span>
            @if($field->hint)<span class="block text-meta text-ink-400">{{ $field->hint }}</span>@endif
        </span>
    </label>
@else
    <label for="{{ $id }}" class="mb-2 block text-meta font-semibold text-ink-600">
        {{ $field->label }}
        @if(in_array('required', $field->rules, true))
            <span class="text-clay-600" aria-hidden="true">*</span>
        @endif
        @if($field->suffix)
            <span class="tech mr-1 font-normal text-ink-300">({{ $field->suffix }})</span>
        @endif
    </label>

    @include('admin.fields.control', compact('field', 'record', 'id', 'value', 'base', 'invalid'))

    @error($field->key)
        <p class="mt-1.5 text-meta text-red-600">{{ $message }}</p>
    @enderror

    @if($field->hint && ! $errors->has($field->key))
        <p class="mt-1.5 text-micro leading-relaxed text-ink-400">{{ $field->hint }}</p>
    @endif
@endif
