<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * نسخه‌های سبکِ هر عکسِ آپلودشده: WebP در چند پهنا.
 *
 * عکسی که مدیر از گوشی یا دوربین آپلود می‌کند معمولاً ۳ تا ۸ مگابایت است و
 * ۴۰۰۰ پیکسل پهنا دارد؛ سایت همان را به گوشیِ ۴۰۰ پیکسلی می‌فرستاد. روی
 * اینترنتِ همراه، تصویرِ هیرو ده‌ها ثانیه طول می‌کشید و PageSpeed ِ موبایل
 * به ۶۹ می‌رسید.
 *
 * حالا کنارِ هر عکس، در پوشه‌ی variants، نسخه‌های ۴۸۰ تا ۱۹۲۰ پیکسلی به WebP
 * ساخته می‌شود. سایت به‌جای اصل، بزرگ‌ترینِ آن‌ها را می‌فرستد و با srcset
 * مرورگر به اندازه‌ی صفحه‌اش انتخاب می‌کند. اصلِ فایل دست نمی‌خورد.
 *
 *   admin/site-media/abc.jpg
 *   admin/site-media/variants/abc-480.webp   …-768  …-1280  …-1920
 */
class ImageVariants
{
    public const WIDTHS = [480, 768, 1280, 1920];

    protected const QUALITY = 78;

    protected const RASTER = '~\.(jpe?g|png|webp)$~i';

    /** @var array<string, array<int, string>> */
    protected static array $memo = [];

    public static function isRaster(?string $path): bool
    {
        return is_string($path) && preg_match(self::RASTER, $path) && ! str_contains($path, '/variants/');
    }

    public static function pathFor(string $path, int $width): string
    {
        $dir = trim(dirname($path), './');

        return ($dir !== '' ? $dir.'/' : '').'variants/'.pathinfo($path, PATHINFO_FILENAME).'-'.$width.'.webp';
    }

    /**
     * نسخه‌های موجودِ یک عکس، از کوچک به بزرگ: [پهنا => مسیر].
     *
     * @return array<int, string>
     */
    public static function existing(?string $path, string $disk = 'public'): array
    {
        if (! static::isRaster($path)) {
            return [];
        }

        return static::$memo[$disk.':'.$path] ??= collect(self::WIDTHS)
            ->mapWithKeys(fn (int $w) => [$w => static::pathFor($path, $w)])
            ->filter(fn (string $variant) => Storage::disk($disk)->exists($variant))
            ->all();
    }

    /**
     * می‌سازد، اگر نیست. برمی‌گرداند چند نسخه‌ی تازه ساخته شد.
     *
     * عکسِ کوچک‌تر از یک پهنا، برای آن پهنا بزرگ نمی‌شود؛ ولی کوچک‌ترین نسخه
     * همیشه ساخته می‌شود تا حتی عکسِ کوچک هم WebP داشته باشد.
     */
    public static function generate(string $path, string $disk = 'public'): int
    {
        if (! static::isRaster($path) || ! function_exists('imagewebp')) {
            return 0;
        }

        $storage = Storage::disk($disk);
        $source = $storage->path($path);

        if (! is_file($source)) {
            return 0;
        }

        $todo = array_filter(self::WIDTHS, fn (int $w) => ! $storage->exists(static::pathFor($path, $w)));
        if (! $todo) {
            return 0;
        }

        $limit = ini_get('memory_limit');
        @ini_set('memory_limit', '768M');

        try {
            $image = static::load($source);
            if (! $image) {
                return 0;
            }

            $originalWidth = imagesx($image);
            $made = 0;

            foreach (self::WIDTHS as $i => $width) {
                if (! in_array($width, $todo, true)) {
                    continue;
                }
                // بزرگ‌تر از اصل ساخته نمی‌شود، جز نخستین پهنایی که از اصل بیشتر است —
                // آن یکی به اندازه‌ی خودِ اصل، تا عکسِ ۱۰۰۰ پیکسلی روی دسکتاپ
                // با نسخه‌ی ۷۶۸ پیکسلی جایگزین نشود.
                $previous = $i > 0 ? self::WIDTHS[$i - 1] : 0;
                if ($width > $originalWidth && $previous >= $originalWidth) {
                    continue;
                }

                $target = min($width, $originalWidth);
                $height = (int) round(imagesy($image) * $target / $originalWidth);

                $resized = imagecreatetruecolor($target, $height);
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $target, $height, $originalWidth, imagesy($image));

                $variant = static::pathFor($path, $width);
                $storage->makeDirectory(dirname($variant));
                imagewebp($resized, $storage->path($variant), self::QUALITY);
                imagedestroy($resized);
                $made++;
            }

            imagedestroy($image);
            unset(static::$memo[$disk.':'.$path]);

            return $made;
        } catch (Throwable $e) {
            report($e);

            return 0;
        } finally {
            @ini_set('memory_limit', $limit);
        }
    }

    public static function purge(?string $path, string $disk = 'public'): void
    {
        if (! static::isRaster($path)) {
            return;
        }

        foreach (self::WIDTHS as $width) {
            Storage::disk($disk)->delete(static::pathFor($path, $width));
        }

        unset(static::$memo[$disk.':'.$path]);
    }

    /**
     * بارکردنِ عکس، با چرخشِ EXIF.
     *
     * عکسِ گوشی اغلب افقی ذخیره می‌شود و فقط برچسبِ «۹۰ درجه بچرخان» دارد؛
     * GD آن برچسب را نمی‌خواند و نسخه‌ها کج درمی‌آمدند.
     */
    protected static function load(string $file): ?\GdImage
    {
        $image = match (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($file),
            'png' => @imagecreatefrompng($file),
            'webp' => @imagecreatefromwebp($file),
            default => false,
        };

        if (! $image) {
            return null;
        }

        if (function_exists('exif_read_data') && preg_match('~\.jpe?g$~i', $file)) {
            $orientation = (int) (@exif_read_data($file)['Orientation'] ?? 1);
            $image = match ($orientation) {
                3 => imagerotate($image, 180, 0),
                6 => imagerotate($image, -90, 0),
                8 => imagerotate($image, 90, 0),
                default => $image,
            };
        }

        imagepalettetotruecolor($image);

        return $image;
    }
}
