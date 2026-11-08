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

    /**
     * نشانیِ خودِ فایلِ آپلودشده، بی نسخه‌ی WebP.
     *
     * برای جاهایی که بیرون از سایت خوانده می‌شوند و قالبِ اصل را می‌خواهند:
     * favicon، apple-touch-icon و og:image (پیش‌نمایشِ لینک در پیام‌رسان‌ها
     * WebP را همه‌جا نمی‌شناسند).
     */
    public static function original(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return static::relative(Storage::disk(static::$disk)->url($path));
    }

    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        // مسیر کامل (مثلاً سیدِ نمونه با آدرس بیرونی) دست‌نخورده می‌ماند
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        /*
        | عکس با نسخه‌ی سبک: به‌جای اصلِ چندمگابایتی، بزرگ‌ترین WebP ِ ساخته‌شده.
        | srcset (پایین) بقیه‌ی پهناها را به مرورگر می‌دهد.
        */
        if ($variants = ImageVariants::existing($path, static::$disk)) {
            $path = end($variants);
        }

        return static::relative(Storage::disk(static::$disk)->url($path));
    }

    /**
     * srcset برای نشانی‌ای که url() ساخته — همه‌ی پهناهای همان عکس.
     *
     * از روی نشانی کار می‌کند و نه مسیر، چون ویوها نشانی دارند. null اگر عکس
     * نسخه ندارد (مثلاً هنوز بهینه نشده) — آن‌وقت src تنها کافی است.
     */
    public static function srcset(?string $url): ?string
    {
        if (! $url || ! preg_match('~^(.*/)variants/(.+)-\d+w\.webp$~', $url, $m)) {
            return null;
        }

        $prefix = parse_url(Storage::disk(static::$disk)->url(''), PHP_URL_PATH) ?: '/storage/';
        $dir = substr($m[1], strlen(rtrim($prefix, '/').'/'));

        $entries = [];
        foreach (ImageVariants::WIDTHS as $width) {
            $variant = $dir.'variants/'.$m[2].'-'.$width.'w.webp';
            if (Storage::disk(static::$disk)->exists($variant)) {
                $entries[] = $m[1].'variants/'.$m[2].'-'.$width.'w.webp '.$width.'w';
            }
        }

        return count($entries) > 1 ? implode(', ', $entries) : null;
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
