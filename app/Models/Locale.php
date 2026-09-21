<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * وضعیتِ روشن/خاموشِ زبان‌ها.
 *
 * تقسیم کار با config/locales.php عمدی است:
 *   • تنظیمات می‌گوید چه زبان‌هایی *وجود دارند* و مشخصاتشان چیست — این کد
 *     است، چون هر زبان به پوشه‌ی lang و ترجمه‌ی محتوا نیاز دارد؛
 *   • این جدول می‌گوید کدام‌ها *همین حالا روی سایت‌اند* — این تصمیم مدیر است
 *     و باید بدون استقرار عوض شود.
 *
 * هیچ‌جای سایت این مدل را مستقیم صدا نمی‌زند؛ همه از App\Support\Locales
 * می‌پرسند، که این را روی تنظیمات سوار می‌کند.
 */
class Locale extends Model
{
    public const CACHE_KEY = 'locales.state';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public $timestamps = true;

    /**
     * حافظه‌ی همین درخواست.
     *
     * کش دائمی جلوی کوئری را می‌گیرد ولی نه جلوی *خواندنِ کش* را، و درایور
     * پیش‌فرضِ کش خودش دیتابیس است. Locales در یک صفحه ده‌ها بار پرسیده
     * می‌شود — جهت، قلم، hreflang، سوئیچر — و بی این خط، هرکدام یک رفت‌وبرگشت
     * می‌شد. false یعنی «هنوز نپرسیده‌ایم»، چون null خودش پاسخی معنادار است.
     */
    protected static array|null|false $memo = false;

    /**
     * code => ['is_active' => bool, 'order' => int]، با کش دائمی.
     *
     * @return array<string, array{is_active: bool, order: int}>
     */
    public static function map(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::query()
                ->orderBy('order')
                ->orderBy('id')
                ->get()
                ->mapWithKeys(fn (self $row) => [
                    $row->code => ['is_active' => $row->is_active, 'order' => $row->order],
                ])
                ->all();
        });
    }

    /**
     * همان نقشه، ولی بی‌خطر پیش از مهاجرت.
     *
     * Locales در هر درخواست خوانده می‌شود — از جمله درخواستی که هنوز جدولی
     * ندارد: نصبِ تازه، یا استقراری که مهاجرت‌هایش نیمه مانده. سایت آن لحظه
     * نباید سفید شود؛ null یعنی «نمی‌دانم» و Locales به کلید enabledِ خودِ
     * تنظیمات برمی‌گردد.
     *
     * جدولِ خالی هم null است و نه آرایه‌ی خالی: صفرتا زبان یعنی صفرتا صفحه.
     *
     * @return array<string, array{is_active: bool, order: int}>|null
     */
    public static function mapOrNull(): ?array
    {
        if (static::$memo !== false) {
            return static::$memo;
        }

        try {
            $map = static::map();
        } catch (Throwable) {
            /*
             * عمداً بدون Schema::hasTable.
             *
             * hasTable خودش یک کوئری است و در *هر* درخواستِ سالم اجرا می‌شد،
             * فقط برای احتمالی که یک‌بار در عمر سایت پیش می‌آید. اینجا مسیرِ
             * سالم هیچ هزینه‌ای نمی‌دهد و مسیرِ نادر هزینه‌ی یک کوئریِ شکسته.
             */
            return static::$memo = null;
        }

        return static::$memo = ($map === [] ? null : $map);
    }

    /** برای تست‌ها و برای لحظه‌ی درست پس از ذخیره. */
    public static function forget(): void
    {
        static::$memo = false;
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * ساختِ ردیفِ هر زبانی که در تنظیمات هست و اینجا نیست.
     *
     * زبان با افزوده‌شدن یک مدخل به config به سایت اضافه می‌شود، ولی مسیر
     * به‌روزرسانی فقط مهاجرت‌ها را اجرا می‌کند. بدون این، زبان تازه در پنل
     * دیده نمی‌شد. همان الگوی SiteMedia::sync.
     */
    public static function sync(): void
    {
        $declared = array_keys(config('locales.available', []));

        if ($declared === []) {
            return;
        }

        $existing = static::query()->pluck('code')->all();
        $next = (int) static::query()->max('order');

        $missing = [];

        foreach ($declared as $code) {
            if (in_array($code, $existing, true)) {
                continue;
            }

            $missing[] = [
                'code' => $code,
                /*
                 * زبانِ تازه خاموش متولد می‌شود.
                 *
                 * مدخلی که با به‌روزرسانی رسیده هنوز ترجمه‌ی محتوا ندارد؛
                 * روشن‌کردنش یعنی انتشارِ صفحه‌ای که همه‌اش به فارسی برگشته.
                 * استثنا زبان پیش‌فرض است که بی‌آن سایتی نیست.
                 */
                'is_active' => $code === config('locales.default'),
                'order' => $next += 10,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($missing) {
            static::query()->insert($missing);
            static::forget();
        }
    }

    /** نام خوانا — از تنظیمات، نه از جدول. */
    public function getNameAttribute(): string
    {
        return config("locales.available.{$this->code}.name", $this->code);
    }

    public function getEnglishAttribute(): string
    {
        return config("locales.available.{$this->code}.english", $this->code);
    }

    public function getDirectionAttribute(): string
    {
        return config("locales.available.{$this->code}.dir") === 'ltr'
            ? 'چپ‌به‌راست'
            : 'راست‌به‌چپ';
    }

    /** زبان پیش‌فرض نه خاموش می‌شود و نه حذف. */
    public function getIsDefaultAttribute(): bool
    {
        return $this->code === config('locales.default');
    }

    /** جای تیکِ روشن/خاموش، وقتی تیک معنایی ندارد. */
    public function getActiveStateAttribute(): string
    {
        return 'روشن — زبان پیش‌فرض سایت است و خاموش نمی‌شود.';
    }

    protected static function booted(): void
    {
        static::saving(function (self $locale) {
            // آخرین سد در برابر خاموش‌شدنِ زبانی که کل سایت رویش نشسته
            if ($locale->is_default) {
                $locale->is_active = true;
            }
        });

        static::saved(fn () => static::forget());
        static::deleted(fn () => static::forget());
    }
}
