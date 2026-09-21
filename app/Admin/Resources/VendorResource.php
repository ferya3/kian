<?php

namespace App\Admin\Resources;

use App\Models\Distributor;
use App\Models\Vendor;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;

class VendorResource extends Resource
{
    public static string $model = Vendor::class;

    public static string $slug = 'vendors';

    public static string $label = 'فروشندگان';

    public static string $singular = 'فروشنده';

    public static string $icon = 'grid';

    public static string $group = 'فروشگاه';

    /** ساختنِ فروشنده یعنی دادنِ دسترسی فروش — کارِ مدیر کل است. */
    public static bool $adminOnly = true;

    public static string $orderBy = 'position';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['name', 'city', 'province'];
    }

    public static function with(): array
    {
        return ['distributor'];
    }

    public static function fields(): array
    {
        return [
            Field::text('name', 'نام فروشنده')->rules(['required', 'max:120'])->inList(true)->half(),
            Field::slug('slug')->rules(['required', 'max:120'])->half(),

            Field::relation('distributor_id', 'نمایندگی مرتبط (اختیاری)', Distributor::class)
                ->rules(['nullable', 'exists:distributors,id'])
                ->half(),

            Field::text('phone', 'تلفن')->rules(['nullable', 'max:30'])->half(),
            Field::text('province', 'استان')->rules(['nullable', 'max:60'])->inList()->half(),
            Field::text('city', 'شهر')->rules(['nullable', 'max:60'])->inList()->half(),
            Field::textarea('about', 'درباره')->rules(['nullable', 'max:600']),
            Field::boolean('is_active', 'فعال')->inList()->half(),
            Field::number('position', 'ترتیب')->rules(['nullable', 'min:0', 'max:999'])->half(),
        ];
    }
}
