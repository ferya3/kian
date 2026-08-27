@php /** @var \App\Support\Admin\Field $field */ @endphp

@switch($field->type)

    @case('textarea')
        <textarea id="{{ $id }}" name="{{ $field->key }}" rows="3"
                  placeholder="{{ $field->placeholder }}"
                  class="{{ $base }} leading-relaxed">{{ $value }}</textarea>
        @break

    @case('longtext')
        <textarea id="{{ $id }}" name="{{ $field->key }}" rows="10"
                  class="{{ $base }} leading-loose">{{ $value }}</textarea>
        @break

    @case('lines')
        {{-- آرایه‌ی متنی: هر خط یک آیتم. ساده‌ترین ویرایشگری که خطا نمی‌سازد. --}}
        <textarea id="{{ $id }}" name="{{ $field->key }}" rows="4"
                  class="{{ $base }} leading-loose">{{ is_array($value) ? implode("\n", $value) : $value }}</textarea>
        @break

    @case('select')
        <div class="relative">
            <select id="{{ $id }}" name="{{ $field->key }}" class="{{ $base }} appearance-none pl-10">
                @unless(in_array('required', $field->rules, true))
                    <option value="">انتخاب نشده</option>
                @endunless
                @foreach($field->options as $key => $label)
                    <option value="{{ $key }}" @selected((string) $value === (string) $key)>{{ $label }}</option>
                @endforeach
            </select>
            <x-icon name="chevron-down" size="16" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-400" />
        </div>
        @break

    @case('relation')
        @php
            $options = $field->relatedModel::query()
                ->orderBy($field->relatedLabel)
                ->pluck($field->relatedLabel, 'id');
        @endphp
        <div class="relative">
            <select id="{{ $id }}" name="{{ $field->key }}" class="{{ $base }} appearance-none pl-10">
                <option value="">انتخاب نشده</option>
                @foreach($options as $key => $label)
                    <option value="{{ $key }}" @selected((string) $value === (string) $key)>{{ $label }}</option>
                @endforeach
            </select>
            <x-icon name="chevron-down" size="16" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-400" />
        </div>
        @break

    @case('checkboxes')
        @php $selected = is_array($value) ? $value : []; @endphp
        <div class="grid gap-2 rounded-xl border border-sand-300 bg-sand-100 p-3 sm:grid-cols-2">
            @foreach($field->options as $key => $label)
                <label class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2 py-2 text-[0.9375rem] transition hover:bg-sand-200">
                    <input type="checkbox" name="{{ $field->key }}[]" value="{{ $key }}"
                           @checked(in_array($key, $selected, true))
                           class="h-5 w-5 shrink-0 accent-[var(--color-clay-500)]">
                    {{ $label }}
                </label>
            @endforeach
        </div>
        @break

    @case('date')
        <input id="{{ $id }}" name="{{ $field->key }}" type="date" dir="ltr"
               value="{{ $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : $value }}"
               class="{{ $base }}">
        @break

    @case('file')
    @case('image')
        @php $existing = $record->{$field->key} ?? null; @endphp
        @if($existing)
            <div class="mb-2 flex flex-wrap items-center gap-3 rounded-xl border border-sand-300 bg-sand-100 p-3">
                @if($field->type === 'image')
                    <img src="{{ \App\Support\Media::url($existing) }}"
                         alt="" class="h-14 w-14 rounded-lg object-cover">
                @else
                    <x-icon name="file" size="22" class="text-clay-500" />
                @endif
                <a href="{{ \App\Support\Media::url($existing) }}"
                   target="_blank" rel="noopener"
                   class="tap min-w-0 flex-1 truncate text-meta text-ink-600 hover:text-clay-600" dir="ltr">
                    {{ basename($existing) }}
                </a>
                <label class="tap gap-2 rounded-lg px-3 text-meta text-red-600">
                    <input type="checkbox" name="_remove_{{ $field->key }}" value="1"
                           class="h-4 w-4 accent-[var(--color-clay-500)]">
                    حذف فایل
                </label>
            </div>
        @endif
        {{--
            دکمه‌ی بومی input[type=file] متن انگلیسی مرورگر را نشان می‌دهد
            («Choose File») و ترجمه‌پذیر نیست؛ پس پنهانش می‌کنیم و label
            جای آن می‌نشیند. label برای input فایل، خودش کلیک را منتقل می‌کند،
            پس بدون جاوااسکریپت هم کار می‌کند.
        --}}
        <div x-data="{ picked: '' }" class="flex flex-wrap items-center gap-3">
            <input id="{{ $id }}" name="{{ $field->key }}" type="file" class="sr-only"
                   @if($field->type === 'image') accept="image/*" @endif
                   x-on:change="picked = $el.files.length ? $el.files[0].name : ''">
            <label for="{{ $id }}"
                   class="tap cursor-pointer gap-2 rounded-xl bg-ink-900 px-4 text-meta font-semibold text-sand-50 transition hover:bg-ink-800">
                <x-icon name="plus" size="15" />
                انتخاب فایل
            </label>
            <span class="min-w-0 flex-1 truncate text-meta text-ink-400"
                  x-text="picked || 'فایلی انتخاب نشده'" dir="auto">فایلی انتخاب نشده</span>
        </div>
        @break

    @case('gallery')
        @php $images = collect((array) ($record->{$field->key} ?? []))->filter()->values(); @endphp

        {{--
            تصویرهای موجود به‌صورت مسیر در فرم می‌مانند تا ذخیره بدون انتخاب
            فایل جدید، گالری را خالی نکند. حذف با چک‌باکس روی همان کارت است.
        --}}
        @if($images->isNotEmpty())
            <ul class="mb-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach($images as $path)
                    <li class="relative overflow-hidden rounded-xl border border-sand-300 bg-sand-100">
                        <input type="hidden" name="_order_{{ $field->key }}[]" value="{{ $path }}">
                        <img src="{{ \App\Support\Media::url($path) }}"
                             alt="" loading="lazy" class="aspect-[4/3] w-full object-cover">
                        <label class="tap w-full justify-center gap-2 border-t border-sand-300 text-meta text-red-600">
                            <input type="checkbox" name="_remove_{{ $field->key }}[]" value="{{ $path }}"
                                   class="h-4 w-4 accent-[var(--color-clay-500)]">
                            حذف
                        </label>
                    </li>
                @endforeach
            </ul>
        @endif

        <div x-data="{ count: 0 }" class="flex flex-wrap items-center gap-3">
            <input id="{{ $id }}" name="{{ $field->key }}[]" type="file" multiple accept="image/*" class="sr-only"
                   x-on:change="count = $el.files.length">
            <label for="{{ $id }}"
                   class="tap cursor-pointer gap-2 rounded-xl bg-ink-900 px-4 text-meta font-semibold text-sand-50 transition hover:bg-ink-800">
                <x-icon name="plus" size="15" />
                افزودن تصویر
            </label>
            <span class="min-w-0 flex-1 truncate text-meta text-ink-400"
                  x-text="count ? count + ' فایل انتخاب شد' : 'می‌توانید چند فایل را با هم انتخاب کنید'">می‌توانید چند فایل را با هم انتخاب کنید</span>
        </div>
        @break

    @case('readonly')
        <div class="rounded-xl border border-sand-200 bg-sand-200/50 px-4 py-3 leading-relaxed text-ink-600">
            @if($value instanceof \DateTimeInterface)
                <span class="tech">{{ \App\Support\Jalali::format($value, 'd MMMM y — HH:mm') }}</span>
            @else
                {{ filled($value) ? $value : '—' }}
            @endif
        </div>
        @break

    @case('password')
        <input id="{{ $id }}" name="{{ $field->key }}" type="password" autocomplete="new-password"
               class="{{ $base }}" dir="ltr">
        <input name="{{ $field->key }}_confirmation" type="password" autocomplete="new-password"
               placeholder="تکرار گذرواژه"
               class="{{ $base }} mt-2" dir="ltr" aria-label="تکرار گذرواژه">
        @break

    @case('number')
        <input id="{{ $id }}" name="{{ $field->key }}" type="number" step="1" inputmode="numeric" dir="ltr"
               value="{{ $value }}" class="{{ $base }} text-left">
        @break

    @case('decimal')
        <input id="{{ $id }}" name="{{ $field->key }}" type="number" step="0.001" inputmode="decimal" dir="ltr"
               value="{{ $value }}" class="{{ $base }} text-left">
        @break

    @case('slug')
    @case('email')
        <input id="{{ $id }}" name="{{ $field->key }}" type="{{ $field->type === 'email' ? 'email' : 'text' }}"
               value="{{ $value }}" dir="ltr" class="{{ $base }} text-left">
        @break

    @default
        <input id="{{ $id }}" name="{{ $field->key }}" type="text"
               value="{{ $value }}" placeholder="{{ $field->placeholder }}" class="{{ $base }}">
@endswitch
