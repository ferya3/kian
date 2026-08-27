<?php

namespace App\Admin\Resources;

use App\Models\FactorySection;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;

class FactorySectionResource extends Resource
{
    public static string $model = FactorySection::class;

    public static string $slug = 'factory-sections';

    public static string $label = 'بخش‌های کارخانه';

    public static string $singular = 'بخش کارخانه';

    public static string $icon = 'pin';

    public static string $group = 'کارخانه';

    public static string $orderBy = 'position';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['title', 'title_en'];
    }

    public static function fields(): array
    {
        return [
            Field::text('title', 'عنوان')->rules(['required'])->inList(true)->half(),
            Field::text('title_en', 'عنوان انگلیسی')->rules(['nullable'])->half(),
            Field::slug('slug')->rules(['required'])->half(),
            Field::image('image', 'تصویر بخش', 'factory')->rules(['nullable'])->half(),
            Field::number('position', 'ترتیب')->rules(['nullable', 'min:0', 'max:999'])->half(),
            Field::textarea('description', 'توضیح')->rules(['required', 'max:800']),
            Field::decimal('hotspot_x', 'موقعیت افقی نقطه')->rules(['required', 'min:0', 'max:100'])
                ->suffix('٪ از چپ')->hint('روی نقشه‌ی ایزومتریک کارخانه.')->inList()->half(),
            Field::decimal('hotspot_y', 'موقعیت عمودی نقطه')->rules(['required', 'min:0', 'max:100'])
                ->suffix('٪ از بالا')->inList()->half(),
            Field::lines('stats', 'شاخص‌های این بخش')
                ->hint('مثلاً: ۱۱۰ متر طول — هر مورد یک خط.'),
        ];
    }
}
