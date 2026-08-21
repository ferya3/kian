<?php

namespace App\Admin\Resources;

use App\Models\Stat;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;

class StatResource extends Resource
{
    public static string $model = Stat::class;

    public static string $slug = 'stats';

    public static string $label = 'شاخص‌های عددی';

    public static string $singular = 'شاخص';

    public static string $icon = 'sparkle';

    public static string $group = 'کارخانه';

    public static string $orderBy = 'position';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['label', 'description'];
    }

    public static function fields(): array
    {
        return [
            Field::text('label', 'برچسب')->rules(['required'])->inList(true)->half(),
            Field::select('group', 'محل نمایش', [
                'factory' => 'نوار زیر هیرو و صفحه کارخانه',
                'sustainability' => 'صفحه پایداری',
            ])->rules(['required'])->inList()->half(),
            Field::decimal('value', 'عدد')->rules(['required', 'min:0'])->inList()->third(),
            Field::text('suffix', 'پسوند')->rules(['nullable', 'max:20'])
                ->hint('مثلاً: ٪ یا « تن» یا +')->third(),
            Field::number('decimals', 'رقم اعشار')->rules(['nullable', 'min:0', 'max:3'])->third(),
            Field::text('description', 'توضیح کوتاه')->rules(['nullable', 'max:200']),
            Field::number('position', 'ترتیب')->rules(['nullable', 'min:0', 'max:999'])->half(),
        ];
    }
}
