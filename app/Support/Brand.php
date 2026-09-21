<?php

namespace App\Support;

/**
 * نام و نشانیِ شرکت، به زبانِ صفحه.
 *
 * مقدارِ پایه در config/kian.php است — فارسی، و قابل تغییر از .env بی‌آنکه
 * کسی به کد دست بزند. زبان‌های دیگر رویش می‌نشینند: هر کلیدی که در
 * lang/<code>/site.php زیر brand یا contact آمده باشد، جای مقدار پایه را
 * می‌گیرد و هرچه نیامده، همان پایه می‌ماند.
 *
 * به همین دلیل پرونده‌ی فارسی این کلیدها را *ندارد*: داشتنش یعنی نامِ شرکت
 * دو جا نوشته شده و روزی یکی‌شان عوض می‌شود و دیگری نه.
 *
 * Schema.org و پرونده‌های پیکربندی همچنان مستقیم از config می‌خوانند: آنجا
 * نامِ حقوقیِ ثبت‌شده لازم است و نه ترجمه‌اش.
 */
class Brand
{
    public static function name(): string
    {
        return static::value('brand.name');
    }

    public static function legalName(): string
    {
        return static::value('brand.legal_name');
    }

    public static function tagline(): string
    {
        return static::value('brand.tagline');
    }

    public static function address(): string
    {
        return static::value('contact.address');
    }

    public static function workingHours(): string
    {
        return static::value('contact.working_hours');
    }

    /** شماره‌ی نمایشی — با ارقامِ خطِ زبان جاری. */
    public static function phone(): string
    {
        return Jalali::digits(config('kian.contact.phone'));
    }

    public static function salesPhone(): string
    {
        return Jalali::digits(config('kian.contact.sales_phone'));
    }

    public static function postalCode(): string
    {
        return Jalali::digits(config('kian.contact.postal_code'));
    }

    /** سالِ تأسیس، در تقویمِ زبانِ جاری. */
    public static function founded(): string
    {
        $gregorian = (int) config('kian.brand.founded');

        return Locales::calendar() === 'jalali'
            ? Jalali::digits($gregorian - 621)
            : Jalali::digits($gregorian);
    }

    /**
     * ترجمه اگر بود، وگرنه مقدار پیکربندی.
     *
     * __() برای کلیدِ نبوده خودِ کلید را برمی‌گرداند؛ همان علامتِ «ترجمه‌ای
     * نیست» است.
     */
    protected static function value(string $key): string
    {
        $line = "site.{$key}";
        $translated = __($line);

        return is_string($translated) && $translated !== $line
            ? $translated
            : (string) config("kian.{$key}");
    }
}
