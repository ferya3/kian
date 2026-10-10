<?php

namespace App\Admin\Resources;

use App\Models\Milestone;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;

/** خطِ زمانِ صفحه‌ی بیوگرافی. */
class MilestoneResource extends Resource
{
    public static string $model = Milestone::class;

    public static string $slug = 'milestones';

    public static string $label = 'بیوگرافی — خط زمان';

    public static string $singular = 'رویداد';

    public static string $icon = 'clock';

    public static string $group = 'درباره ما';

    public static string $orderBy = 'year';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['title', 'text'];
    }

    public static function fields(): array
    {
        return [
            Field::number('year', 'سال (شمسی)')->rules(['nullable', 'integer', 'min:1300', 'max:1500'])
                ->hint('مثل ۱۳۹۲. خالی بماند یعنی «امروز» — همیشه آخرِ خط می‌نشیند. صفحه‌ی انگلیسی خودش به میلادی برمی‌گرداند.')
                ->inList(true)->half(),
            Field::number('position', 'ترتیب در همان سال')->rules(['nullable', 'min:0', 'max:999'])
                ->hint('فقط وقتی دو رویداد یک سال دارند لازم است؛ خط به ترتیبِ سال چیده می‌شود.')
                ->half(),
            Field::text('title', 'عنوان')->rules(['required', 'max:160'])->inList(),
            Field::textarea('text', 'شرح')->rules(['nullable', 'max:1200']),
            Field::image('image', 'تصویر (اختیاری)', 'biography')->rules(['nullable'])->half(),
            Field::boolean('is_active', 'نمایش در سایت')->default(true)->inList()->half(),
        ];
    }
}
