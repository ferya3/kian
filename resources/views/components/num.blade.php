@props(['value', 'unit' => null, 'decimals' => null])

@php
    /*
     * نمایش امن عدد فنی در متن راست‌به‌چپ.
     *
     * مشکل: در پاراگراف RTL، رشته‌ای مثل «۹۰۰ °C» به‌صورت «C° ۹۰۰» رندر
     * می‌شود، چون نویسه‌های خنثی (° ± / ×) جهت پاراگراف را می‌گیرند.
     * راه‌حل: اگر مقدار هیچ حرف فارسی/عربی ندارد، آن را داخل یک جزیره‌ی LTR
     * ایزوله می‌کنیم. اگر دارد، دست‌نخورده می‌ماند تا فارسی درست بخواند.
     */
    $text = trim($value.($unit ? ' '.$unit : ''));

    if (is_numeric($value)) {
        $formatted = $decimals === null
            ? rtrim(rtrim(number_format((float) $value, 3), '0'), '.')
            : number_format((float) $value, $decimals);
        $text = trim($formatted.($unit ? ' '.$unit : ''));
    }

    $text = \App\Support\Jalali::digits($text);
    $hasPersianLetters = (bool) preg_match('/[\p{Arabic}]/u', preg_replace('/[۰-۹٫٬]/u', '', $text));
@endphp

@if($hasPersianLetters)
    <span {{ $attributes->merge(['class' => 'tech']) }}>{{ $text }}</span>
@else
    <bdi dir="ltr" {{ $attributes->merge(['class' => 'tech inline-block whitespace-nowrap']) }}>{{ $text }}</bdi>
@endif
