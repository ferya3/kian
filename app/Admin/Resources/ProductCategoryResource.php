<?php

namespace App\Admin\Resources;

use App\Models\ProductCategory;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;

class ProductCategoryResource extends Resource
{
    public static string $model = ProductCategory::class;

    public static string $slug = 'product-categories';

    public static string $label = 'دسته‌بندی محصولات';

    public static string $singular = 'دسته‌بندی';

    public static string $icon = 'grid';

    public static string $group = 'کاتالوگ';

    public static string $orderBy = 'position';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['name', 'name_en'];
    }

    public static function with(): array
    {
        return ['parent'];
    }

    public static function fields(): array
    {
        return [
            Field::text('name', 'نام دسته')->rules(['required'])->inList(true)->half(),
            Field::text('name_en', 'نام انگلیسی')->rules(['nullable'])->half(),
            Field::slug('slug')->rules(['required'])->half(),
            Field::relation('parent_id', 'زیرمجموعه‌ی', ProductCategory::class)
                ->rules(['nullable', 'exists:product_categories,id'])
                ->hint('برای دسته‌ی اصلی خالی بگذارید.')->inList()->half(),
            Field::text('tagline', 'شعار کوتاه')->rules(['nullable']),
            Field::textarea('description', 'توضیح')->rules(['nullable', 'max:800']),
            Field::number('position', 'ترتیب')->rules(['nullable', 'min:0', 'max:999'])->half(),
            Field::boolean('is_featured', 'نمایش در مِگا منو')->half(),
        ];
    }
}
