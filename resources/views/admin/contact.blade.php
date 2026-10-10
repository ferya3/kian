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
        'شبکه‌های اجتماعی' => [
            ['instagram', 'اینستاگرام', 'نشانی کامل صفحه. خالی یا بی نام کاربری یعنی آیکون نشان داده نمی‌شود.', 'half', 'url', 'ltr'],
            ['telegram', 'تلگرام', null, 'half', 'url', 'ltr'],
            ['linkedin', 'لینکدین', null, 'half', 'url', 'ltr'],
            ['aparat', 'آپارات', null, 'half', 'url', 'ltr'],
        ],
    ];
@endphp

<x-layouts.admin title="اطلاعات تماس" subtitle="تلفن، ایمیل، نشانی‌ها و شبکه‌های اجتماعی — همان چیزی که روی سایت دیده می‌شود">

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
            {{--
                نشانی‌ها جای خودشان را دارند — هر کدام با ساعت کاری و تلفن
                و نقشه‌ی خودش. اینجا فقط فهرستشان، تا مدیر بداند کجا پیدایشان کند.
            --}}
            <section class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-5 lg:p-7">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3 border-b border-sand-200 pb-3">
                    <h2 class="text-[0.9375rem] font-extrabold text-ink-800">نشانی‌ها و ساعت کاری</h2>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.resource.create', 'locations') }}"
                           class="tap gap-1.5 rounded-xl bg-ink-900 px-4 text-meta font-semibold text-sand-50 transition hover:bg-clay-600">
                            <x-icon name="plus" size="15" />
                            نشانی تازه
                        </a>
                        <a href="{{ route('admin.resource.index', 'locations') }}"
                           class="tap rounded-xl border border-sand-300 px-4 text-meta font-semibold text-ink-600 transition hover:bg-sand-200">
                            مدیریت همه
                        </a>
                    </div>
                </div>

                @forelse($locations as $location)
                    <a href="{{ route('admin.resource.edit', ['locations', $location->id]) }}"
                       class="-mx-2 flex items-start gap-3 rounded-xl px-2 py-3 transition hover:bg-sand-100">
                        <x-icon name="pin" size="18" class="mt-0.5 shrink-0 {{ $location->is_active ? 'text-clay-500' : 'text-ink-300' }}" />
                        <span class="min-w-0 flex-1">
                            <span class="flex flex-wrap items-center gap-2 font-bold text-ink-800">
                                {{ $location->title }}
                                @if($loop->first && $location->is_active)
                                    <span class="rounded-full bg-clay-50 px-2 py-0.5 text-micro font-bold text-clay-700">اصلی</span>
                                @endif
                                @unless($location->is_active)
                                    <span class="rounded-full bg-sand-200 px-2 py-0.5 text-micro font-bold text-ink-500">پنهان</span>
                                @endunless
                            </span>
                            <span class="block text-meta text-ink-500">{{ $location->address }}</span>
                            @if($location->working_hours)
                                <span class="block text-meta text-ink-400">{{ $location->working_hours }}</span>
                            @endif
                        </span>
                        <span class="shrink-0 text-meta font-semibold text-clay-600">ویرایش</span>
                    </a>
                @empty
                    <p class="text-meta text-ink-500">
                        هیچ نشانی‌ای ثبت نشده — فوتر و صفحه‌ی تماس بی نشانی‌اند.
                    </p>
                @endforelse

                <p class="mt-3 text-meta text-ink-400">
                    نخستین نشانیِ فعال (کوچک‌ترین «ترتیب») اصلی است: ساعت کاری‌اش در نوار بالای سایت می‌آید و گوگل همان را نشانیِ شرکت می‌شناسد.
                </p>
            </section>

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
                                    @if($key === 'phone')<span class="text-clay-500">*</span>@endif
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

        </div>

        <div class="sticky bottom-0 z-10 -mx-4 mt-6 border-t border-sand-300 bg-sand-100/95 px-4 py-3 backdrop-blur lg:mx-0 lg:rounded-xl lg:border">
            <button type="submit" class="tap gap-2 rounded-xl bg-ink-900 px-6 font-semibold text-sand-50 transition hover:bg-clay-600">
                <x-icon name="check" size="16" />
                ذخیره
            </button>
        </div>
    </form>
</x-layouts.admin>
