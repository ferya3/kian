<?php

namespace App\Models;

use App\Support\HasTranslations;
use App\Support\Locales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasTranslations;

    /** متنِ تنظیم دیده می‌شود، پس ترجمه می‌شود. کلید و گروه نه — آن‌ها کدند. */
    public array $translatable = ['value'];

    protected $guarded = [];

    /**
     * تمام تنظیمات به شکل key => value، با کش دائمی — یکی برای هر زبان.
     *
     * پیش‌تر اینجا pluck('value', 'key') بود: کوئری خام، که از accessorِ
     * HasTranslations رد می‌شود. یعنی تنظیمات ترجمه‌پذیر بودند و ترجمه‌شان
     * هیچ‌وقت دیده نمی‌شد. حالا مدل ساخته می‌شود تا ترجمه روی مقدار بنشیند.
     */
    public static function map(): array
    {
        return Cache::rememberForever(
            static::cacheKey(),
            fn () => static::query()->get()->mapWithKeys(
                fn (self $setting) => [$setting->key => $setting->value]
            )->all()
        );
    }

    /**
     * مقدار یک تنظیم.
     * نام get عمداً استفاده نشده تا با Model::get() فوروارد‌شده به Query Builder
     * تداخل پیدا نکند.
     */
    public static function text(string $key, mixed $default = null): mixed
    {
        $value = static::map()[$key] ?? null;

        // ترجمه‌ی خالی یعنی «ندارم»، نه «خالی باشد»
        return blank($value) ? $default : $value;
    }

    public static function put(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
    }

    protected static function cacheKey(?string $locale = null): string
    {
        return 'settings.map.'.($locale ?? Locales::current());
    }

    /**
     * کشِ هر زبان جداگانه پاک می‌شود.
     *
     * ذخیره‌ی یک تنظیم در فارسی، نقشه‌ی انگلیسی را هم بی‌اعتبار می‌کند: هر
     * کلیدِ ترجمه‌نشده مقدار فارسی را نشان می‌دهد.
     */
    public static function forget(): void
    {
        foreach (Locales::declaredCodes() as $code) {
            Cache::forget(static::cacheKey($code));
        }
    }

    protected function afterTranslationsSaved(): void
    {
        static::forget();
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::forget());
        static::deleted(fn () => static::forget());
    }
}
