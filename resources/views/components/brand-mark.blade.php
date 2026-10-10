@php
    $logo = \App\Models\SiteMedia::url('brand.logo');

    /*
    | اندازه‌ی پیش‌فرض فقط وقتی که صدازننده اندازه نداده.
    |
    | merge کلاس‌ها را کنار هم می‌گذارد و نه جایگزین: «h-10 w-10» ِ پیش‌فرض
    | و «h-14» ِ صدازننده هر دو می‌نشستند و برنده را ترتیبِ CSS ِ ساخته‌شده
    | تعیین می‌کرد، نه نیتِ صدازننده.
    */
    $sized = $attributes->has('class');
@endphp

@if($logo)
    {{--
        لوگوی آپلودشده جای نشانه‌ی وکتوری می‌نشیند.

        ارتفاع از صدازننده، پهنا خودکار: بیشترِ لوگوها افقی‌اند، و در کادرِ
        مربعِ پیشین با object-contain به نوارِ باریکی کوچک می‌شدند که در هدر
        اصلاً دیده نمی‌شد.
    --}}
    <img src="{{ $logo }}" alt="{{ config('kian.brand.name') }}"
         {{ $sized ? $attributes->class('object-contain') : $attributes->merge(['class' => 'h-10 w-auto object-contain']) }}>
@else

{{-- نشانه‌ی برند: مقطع یک بلوک سفالی با حفره‌های عمودی --}}
<svg {{ $sized ? $attributes->class('aspect-square') : $attributes->merge(['class' => 'h-10 w-10']) }} viewBox="0 0 40 40" fill="none" role="img"
     aria-label="{{ config('kian.brand.name') }}">
    <rect x="1.25" y="1.25" width="37.5" height="37.5" rx="6" fill="#B4552D"/>
    <rect x="1.25" y="1.25" width="37.5" height="37.5" rx="6" fill="url(#kian-mark-grad)" fill-opacity=".55"/>
    <g fill="#F5F3EF" fill-opacity=".92">
        <rect x="7.5" y="9" width="6" height="8" rx="1.2"/>
        <rect x="17" y="9" width="6" height="8" rx="1.2"/>
        <rect x="26.5" y="9" width="6" height="8" rx="1.2"/>
        <rect x="12.25" y="21" width="6" height="10" rx="1.2"/>
        <rect x="21.75" y="21" width="6" height="10" rx="1.2"/>
    </g>
    <defs>
        <linearGradient id="kian-mark-grad" x1="40" y1="0" x2="0" y2="40" gradientUnits="userSpaceOnUse">
            <stop stop-color="#E2732F"/>
            <stop offset="1" stop-color="#7C361C"/>
        </linearGradient>
    </defs>
</svg>

@endif
