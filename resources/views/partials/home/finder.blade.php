{{--
    موتور انتخاب محصول — اولین چیز بعد از هیرو.

    عنوان و توضیح بالای فرم می‌آیند: بازدیدکننده‌ای که تازه از هیرو پایین آمده،
    اول باید بداند این چهار پرسش چه چیزی از او می‌خواهد و در ازایش چه می‌دهد؛
    فرمِ بی‌مقدمه، سؤالِ بی‌زمینه است.
--}}
<section id="find-your-block" class="relative bg-sand-100 section-sm" aria-labelledby="finder-heading">
    <div class="container-page">

        <div>
            <p class="eyebrow text-clay-600" data-reveal>{{ __('site.home.finder.eyebrow') }}</p>
            <h2 id="finder-heading" class="mt-3 text-h2 font-extrabold text-balance" data-reveal>
                {{ __('site.home.finder.title') }}
            </h2>
            <p class="mt-4 max-w-2xl text-lead text-ink-500" data-reveal>
                {{ __('site.home.finder.lead') }}
            </p>
        </div>

        <div class="mt-8 lg:mt-10" data-reveal>
            @include('partials.finder-form')
        </div>

        {{--
            سه پرسونا در یک ردیف. پیش‌تر روی دسکتاپ ستونی کنار متن می‌نشستند و
            ارتفاع زیادی می‌گرفتند؛ حالا یک نوار افقی‌اند و از sm به بالا کنار
            هم. زیر sm ناچار روی هم می‌روند: سه کارت متن‌دار در ۳۶۰ پیکسل
            خوانا نمی‌ماند.
        --}}
        <ul class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-3" data-reveal-stagger="80">
            @foreach(\App\Support\Options::audiences() as $key => $audience)
                <li>
                    <a href="{{ route($audience['route']) }}" data-reveal
                       class="group flex h-full items-center gap-3 rounded-xl border border-sand-300 bg-sand-50 px-4 py-3.5 transition hover:border-clay-300 hover:bg-clay-50">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-clay-100 text-clay-600 transition group-hover:bg-clay-500 group-hover:text-white">
                            <x-icon :name="$audience['icon']" size="18" />
                        </span>
                        <span class="min-w-0">
                            <span class="block text-meta font-bold">{{ $audience['label'] }}</span>
                            <span class="block text-xs leading-snug text-ink-400">{{ $audience['question'] }}</span>
                        </span>
                        <x-icon name="chevron-left" size="16" class="mr-auto shrink-0 text-ink-300 transition group-hover:text-clay-500" />
                    </a>
                </li>
            @endforeach
        </ul>

    </div>
</section>
