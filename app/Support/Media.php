<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * نشانی تصویرهای آپلودشده‌ی پنل.
 *
 * سایت باید با دیتابیس خالی هم درست دیده شود، پس هیچ‌جا مستقیم به ستون تصویر
 * تکیه نمی‌کنیم: این کلاس در نبودِ فایل null برمی‌گرداند و ویو به آرت وکتوری
 * برمی‌گردد.
 */
class Media
{
    public static string $disk = 'public';

    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        // مسیر کامل (مثلاً سیدِ نمونه با آدرس بیرونی) دست‌نخورده می‌ماند
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return static::relative(Storage::disk(static::$disk)->url($path));
    }

    /**
     * نشانی را نسبت به ریشه می‌کند.
     *
     * Storage::url نشانی مطلق از APP_URL می‌سازد؛ اگر APP_URL دقیقاً با میزبانی
     * که کاربر با آن سایت را باز کرده یکی نباشد — http در برابر https، با یا
     * بدون www، یا پشت پراکسی — همه‌ی تصویرها بی‌صدا خراب می‌شوند. نشانی نسبی
     * همیشه روی میزبان جاری حل می‌شود.
     *
     * اگر دیسک عمداً روی میزبان دیگری (CDN) تنظیم شده باشد، مطلق می‌ماند.
     */
    protected static function relative(string $url): string
    {
        $parts = parse_url($url);

        if (! isset($parts['host'])) {
            return $url;
        }

        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);

        if ($appHost !== null && $parts['host'] !== $appHost) {
            return $url;   // میزبان دیگر یعنی CDN، دست نمی‌زنیم
        }

        return ($parts['path'] ?? '/').(isset($parts['query']) ? '?'.$parts['query'] : '');
    }

    /** @return array<int, string> نشانی همه‌ی تصویرهای یک ستون گالری */
    public static function gallery(mixed $paths): array
    {
        return collect((array) $paths)
            ->map(fn ($path) => static::url(is_string($path) ? $path : null))
            ->filter()
            ->values()
            ->all();
    }

    public static function has(?string $path): bool
    {
        return static::url($path) !== null;
    }
}
