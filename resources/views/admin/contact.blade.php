@php
    $input = 'w-full rounded-xl border border-sand-300 bg-white px-3.5 py-2.5 text-field transition focus:border-clay-400 focus:outline-none';
    $v = fn (string $key) => old($key, $values[$key] ?? '');

    /*
    | میدان‌ها: [کلید، برچسب، راهنما، پهنا، نوع، جهت]
    | جهتِ ltr برای شماره و ایمیل و نشانیِ وب: در جعبه‌ی راست‌به‌چپ،
    | «۰۴۵-۳۱۸۲» به «۳۱۸۲-۰۴۵» تبدیل می‌شود و مدیر گمان می‌کند غلط ذخیره شده.
    */
    $sections = [
        'تلفن' => [
            ['phone', 'تلفن مرکزی', 'در هدر، فوتر، نوار موبایل و صفحه‌ی تماس.', 'half', 'tel', 'ltr'],
            ['sales_phone', 'خط مستقیم', 'خالی بماند یعنی خط مستقیم نشان داده نمی‌شود.', 'third', 'tel', 'ltr'],
            ['sales_extension', 'داخلی', 'فقط رقم. گوشی پس از وصل‌شدن خودش می‌گیردش.', 'third', 'text', 'ltr'],
        ],
        'ایمیل' => [
            ['email', 'ایمیل فروش', null, 'half', 'email', 'ltr'],
            ['technical_email', 'ایمیل واحد فنی', null, 'half', 'email', 'ltr'],
        ],
        'نشانی و ساعت کاری' => [
            ['address', 'نشانی کامل', 'در فوتر، صفحه‌ی تماس و اطلاعات کسب‌وکار برای گوگل.', 'full', 'textarea', 'rtl'],
            ['address_region', 'استان', null, 'third', 'text', 'rtl'],
            ['address_locality', 'شهر', null, 'third', 'text', 'rtl'],
            ['postal_code', 'کد پستی', null, 'third', 'text', 'ltr'],
            ['working_hours', 'ساعت کاری', 'در نوار بالای سایت.', 'full', 'text', 'rtl'],
            ['lat', 'عرض جغرافیایی', 'مثل 38.2498 — از گوگل‌مپ، کلیک راست روی محل.', 'half', 'text', 'ltr'],
            ['lng', 'طول جغرافیایی', 'مثل 48.2933', 'half', 'text', 'ltr'],
        ],
        'شبکه‌های اجتماعی' => [
            ['instagram', 'اینستاگرام', 'نشانی کامل صفحه. خالی یا بی نام کاربری یعنی آیکون نشان داده نمی‌شود.', 'half', 'url', 'ltr'],
            ['telegram', 'تلگرام', null, 'half', 'url', 'ltr'],
            ['linkedin', 'لینکدین', null, 'half', 'url', 'ltr'],
            ['aparat', 'آپارات', null, 'half', 'url', 'ltr'],
        ],
    ];
@endphp

<x-layouts.admin title="اطلاعات تماس" subtitle="تلفن، نشانی، ایمیل و شبکه‌های اجتماعی — همان چیزی که روی سایت دیده می‌شود">

    <x-slot:actions>
        <a href="{{ route('contact') }}" target="_blank" rel="noopener"
           class="tap gap-2 rounded-xl border border-sand-300 px-4 text-meta font-semibold text-ink-600 transition hover:bg-sand-200">
            <x-icon name="external" size="15" />
            <span class="hidden sm:inline">صفحه‌ی تماس در سایت</span>
        </a>
    </x-slot:actions>

    <form method="POST" action="{{ route('admin.contact.update') }}" class="max-w-4xl">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            @foreach($sections as $heading => $fields)
                <section class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-5 lg:p-7">
                    <h2 class="mb-5 border-b border-sand-200 pb-3 text-[0.9375rem] font-extrabold text-ink-800">{{ $heading }}</h2>

                    <div class="grid grid-cols-1 gap-x-5 gap-y-5 sm:grid-cols-6">
                        @foreach($fields as [$key, $label, $hint, $width, $type, $dir])
                            <label @class([
                                'block',
                                'sm:col-span-6' => $width === 'full',
                                'sm:col-span-3' => $width === 'half',
                                'sm:col-span-2' => $width === 'third',
                            ])>
                                <span class="mb-1.5 block text-meta font-semibold text-ink-700">
                                    {{ $label }}
                                    @if(in_array($key, ['phone', 'address'], true))<span class="text-clay-500">*</span>@endif
                                </span>

                                @if($type === 'textarea')
                                    <textarea name="{{ $key }}" rows="2" dir="{{ $dir }}"
                                              @class([$input, 'border-red-400' => $errors->has($key)])>{{ $v($key) }}</textarea>
                                @else
                                    <input type="{{ $type === 'tel' ? 'tel' : ($type === 'email' ? 'email' : ($type === 'url' ? 'url' : 'text')) }}"
                                           name="{{ $key }}" value="{{ $v($key) }}" dir="{{ $dir }}"
                                           @if($type === 'tel') inputmode="tel" @endif
                                           @class([$input, 'text-left' => $dir === 'ltr', 'border-red-400' => $errors->has($key)])>
                                @endif

                                @error($key)
                                    <span class="mt-1 block text-meta text-red-600">{{ $message }}</span>
                                @else
                                    @if($hint)
                                        <span class="mt-1 block text-meta text-ink-400">{{ $hint }}</span>
                                    @endif
                                @enderror
                            </label>
                        @endforeach
                    </div>
                </section>
            @endforeach

            {{--
                نشانی و ساعت کاری به زبان‌اند. خالی ماندنِ جعبه یعنی همان
                ترجمه‌ای که در پرونده‌ی زبان هست — نه نشانیِ فارسی روی صفحه‌ی
                انگلیسی.
            --}}
            @foreach($locales as $code => $meta)
                <section class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-5 lg:p-7">
                    <h2 class="mb-1 flex items-center gap-2 text-[0.9375rem] font-extrabold text-ink-800">
                        <x-icon name="globe" size="16" class="text-clay-500" />
                        ترجمه — {{ $meta['name'] }}
                    </h2>
                    <p class="mb-5 border-b border-sand-200 pb-3 text-meta text-ink-400">
                        خالی بماند، همان ترجمه‌ی پیش‌فرضِ سایت نشان داده می‌شود.
                    </p>

                    <div class="grid grid-cols-1 gap-5">
                        @foreach(['address' => 'نشانی کامل', 'working_hours' => 'ساعت کاری'] as $field => $label)
                            @php
                                $key = "{$field}_{$code}";
                                $fallback = trans("site.contact.{$field}", [], $code);
                            @endphp
                            <label class="block">
                                <span class="mb-1.5 block text-meta font-semibold text-ink-700">{{ $label }}</span>
                                <input type="text" name="{{ $key }}" value="{{ $v($key) }}"
                                       dir="{{ $meta['dir'] }}" lang="{{ $meta['html'] }}"
                                       placeholder="{{ $fallback !== "site.contact.{$field}" ? $fallback : '' }}"
                                       @class([$input, 'border-red-400' => $errors->has($key)])>
                            </label>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        <div class="sticky bottom-0 z-10 -mx-4 mt-6 border-t border-sand-300 bg-sand-100/95 px-4 py-3 backdrop-blur lg:mx-0 lg:rounded-xl lg:border">
            <button type="submit" class="tap gap-2 rounded-xl bg-ink-900 px-6 font-semibold text-sand-50 transition hover:bg-clay-600">
                <x-icon name="check" size="16" />
                ذخیره
            </button>
        </div>
    </form>
</x-layouts.admin>
