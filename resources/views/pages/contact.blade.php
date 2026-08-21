<x-layouts.app>
    @php
        $types = [
            'quote' => ['label' => 'درخواست قیمت', 'hint' => 'پیش‌فاکتور و شرایط تحویل'],
            'technical' => ['label' => 'مشاوره فنی', 'hint' => 'محاسبه، انتخاب محصول، فایل فنی'],
            'distributor' => ['label' => 'درخواست نمایندگی', 'hint' => 'همکاری در توزیع'],
            'general' => ['label' => 'سایر موضوعات', 'hint' => 'بازدید، همکاری، پرسش عمومی'],
        ];
    @endphp

    <x-page-hero
        eyebrow="Contact"
        title="تماس با ما"
        lead="فرم را پر کنید یا مستقیم زنگ بزنید. کارشناسان ما حداکثر تا یک روز کاری پاسخ می‌دهند."
        compact />

    <section class="bg-sand-100 pb-20">
        <div class="container-page grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-14">

            <div class="lg:col-span-7">
                @if(session('success'))
                    <div role="status"
                         class="mb-6 flex gap-3 rounded-[var(--radius-panel)] border border-clay-200 bg-clay-50 p-5">
                        <x-icon name="check" size="20" class="mt-0.5 shrink-0 text-clay-600" />
                        <p class="font-semibold text-clay-800">{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('notice'))
                    <div role="status"
                         class="mb-6 flex gap-3 rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-5">
                        <x-icon name="file" size="20" class="mt-0.5 shrink-0 text-ink-400" />
                        <p class="text-ink-600">{{ session('notice') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div role="alert" class="mb-6 rounded-[var(--radius-panel)] border border-red-300 bg-red-50 p-5">
                        <p class="font-bold text-red-800">لطفاً موارد زیر را اصلاح کنید:</p>
                        <ul class="mt-2 list-inside list-disc space-y-1 text-[0.9375rem] text-red-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}"
                      class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-6 lg:p-8">
                    @csrf

                    {{-- تله‌ی ربات — از دید کاربر و صفحه‌خوان پنهان --}}
                    <div class="hidden" aria-hidden="true">
                        <label for="website">وب‌سایت</label>
                        <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>

                    <fieldset>
                        <legend class="text-meta font-semibold text-ink-600">موضوع درخواست</legend>
                        <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                            @foreach($types as $value => $type)
                                <label class="group relative flex cursor-pointer items-start gap-3 rounded-xl border border-sand-300 bg-sand-100 p-4 transition has-[:checked]:border-clay-400 has-[:checked]:bg-clay-50">
                                    <input type="radio" name="type" value="{{ $value }}"
                                           @checked(old('type', $presetType) === $value)
                                           class="mt-0.5 h-6 w-6 shrink-0 accent-[var(--color-clay-500)]">
                                    <span>
                                        <span class="block text-[0.9375rem] font-semibold">{{ $type['label'] }}</span>
                                        <span class="block text-meta text-ink-400">{{ $type['hint'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach([
                            ['name', 'نام و نام خانوادگی', 'text', true, 'name'],
                            ['company', 'شرکت یا پروژه', 'text', false, 'organization'],
                            ['phone', 'شماره تماس', 'tel', true, 'tel'],
                            ['email', 'ایمیل', 'email', false, 'email'],
                            ['city', 'شهر', 'text', false, 'address-level2'],
                        ] as [$field, $label, $type, $required, $autocomplete])
                            <div @class(['sm:col-span-2' => $field === 'city'])>
                                <label for="{{ $field }}" class="mb-2 block text-meta font-semibold text-ink-600">
                                    {{ $label }} @if($required)<span class="text-clay-600" aria-hidden="true">*</span>@endif
                                </label>
                                <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}"
                                       value="{{ old($field) }}" autocomplete="{{ $autocomplete }}"
                                       @if($required) required aria-required="true" @endif
                                       @if($errors->has($field)) aria-invalid="true" aria-describedby="{{ $field }}-error" @endif
                                       @if($field === 'phone') inputmode="tel" dir="ltr" placeholder="۰۹۱۲۱۲۳۴۵۶۷" @endif
                                       class="w-full rounded-xl border bg-sand-100 px-4 py-3 outline-none transition focus:bg-sand-50 {{ $errors->has($field) ? 'border-red-400 focus:border-red-500' : 'border-sand-300 focus:border-clay-400' }}">
                                @error($field)
                                    <p id="{{ $field }}-error" class="mt-1.5 text-meta text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach

                        <div class="sm:col-span-2">
                            <label for="product_id" class="mb-2 block text-meta font-semibold text-ink-600">محصول مورد نظر (اختیاری)</label>
                            <div class="relative">
                                <select id="product_id" name="product_id"
                                        class="w-full appearance-none rounded-xl border border-sand-300 bg-sand-100 py-3 pr-4 pl-10 outline-none focus:border-clay-400 focus:bg-sand-50">
                                    <option value="">انتخاب نشده</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" @selected((int) old('product_id', $presetProduct) === $product->id)>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                                <x-icon name="chevron-down" size="16" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-400" />
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="message" class="mb-2 block text-meta font-semibold text-ink-600">
                                متن پیام <span class="text-clay-600" aria-hidden="true">*</span>
                            </label>
                            <textarea id="message" name="message" rows="5" required aria-required="true"
                                      placeholder="متراژ پروژه، شهر، زمان مورد نیاز و هر جزئیاتی که کمک می‌کند دقیق‌تر پاسخ بدهیم."
                                      @if($errors->has('message')) aria-invalid="true" @endif
                                      class="w-full rounded-xl border bg-sand-100 px-4 py-3 leading-relaxed outline-none transition focus:bg-sand-50 {{ $errors->has('message') ? 'border-red-400' : 'border-sand-300 focus:border-clay-400' }}">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1.5 text-meta text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-between gap-4 border-t border-sand-200 pt-6">
                        <p class="text-meta text-ink-400">فیلدهای ستاره‌دار الزامی است.</p>
                        <button type="submit"
                                class="group inline-flex items-center gap-2 rounded-full bg-clay-500 px-8 py-3.5 font-semibold text-white transition hover:bg-clay-600">
                            ارسال درخواست
                            <x-icon name="arrow-left" size="17" class="transition-transform group-hover:-translate-x-1" />
                        </button>
                    </div>
                </form>
            </div>

            <aside class="lg:col-span-5">
                <div class="sticky top-28 space-y-4">
                    <div class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-6">
                        <h2 class="font-bold">تماس مستقیم</h2>
                        <ul class="mt-4 space-y-4 text-[0.9375rem]">
                            <li class="flex gap-3">
                                <x-icon name="phone" size="18" class="mt-1 shrink-0 text-clay-500" />
                                <span>
                                    <span class="block text-meta text-ink-400">دفتر مرکزی</span>
                                    <a href="tel:{{ config('kian.contact.phone_raw') }}" class="tech tap font-bold transition hover:text-clay-600">{{ config('kian.contact.phone') }}</a>
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <x-icon name="mail" size="18" class="mt-1 shrink-0 text-clay-500" />
                                <span>
                                    <span class="block text-meta text-ink-400">واحد فروش</span>
                                    <a href="mailto:{{ config('kian.contact.email') }}" class="tech tap font-bold transition hover:text-clay-600">{{ config('kian.contact.email') }}</a>
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <x-icon name="blueprint" size="18" class="mt-1 shrink-0 text-clay-500" />
                                <span>
                                    <span class="block text-meta text-ink-400">واحد فنی</span>
                                    <a href="mailto:{{ config('kian.contact.technical_email') }}" class="tech tap font-bold transition hover:text-clay-600">{{ config('kian.contact.technical_email') }}</a>
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <x-icon name="pin" size="18" class="mt-1 shrink-0 text-clay-500" />
                                <span>
                                    <span class="block text-meta text-ink-400">کارخانه</span>
                                    <span class="font-semibold leading-relaxed">{{ config('kian.contact.address') }}</span>
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <x-icon name="clock" size="18" class="mt-1 shrink-0 text-clay-500" />
                                <span>
                                    <span class="block text-meta text-ink-400">ساعات کاری</span>
                                    <span class="font-semibold">{{ config('kian.contact.working_hours') }}</span>
                                </span>
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-100 p-6">
                        <h2 class="font-bold">شاید سریع‌تر باشد</h2>
                        <ul class="mt-4 space-y-2">
                            @foreach([
                                ['انتخاب محصول مناسب پروژه', 'finder.show'],
                                ['دانلود دیتاشیت و فایل فنی', 'technical.downloads'],
                                ['پرسش‌های متداول', 'technical.faq'],
                                ['نزدیک‌ترین نمایندگی', 'distributors'],
                            ] as [$label, $route])
                                <li>
                                    <a href="{{ route($route) }}"
                                       class="group flex items-center justify-between gap-2 rounded-lg bg-sand-50 px-4 py-3 text-[0.9375rem] font-semibold transition hover:bg-clay-50 hover:text-clay-700">
                                        {{ $label }}
                                        <x-icon name="chevron-left" size="16" class="text-ink-300 transition group-hover:text-clay-500" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.app>
