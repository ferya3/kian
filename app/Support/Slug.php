<?php

namespace App\Support;

/**
 * تولید slug فارسی‌پسند.
 *
 * Str::slug روی متن فارسی رشته‌ی خالی برمی‌گرداند چون حروف غیرلاتین را حذف
 * می‌کند. آدرس‌های فارسی برای موتور جستجو و برای کاربر خواناتر از
 * transliteration ناقص‌اند، پس حروف را نگه می‌داریم و فقط فاصله و نقطه‌گذاری
 * را به خط تیره تبدیل می‌کنیم.
 */
class Slug
{
    protected const PERSIAN_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

    protected const ARABIC_DIGITS = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

    protected const LATIN_DIGITS = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    public static function make(string $value, string $separator = '-'): string
    {
        // یکسان‌سازی نویسه‌های عربی و فارسی و ارقام
        $value = str_replace(['ي', 'ك', 'ۀ', 'ة'], ['ی', 'ک', 'ه', 'ه'], $value);
        $value = str_replace([...self::PERSIAN_DIGITS, ...self::ARABIC_DIGITS], [...self::LATIN_DIGITS, ...self::LATIN_DIGITS], $value);

        // نیم‌فاصله و فاصله‌ی صفر عرض → جداکننده
        $value = preg_replace('/[\x{200B}-\x{200F}\x{FEFF}]/u', $separator, $value);

        // هر چیزی جز حرف و رقم → جداکننده
        $value = preg_replace('/[^\p{L}\p{N}]+/u', $separator, $value);

        $quoted = preg_quote($separator, '/');
        $value = preg_replace('/'.$quoted.'{2,}/u', $separator, $value);

        return trim(mb_strtolower($value, 'UTF-8'), $separator);
    }
}
