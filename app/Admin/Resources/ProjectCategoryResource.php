<?php

namespace App\Admin\Resources;

use App\Models\ProjectCategory;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;

class ProjectCategoryResource extends Resource
{
    public static string $model = ProjectCategory::class;

    public static string $slug = 'project-categories';

    public static string $label = 'دسته‌بندی پروژه‌ها';

    public static string $singular = 'دسته‌بندی پروژه';

    public static string $icon = 'grid';

    public static string $group = 'کاتالوگ';

    public static string $orderBy = 'position';

    public static string $orderDir = 'asc';

    public static function fields(): array
    {
        return [
            Field::text('name', 'نام')->rules(['required'])->inList(true)->half(),
            Field::text('name_en', 'نام انگلیسی')->rules(['nullable'])->half(),
            Field::slug('slug')->rules(['required'])->half(),
            Field::number('position', 'ترتیب')->rules(['nullable', 'min:0', 'max:999'])->half(),
        ];
    }
}
