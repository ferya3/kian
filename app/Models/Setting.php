<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $guarded = [];

    /** تمام تنظیمات به شکل key => value، با کش دائمی. */
    public static function map(): array
    {
        return Cache::rememberForever(
            'settings.map',
            fn () => static::query()->pluck('value', 'key')->all()
        );
    }

    /**
     * مقدار یک تنظیم.
     * نام get عمداً استفاده نشده تا با Model::get() فوروارد‌شده به Query Builder
     * تداخل پیدا نکند.
     */
    public static function text(string $key, mixed $default = null): mixed
    {
        return static::map()[$key] ?? $default;
    }

    public static function put(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings.map'));
        static::deleted(fn () => Cache::forget('settings.map'));
    }
}
