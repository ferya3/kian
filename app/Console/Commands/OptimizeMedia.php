<?php

namespace App\Console\Commands;

use App\Support\ImageVariants;
use App\Support\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * ساختِ نسخه‌های سبکِ WebP برای عکس‌هایی که پیش‌تر آپلود شده‌اند.
 *
 * آپلودهای تازه خودشان نسخه می‌گیرند؛ این برای آنچه پیش از این تغییر روی
 * سرور بوده. هر بار اجرا شود فقط کم‌وکسری را می‌سازد، پس نصب‌کننده هر بار
 * صدایش می‌زند.
 */
class OptimizeMedia extends Command
{
    protected $signature = 'media:optimize';

    protected $description = 'ساختِ نسخه‌های WebP در چند پهنا برای عکس‌های آپلودشده';

    public function handle(): int
    {
        $disk = Storage::disk(Media::$disk);

        if (! $disk->exists('admin')) {
            $this->line('عکسی برای بهینه‌سازی نیست.');

            return self::SUCCESS;
        }

        $images = collect($disk->allFiles('admin'))->filter(fn ($p) => ImageVariants::isRaster($p))->values();
        $made = 0;
        $before = 0;
        $after = 0;

        foreach ($images as $path) {
            $made += ImageVariants::generate($path, Media::$disk);

            if ($variants = ImageVariants::existing($path, Media::$disk)) {
                $before += $disk->size($path);
                $after += $disk->size(end($variants));
            }
        }

        // نسخه‌های نسلِ پیشین یا عکس‌های حذف‌شده
        $stale = ImageVariants::stale(Media::$disk);
        $disk->delete($stale);

        $this->info(sprintf(
            '%d عکس، %d نسخه‌ی تازه، %d نسخه‌ی کهنه پاک شد. حجمِ ارسالی: %s ⇐ %s',
            $images->count(), $made, count($stale), $this->mb($before), $this->mb($after)
        ));

        return self::SUCCESS;
    }

    protected function mb(int $bytes): string
    {
        return number_format($bytes / 1048576, 1).' MB';
    }
}
