@props(['compact' => false])

{{--
    موتور انتخاب محصول.
    بدون جاوااسکریپت: submit عادی به صفحه‌ی نتایج.
    با جاوااسکریپت: نتیجه در همان صفحه و بدون بارگذاری مجدد ظاهر می‌شود.
--}}
<div x-data="productFinder('{{ route('finder.results') }}')" class="w-full">
    <form x-ref="form" action="{{ route('finder.results') }}" method="GET"
          @submit.prevent="submit()"
          class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-5 shadow-lift lg:p-7">

        <div class="grid gap-4 lg:grid-cols-4">
            @php
                $fields = [
                    ['name' => 'project_type', 'label' => 'نوع پروژه', 'options' => config('kian.finder.project_types')],
                    ['name' => 'wall_type', 'label' => 'نوع دیوار', 'options' => config('kian.finder.wall_types')],
                ];
            @endphp

            @foreach($fields as $field)
                <div>
                    <label for="finder-{{ $field['name'] }}" class="mb-2 block text-[0.8125rem] font-semibold text-ink-600">
                        {{ $field['label'] }}
                    </label>
                    <div class="relative">
                        <select id="finder-{{ $field['name'] }}" name="{{ $field['name'] }}"
                                x-model="criteria.{{ $field['name'] }}"
                                class="w-full appearance-none rounded-xl border border-sand-300 bg-sand-100 py-3.5 pr-4 pl-10 text-[0.9375rem] font-semibold text-ink-800 outline-none transition focus:border-clay-400 focus:bg-sand-50">
                            <option value="">انتخاب کنید</option>
                            @foreach($field['options'] as $value => $option)
                                <option value="{{ $value }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                        <x-icon name="chevron-down" size="17" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-400" />
                    </div>
                </div>
            @endforeach

            <div>
                <label for="finder-thickness" class="mb-2 block text-[0.8125rem] font-semibold text-ink-600">
                    ضخامت مورد نیاز
                </label>
                <div class="relative">
                    <select id="finder-thickness" name="thickness" x-model="criteria.thickness"
                            class="w-full appearance-none rounded-xl border border-sand-300 bg-sand-100 py-3.5 pr-4 pl-10 text-[0.9375rem] font-semibold text-ink-800 outline-none transition focus:border-clay-400 focus:bg-sand-50">
                        <option value="">مهم نیست</option>
                        @foreach(config('kian.finder.thicknesses') as $thickness)
                            <option value="{{ $thickness }}">{{ \App\Support\Jalali::digits($thickness) }} سانتی‌متر</option>
                        @endforeach
                    </select>
                    <x-icon name="chevron-down" size="17" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-400" />
                </div>
            </div>

            <div>
                <label for="finder-insulation" class="mb-2 block text-[0.8125rem] font-semibold text-ink-600">
                    نیاز به عایق حرارتی
                </label>
                <div class="relative">
                    <select id="finder-insulation" name="insulation" x-model="criteria.insulation"
                            class="w-full appearance-none rounded-xl border border-sand-300 bg-sand-100 py-3.5 pr-4 pl-10 text-[0.9375rem] font-semibold text-ink-800 outline-none transition focus:border-clay-400 focus:bg-sand-50">
                        <option value="">مهم نیست</option>
                        @foreach(config('kian.finder.insulation_levels') as $value => $level)
                            <option value="{{ $value }}">{{ $level['label'] }}</option>
                        @endforeach
                    </select>
                    <x-icon name="chevron-down" size="17" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-400" />
                </div>
            </div>
        </div>

        <div class="mt-5 flex flex-wrap items-center justify-between gap-4 border-t border-sand-200 pt-5">
            <p class="text-[0.8125rem] text-ink-400">
                <span x-show="!ready">دست‌کم دو گزینه را انتخاب کنید تا پیشنهاد دقیق‌تری بدهیم.</span>
                <span x-show="ready" x-cloak class="flex items-center gap-1.5 font-semibold text-clay-600">
                    <x-icon name="check" size="15" />
                    آماده‌ی پیشنهاد است
                </span>
            </p>

            <div class="flex items-center gap-2">
                <button type="button" x-show="answered > 0" x-cloak @click="reset()"
                        class="rounded-full px-4 py-2 text-[0.8125rem] text-ink-400 transition hover:text-ink-900">
                    پاک کردن
                </button>
                <button type="submit"
                        :disabled="!ready || loading"
                        class="group inline-flex items-center justify-center gap-2 rounded-full bg-clay-500 px-7 py-3.5 font-semibold text-white transition-all duration-300
                               hover:bg-clay-600 disabled:cursor-not-allowed disabled:bg-sand-300 disabled:text-ink-400">
                    <span x-show="!loading">مشاهده محصولات پیشنهادی</span>
                    <span x-show="loading" x-cloak class="flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity=".25" stroke-width="3"/>
                            <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                        در حال محاسبه…
                    </span>
                    <x-icon name="arrow-left" size="17" class="transition-transform duration-300 group-hover:-translate-x-1" />
                </button>
            </div>
        </div>
    </form>

    <div x-ref="results" x-html="results" class="focus:outline-none"
         role="region" aria-live="polite" aria-label="نتیجه انتخاب محصول"></div>

    <noscript>
        <p class="mt-3 text-[0.8125rem] text-ink-400">
            برای دیدن نتیجه، دکمه‌ی بالا شما را به صفحه‌ی نتایج می‌برد.
        </p>
    </noscript>
</div>
