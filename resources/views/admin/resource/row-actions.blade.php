{{--
    کنش‌های هر ردیف — یک تعریف، دو چیدمان (کارت موبایل و جدول دسکتاپ).
    اندازه‌ی هدف لمسی از .tap-icon می‌آید (۴۴×۴۴ مطابق HIG اپل).
--}}
<div class="flex shrink-0 items-center gap-2 lg:gap-1">
    @if($url = $resource::publicUrl($record))
        <a href="{{ $url }}" target="_blank" rel="noopener"
           class="tap-icon rounded-lg text-ink-400 transition hover:bg-sand-200 hover:text-ink-900"
           title="مشاهده در سایت" aria-label="مشاهده «{{ $resource::titleFor($record) }}» در سایت">
            <x-icon name="external" size="16" />
        </a>
    @endif
    {{-- تکثیر: کپیِ کامل، پنهان، و رفتن به ویرایشِ همان کپی --}}
    @if($resource::$duplicable && $resource::$creatable)
        <form method="POST" action="{{ $resource::duplicateUrl($record) }}" class="contents">
            @csrf
            <button type="submit"
                    class="tap-icon rounded-lg text-ink-400 transition hover:bg-sand-200 hover:text-clay-600"
                    title="تکثیر" aria-label="تکثیرِ «{{ $resource::titleFor($record) }}»">
                <x-icon name="copy" size="16" />
            </button>
        </form>
    @endif
    <a href="{{ $resource::editUrl($record) }}"
       class="tap-icon rounded-lg text-ink-500 transition hover:bg-sand-200 hover:text-clay-600"
       title="ویرایش" aria-label="ویرایش «{{ $resource::titleFor($record) }}»">
        <x-icon name="blueprint" size="16" />
    </a>
</div>
