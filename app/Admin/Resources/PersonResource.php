<?php

namespace App\Admin\Resources;

use App\Models\Person;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;

/** بنیان‌گذاران و مدیران — صفحه‌ی بیوگرافی. */
class PersonResource extends Resource
{
    public static string $model = Person::class;

    public static string $slug = 'people';

    public static string $label = 'بیوگرافی — مدیران';

    public static string $singular = 'شخص';

    public static string $icon = 'compass';

    public static string $group = 'درباره ما';

    public static string $orderBy = 'position';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['name', 'role', 'bio'];
    }

    public static function fields(): array
    {
        return [
            Field::text('name', 'نام و نام خانوادگی')->rules(['required', 'max:120'])->inList(true)->half(),
            Field::text('role', 'سمت')->rules(['nullable', 'max:120'])
                ->hint('مثل: بنیان‌گذار و مدیرعامل')->inList()->half(),
            Field::textarea('bio', 'زندگی‌نامه')->rules(['nullable', 'max:4000'])
                ->hint('هر پاراگراف را در یک خط جدا بنویسید.'),
            Field::image('photo', 'عکس', 'people')->rules(['nullable'])
                ->hint('بی عکس، حرفِ نخستِ نام نشان داده می‌شود.')->half(),
            Field::number('position', 'ترتیب')->rules(['nullable', 'min:0', 'max:999'])->half(),
            Field::boolean('is_active', 'نمایش در سایت')->default(true)->inList()->half(),
        ];
    }
}
