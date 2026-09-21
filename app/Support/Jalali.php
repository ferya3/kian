<?php

namespace App\Support;

use DateTimeInterface;
use IntlDateFormatter;

/**
 * تاریخ، به تقویم و خطِ زبانِ صفحه.
 *
 * بدون وابستگی به پکیج جانبی؛ تقویم persian در ICU تعریف شده است.
 *
 * نام کلاس از روزی مانده که سایت تک‌زبانه بود و هر تاریخی شمسی. حالا
 * config/locales.php می‌گوید هر زبان چه تقویمی دارد — فارسی جلالی، انگلیسی و
 * عربی میلادی — و اینجا فقط اجرا می‌شود. نامش را عوض نکردیم چون ۱۰ ویو
 * صدایش می‌زنند و تغییر نام، تغییری در رفتار نیست.
 */
class Jalali
{
    public static function format(?DateTimeInterface $date, string $pattern = 'd MMMM y'): string
    {
        if (! $date) {
            return '';
        }

        $formatter = new IntlDateFormatter(
            static::intlLocale(),
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            $date->getTimezone(),
            IntlDateFormatter::TRADITIONAL,
            $pattern
        );

        /*
         * ارقام را خودمان می‌گذاریم و نه ICU.
         *
         * fa_IR ارقام فارسی می‌دهد و en ارقام لاتین — ولی عربی که تقویمش
         * میلادی است، با locale عربی ارقام هندی می‌گیرد و با لاتین، لاتین.
         * digits() همین تصمیم را برای کل سایت می‌گیرد، پس تاریخ هم از همان
         * راه می‌رود تا «۱۲ مارس» و «12 مارس» در یک صفحه کنار هم نیفتند.
         */
        return static::digits($formatter->format($date) ?: '');
    }

    public static function year(?DateTimeInterface $date = null): string
    {
        return self::format($date ?? new \DateTimeImmutable, 'y');
    }

    /**
     * شناسه‌ی ICU برای زبان جاری.
     *
     * nu=latn ارقامِ خودِ ICU را لاتین نگه می‌دارد تا digits() تنها جایی
     * باشد که خطِ ارقام را تعیین می‌کند.
     */
    protected static function intlLocale(): string
    {
        $locale = Locales::html().'@numbers=latn';

        return Locales::calendar() === 'jalali'
            ? $locale.';calendar=persian'
            : $locale;
    }

    /** تبدیل ارقام لاتین به فارسی — برای اعدادی که از دیتابیس می‌آیند. */
    /**
     * ارقام را به خطِ زبان جاری می‌برد.
     *
     * انگلیسی ارقام لاتین می‌خواهد و عربی ارقام هندی؛ پیش از چندزبانه‌شدن
     * این متد همیشه فارسی می‌داد و «900 °C» در نسخه‌ی انگلیسی هم «۹۰۰» بود.
     * کدام خط برای کدام زبان، در config/locales.php است.
     */
    public static function digits(string|int|float|null $value): string
    {
        /*
         * اول لاتین، بعد خطِ مقصد.
         *
         * ورودی همیشه لاتین نیست: یک مقدار در .env، یا متنی که مدیر در پنل
         * وارد کرده، می‌تواند از پیش ارقام فارسی داشته باشد. بی این خط، همان
         * «۰۲۱» در نسخه‌ی انگلیسی هم «۰۲۱» می‌ماند، چون چیزی برای تبدیل
         * پیدا نمی‌شد.
         */
        $value = Digits::toLatin((string) $value);

        return match (Locales::digits()) {
            'fa' => strtr($value, [
                '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
                '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
                ',' => '٬', '.' => '٫',
            ]),
            'ar' => strtr($value, [
                '0' => '٠', '1' => '١', '2' => '٢', '3' => '٣', '4' => '٤',
                '5' => '٥', '6' => '٦', '7' => '٧', '8' => '٨', '9' => '٩',
                ',' => '٬', '.' => '٫',
            ]),
            default => $value,
        };
    }
}
