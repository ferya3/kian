<?php

namespace App\Support;

use App\Models\Setting;

/**
 * تلفن و ایمیل و شبکه‌های اجتماعیِ شرکت — از پنل، و اگر پنل خالی بود از پیکربندی.
 *
 * تا پیش از این تلفن و نشانی فقط در config/kian.php و .env بودند: عوض‌کردنِ
 * یک رقم یعنی ورود به سرور و ساختنِ دوباره‌ی کش. حالا مدیر از «پنل ←
 * اطلاعات تماس» عوضشان می‌کند و پیکربندی فقط مقدارِ پیش‌فرض است — برای
 * نصبِ تازه، یا میدانی که هنوز کسی پرش نکرده.
 *
 * هر جای سایت که تلفن یا نشانی لازم دارد از همین‌جا می‌خواند. دو منبع که
 * باید یکی باشند، روزی از هم جدا می‌شوند: هدر یک شماره نشان می‌دهد و
 * Schema.org شماره‌ی دیگری به گوگل می‌دهد.
 */
class Contact
{
    /** پیشوندِ کلید در جدول تنظیمات. */
    public const PREFIX = 'contact.';

    /**
     * میدان‌های قابل ویرایش و جایشان در پیکربندی.
     *
     * نشانی و ساعت کاری اینجا نیستند: هر مکان نشانی و ساعت خودش را دارد و
     * در App\Models\Location است. اینجا فقط آنچه مالِ کلِ شرکت است.
     */
    public const FIELDS = [
        'phone' => 'kian.contact.phone',
        'sales_phone' => 'kian.contact.sales_phone',
        'sales_extension' => 'kian.contact.sales_extension',
        'email' => 'kian.contact.email',
        'technical_email' => 'kian.contact.technical_email',
        'instagram' => 'kian.social.instagram',
        'linkedin' => 'kian.social.linkedin',
        'aparat' => 'kian.social.aparat',
        'telegram' => 'kian.social.telegram',
    ];

    public const SOCIAL = ['instagram', 'linkedin', 'aparat', 'telegram'];

    /** مقدارِ خام، بی هیچ قالب‌بندی: پنل اگر بود، وگرنه پیکربندی. */
    public static function get(string $field): string
    {
        // config('') کلِ پیکربندی را برمی‌گرداند و نه رشته‌ی خالی
        if (! isset(static::FIELDS[$field])) {
            throw new \InvalidArgumentException("میدانِ تماسِ ناشناخته: {$field}");
        }

        return static::stored($field) ?? (string) config(static::FIELDS[$field]);
    }

    /**
     * مقداری که در پنل ذخیره شده، یا null اگر هرگز ذخیره نشده.
     *
     * ردیفِ خالی با ردیفِ نبوده فرق دارد. مدیری که داخلی را پاک می‌کند
     * یعنی «داخلی نداریم»؛ اگر خالی را «ذخیره‌نشده» می‌گرفتیم، همان ۱۰۶ِ
     * پیش‌فرض از پیکربندی برمی‌گشت و پاک‌کردن هیچ اثری نداشت.
     */
    public static function stored(string $key): ?string
    {
        $map = Setting::map();
        $full = static::PREFIX.$key;

        return array_key_exists($full, $map) ? trim((string) $map[$full]) : null;
    }

    // ---------------------------------------------------- تلفن ----

    /**
     * نشانیِ tel: از روی همان شماره‌ای که نمایش داده می‌شود.
     *
     * پیش‌تر شماره‌ی نمایشی و شماره‌ی شماره‌گیری دو میدانِ جدا بودند
     * (phone و phone_raw). هر بار یکی عوض می‌شد و دیگری نه، صفحه یک شماره
     * نشان می‌داد و دکمه شماره‌ی دیگری می‌گرفت — بی‌آنکه کسی بفهمد، چون
     * هیچ‌کس روی شماره‌ی سایتِ خودش کلیک نمی‌کند. حالا یکی است و دیگری از
     * رویش ساخته می‌شود.
     *
     * داخلی با دو ویرگول می‌آید: مکث، و بعد رقم‌ها DTMF فرستاده می‌شوند.
     */
    public static function tel(string $number, ?string $extension = null): string
    {
        $digits = preg_replace('/[^\d+]/', '', Digits::toLatin($number));

        if (str_starts_with($digits, '00')) {
            $digits = '+'.substr($digits, 2);
        } elseif (str_starts_with($digits, '0')) {
            $digits = '+98'.substr($digits, 1);
        }

        $extension = preg_replace('/\D/', '', Digits::toLatin((string) $extension));

        return $extension === '' ? $digits : "{$digits},,{$extension}";
    }

    public static function phoneTel(): string
    {
        return static::tel(static::get('phone'));
    }

    public static function salesPhoneTel(): string
    {
        return static::tel(static::get('sales_phone'), static::get('sales_extension'));
    }

    /** @return array<string, string> فقط پیوندهایی که واقعاً به صفحه‌ای اشاره می‌کنند */
    public static function social(): array
    {
        return collect(static::SOCIAL)
            ->mapWithKeys(fn (string $key) => [$key => static::get($key)])
            ->filter(fn (string $url) => static::isProfile($url))
            ->all();
    }

    /**
     * نشانیِ خالیِ شبکه‌ی اجتماعی، پیوند نیست.
     *
     * پیش‌فرضِ پیکربندی «https://instagram.com/» است — بی نامِ کاربری. آیکون
     * نشان داده می‌شد و بازدیدکننده به صفحه‌ی اصلیِ اینستاگرام می‌رسید.
     */
    protected static function isProfile(string $url): bool
    {
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');

        return filter_var($url, FILTER_VALIDATE_URL) !== false && $path !== '';
    }
}
