{{--
    کنش‌های هر ردیف — یک تعریف، دو چیدمان (کارت موبایل و جدول دسکتاپ).
    اندازه‌ی هدف لمسی از .tap-icon می‌آید (۴۴×۴۴ مطابق HIG اپل).
--}}
<div class="flex shrink-0 items-center gap-1">
    @if($url = $resource::publicUrl($record))
        <a href="{{ $url }}" target="_blank" rel="noopener"
           class="tap-icon rounded-lg text-ink-400 transition hover:bg-sand-200 hover:text-ink-900"
           title="مشاهده در سایت" aria-label="مشاهده «{{ $resource::titleFor($record) }}» در سایت">
            <x-icon name="external" size="16" />
        </a>
    @endif
    <a href="{{ $resource::editUrl($record) }}"
       class="tap-icon rounded-lg text-ink-500 transition hover:bg-sand-200 hover:text-clay-600"
       title="ویرایش" aria-label="ویرایش «{{ $resource::titleFor($record) }}»">
        <x-icon name="blueprint" size="16" />
    </a>
</div>
