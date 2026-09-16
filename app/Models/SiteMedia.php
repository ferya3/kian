<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * تصویرهای ثابت سایت — هیرو، لوگو، تصویر اشتراک‌گذاری.
 *
 * برخلاف تصویر محصول یا پروژه که به یک رکورد چسبیده، این‌ها به یک *جایگاه* در
 * طراحی چسبیده‌اند. فهرست جایگاه‌ها در SLOTS است و منبع حقیقت همان‌جاست: هر
 * جایگاه یعنی یک ویو که آن کلید را می‌خواند. به همین دلیل پنل اجازه‌ی ساخت
 * جایگاه تازه نمی‌دهد — جایگاهی که هیچ ویویی نخواندش، همان ستون مرده‌ای است
 * که پیش‌تر داشتیم.
 *
 * نبودِ ردیف هیچ‌وقت خطا نیست: url() مقدار null برمی‌گرداند و ویو به طرح
 * وکتوری خودش برمی‌گردد.
 */
class SiteMedia extends Model
{
    protected $table = 'site_media';

    protected $guarded = [];

    /**
     * جایگاه => [برچسب، بخش، کجا دیده می‌شود].
     *
     * کلیدِ هیروی هر صفحه دقیقاً «hero.» به‌اضافه‌ی نام مسیر همان صفحه است، تا
     * x-page-hero بتواند بدون هیچ تنظیمی در ویو، جایگاه خودش را پیدا کند.
     */
    public const SLOTS = [
        // ------------------------------------------------------ صفحه اصلی --
        'hero.home' => [
            'تصویر هیرو صفحه اصلی',
            'صفحه اصلی',
            'پس‌زمینه‌ی بالای صفحه‌ی اول. اگر ویدئوی هیرو تنظیم شده باشد، ویدئو مقدم است.',
        ],
        'hero.home_poster' => [
            'پوستر ویدئوی هیرو',
            'صفحه اصلی',
            'تا لحظه‌ی آماده‌شدن ویدئو همین تصویر دیده می‌شود. خالی بگذارید تا از تصویر هیرو استفاده شود.',
        ],

        // -------------------------------------------------- هیروی صفحات --
        'hero.products.index' => ['هیرو فهرست محصولات', 'هیروی صفحات', 'بالای صفحه‌ی «محصولات»'],
        'hero.solutions.index' => ['هیرو راهکارها', 'هیروی صفحات', 'بالای صفحه‌ی «راهکارها»'],
        'hero.projects.index' => ['هیرو پروژه‌ها', 'هیروی صفحات', 'بالای صفحه‌ی «پروژه‌ها»'],
        'hero.articles.index' => ['هیرو مقالات', 'هیروی صفحات', 'بالای صفحه‌ی «دانش فنی»'],
        'hero.finder.show' => ['هیرو انتخاب محصول', 'هیروی صفحات', 'بالای صفحه‌ی «موتور انتخاب محصول»'],
        'hero.technical.index' => ['هیرو مرکز فنی', 'هیروی صفحات', 'بالای صفحه‌ی «مرکز فنی»'],
        'hero.technical.downloads' => ['هیرو دانلودها', 'هیروی صفحات', 'بالای صفحه‌ی «دیتاشیت و فایل‌های فنی»'],
        'hero.technical.installation' => ['هیرو راهنمای اجرا', 'هیروی صفحات', 'بالای صفحه‌ی «راهنمای اجرا»'],
        'hero.technical.certificates' => ['هیرو گواهی‌نامه‌ها', 'هیروی صفحات', 'بالای صفحه‌ی «گواهی‌نامه‌ها»'],
        'hero.technical.faq' => ['هیرو پرسش‌های متداول', 'هیروی صفحات', 'بالای صفحه‌ی «پرسش‌های متداول»'],
        'hero.technology' => ['هیرو فناوری', 'هیروی صفحات', 'بالای صفحه‌ی «فناوری»'],
        'hero.factory' => ['هیرو کارخانه', 'هیروی صفحات', 'بالای صفحه‌ی «کارخانه»'],
        'hero.sustainability' => ['هیرو پایداری', 'هیروی صفحات', 'بالای صفحه‌ی «پایداری»'],
        'hero.about' => ['هیرو درباره ما', 'هیروی صفحات', 'بالای صفحه‌ی «درباره ما»'],
        'hero.distributors' => ['هیرو نمایندگان', 'هیروی صفحات', 'بالای صفحه‌ی «نمایندگان»'],
        'hero.contact' => ['هیرو تماس', 'هیروی صفحات', 'بالای صفحه‌ی «تماس با ما»'],

        // ----------------------------------------------------------- برند --
        'brand.logo' => [
            'لوگو',
            'برند',
            'هدر، فوتر، منوی موبایل و پنل مدیریت. خالی بگذارید تا نشانه‌ی وکتوری پیش‌فرض بماند.',
        ],
        'brand.og' => [
            'تصویر اشتراک‌گذاری',
            'برند',
            'وقتی نشانی سایت در پیام‌رسان یا شبکه‌ی اجتماعی فرستاده می‌شود. نسبت ۱۲۰۰×۶۳۰.',
        ],
        'brand.favicon' => [
            'فاوآیکون',
            'برند',
            'نشانه‌ی کوچک کنار عنوان صفحه در تب مرورگر. مربع و دست‌کم ۵۱۲ پیکسل.',
        ],
    ];

    /** @return array<string, array{image: ?string, alt: ?string}> */
    public static function map(): array
    {
        return Cache::rememberForever('site_media.map', fn () => static::query()
            ->get(['key', 'image', 'alt'])
            ->mapWithKeys(fn (self $row) => [$row->key => ['image' => $row->image, 'alt' => $row->alt]])
            ->all());
    }

    public static function url(?string $key): ?string
    {
        return Media::url(static::map()[$key]['image'] ?? null);
    }

    public static function alt(?string $key, string $default = ''): string
    {
        $alt = static::map()[$key]['alt'] ?? null;

        return filled($alt) ? $alt : $default;
    }

    public static function has(?string $key): bool
    {
        return static::url($key) !== null;
    }

    /**
     * ردیف هر جایگاهِ تعریف‌شده را می‌سازد.
     *
     * جایگاه‌ها با گذر زمان اضافه می‌شوند و مسیر به‌روزرسانی فقط مهاجرت‌ها را
     * اجرا می‌کند، نه seed را. پس به‌جای یک مهاجرت تازه به‌ازای هر جایگاه، پنل
     * خودش هر بار کم‌وکسری را پر می‌کند. ردیف‌های ناشناخته دست‌نخورده می‌مانند.
     */
    public static function sync(): void
    {
        $existing = static::query()->pluck('key')->all();

        $missing = collect(array_keys(static::SLOTS))
            ->reject(fn (string $key) => in_array($key, $existing, true))
            ->map(fn (string $key) => ['key' => $key, 'created_at' => now(), 'updated_at' => now()])
            ->all();

        if ($missing) {
            static::query()->insert($missing);
            Cache::forget('site_media.map');
        }
    }

    public function getLabelAttribute(): string
    {
        return static::SLOTS[$this->key][0] ?? $this->key;
    }

    public function getSectionAttribute(): string
    {
        return static::SLOTS[$this->key][1] ?? 'سایر';
    }

    public function getPlacementAttribute(): string
    {
        return static::SLOTS[$this->key][2] ?? '—';
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('site_media.map'));
        static::deleted(fn () => Cache::forget('site_media.map'));
    }
}
