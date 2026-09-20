<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * زبان‌های سایت.
 *
 * فهرست در config/locales.php است و این کلاس تنها راه خواندنش. هر جای سایت
 * که به زبان کار دارد — مسیر، جهت، قلم، hreflang، نقشه‌ی سایت — از اینجا
 * می‌پرسد، تا افزودن زبان تازه فقط یک مدخل در تنظیمات باشد.
 *
 * نکته‌ی مسیرها: همه‌ی صفحه‌های عمومی پیشوند زبان دارند، حتی زبان پیش‌فرض.
 * تقارنِ /fa/products و /en/products ارزشش را دارد: با پیشوندِ اختیاری،
 * هر ۱۳۲ فراخوانیِ route() در ویوها باید عوض می‌شد.
 */
class Locales
{
    public const COOKIE = 'locale';

    /** @return Collection<string, array> فقط زبان‌های روشن */
    public static function all(): Collection
    {
        return collect(config('locales.available'))
            ->filter(fn (array $meta) => $meta['enabled'] ?? true);
    }

    /** @return array<int, string> */
    public static function codes(): array
    {
        return static::all()->keys()->all();
    }

    public static function default(): string
    {
        return config('locales.default', 'fa');
    }

    public static function current(): string
    {
        return static::supports(app()->getLocale()) ? app()->getLocale() : static::default();
    }

    public static function supports(?string $code): bool
    {
        return $code !== null && static::all()->has($code);
    }

    /** @return array<string, mixed> */
    public static function meta(?string $code = null): array
    {
        $code ??= static::current();

        return static::all()->get($code) ?? static::all()->get(static::default()) ?? [];
    }

    public static function dir(?string $code = null): string
    {
        return static::meta($code)['dir'] ?? 'rtl';
    }

    public static function isRtl(?string $code = null): bool
    {
        return static::dir($code) === 'rtl';
    }

    public static function html(?string $code = null): string
    {
        return static::meta($code)['html'] ?? $code ?? static::default();
    }

    public static function name(?string $code = null): string
    {
        return static::meta($code)['name'] ?? (string) $code;
    }

    public static function digits(?string $code = null): string
    {
        return static::meta($code)['digits'] ?? 'latn';
    }

    public static function calendar(?string $code = null): string
    {
        return static::meta($code)['calendar'] ?? 'gregorian';
    }

    public static function isDefault(?string $code = null): bool
    {
        return ($code ?? static::current()) === static::default();
    }

    /**
     * همین صفحه، به زبانی دیگر.
     *
     * از مسیر جاری ساخته می‌شود و نه از رشته‌ی URL، تا پارامترها — شناسه‌ی
     * محصول، فیلتر دسته، شماره‌ی صفحه — سرِ جایشان بمانند.
     */
    public static function urlFor(string $code, ?Request $request = null): string
    {
        $request ??= request();
        $route = $request->route();

        if ($route === null || ! $route->getName()) {
            return url($code);
        }

        return route(
            $route->getName(),
            array_merge($route->parameters(), $request->query(), ['locale' => $code]),
        );
    }

    /**
     * زبانی که بازدیدکننده احتمالاً می‌خواهد.
     *
     * اول انتخاب قبلی خودش، بعد Accept-Language مرورگر، و در نهایت پیش‌فرض.
     */
    public static function preferred(?Request $request = null): string
    {
        $request ??= request();

        $chosen = $request->cookie(static::COOKIE);

        if (static::supports($chosen)) {
            return $chosen;
        }

        return $request->getPreferredLanguage(static::codes()) ?? static::default();
    }
}
