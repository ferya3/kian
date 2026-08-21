<?php

namespace App\Support;

/**
 * تبدیل ارقام فارسی و عربی به لاتین.
 *
 * قلم‌های سایت ارقام را فارسی نشان می‌دهند و صفحه‌کلید فارسی هم ارقام فارسی
 * می‌فرستد؛ اما <input type="number"> و قواعد اعتبارسنجی فقط ارقام لاتین را
 * می‌فهمند. بدون این تبدیل، «۲۵» یا خالی می‌شود یا به صفر می‌نشیند.
 */
class Digits
{
    private const FA = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

    private const AR = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

    private const EN = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    /** ارقام را لاتین می‌کند و جداکننده‌ی اعشار فارسی را به نقطه برمی‌گرداند. */
    public static function toLatin(string $value): string
    {
        return str_replace(
            [...self::FA, ...self::AR, '٫', '،', "\u{200f}", "\u{200e}"],
            [...self::EN, ...self::EN, '.', ',', '', ''],
            $value,
        );
    }

    /** فقط ارقام لاتین را نگه می‌دارد — برای شماره تماس. */
    public static function digitsOnly(string $value): string
    {
        return preg_replace('/\D/', '', self::toLatin($value)) ?? '';
    }
}
