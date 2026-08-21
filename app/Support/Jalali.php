<?php

namespace App\Support;

use DateTimeInterface;
use IntlDateFormatter;

/**
 * تاریخ شمسی با استفاده از افزونه‌ی intl.
 * بدون وابستگی به پکیج جانبی؛ تقویم persian در ICU تعریف شده است.
 */
class Jalali
{
    public static function format(?DateTimeInterface $date, string $pattern = 'd MMMM y'): string
    {
        if (! $date) {
            return '';
        }

        $formatter = new IntlDateFormatter(
            'fa_IR@calendar=persian',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            $date->getTimezone(),
            IntlDateFormatter::TRADITIONAL,
            $pattern
        );

        return $formatter->format($date) ?: '';
    }

    public static function year(?DateTimeInterface $date = null): string
    {
        return self::format($date ?? new \DateTimeImmutable, 'y');
    }

    /** تبدیل ارقام لاتین به فارسی — برای اعدادی که از دیتابیس می‌آیند. */
    public static function digits(string|int|float|null $value): string
    {
        return strtr((string) $value, [
            '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
            '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
            ',' => '٬', '.' => '٫',
        ]);
    }
}
