<?php

namespace App\Admin\Resources;

use App\Models\Setting;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;
use Illuminate\Database\Eloquent\Model;

class SettingResource extends Resource
{
    public static string $model = Setting::class;

    public static string $slug = 'settings';

    public static string $label = 'تنظیمات محتوا';

    public static string $singular = 'تنظیم';

    public static string $icon = 'sparkle';

    public static string $group = 'سیستم';

    public static bool $adminOnly = true;

    public static string $orderBy = 'key';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['key', 'value'];
    }

    public static function titleFor(Model $record): string
    {
        return self::KEY_LABELS[$record->key] ?? $record->key;
    }

    /** کلیدهایی که سایت واقعاً می‌خواند. */
    public const KEY_LABELS = [
        'hero_title' => 'تیتر هیرو',
        'hero_subtitle' => 'زیرتیتر هیرو',
        'hero_eyebrow' => 'برچسب بالای هیرو',
        'hero_video' => 'مسیر ویدئوی هیرو',
        'about_lead' => 'متن ابتدای صفحه درباره ما',
    ];

    public static function fields(): array
    {
        return [
            Field::text('key', 'کلید')->rules(['required', 'max:80', 'regex:/^[a-z0-9_]+$/'])
                ->hint('کلیدهای شناخته‌شده: '.implode('، ', array_keys(self::KEY_LABELS)))
                ->onlyOnCreate()->inList(true)->half(),
            Field::readonly('key', 'کلید')->onlyOnEdit(),
            Field::text('group', 'گروه')->rules(['nullable', 'max:40'])->default('content')->half(),
            Field::textarea('value', 'مقدار')->rules(['nullable', 'max:2000'])->inList(),
        ];
    }
}
