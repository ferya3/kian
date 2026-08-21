<?php

namespace App\Admin\Resources;

use App\Models\Distributor;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;

class DistributorResource extends Resource
{
    public static string $model = Distributor::class;

    public static string $slug = 'distributors';

    public static string $label = 'نمایندگان فروش';

    public static string $singular = 'نماینده';

    public static string $icon = 'pin';

    public static string $group = 'فروش';

    public static string $orderBy = 'position';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['name', 'manager', 'province', 'city'];
    }

    public static function fields(): array
    {
        return [
            Field::text('name', 'نام نمایندگی')->rules(['required'])->inList(true)->half(),
            Field::text('manager', 'مدیر')->rules(['nullable'])->half(),
            Field::text('province', 'استان')->rules(['required'])->inList(true)->half(),
            Field::text('city', 'شهر')->rules(['required'])->inList()->half(),
            Field::textarea('address', 'آدرس')->rules(['nullable', 'max:400']),
            Field::text('phone', 'تلفن ثابت')->rules(['nullable', 'max:30'])->inList()->half(),
            Field::text('mobile', 'همراه')->rules(['nullable', 'max:30'])->half(),
            Field::number('position', 'ترتیب')->rules(['nullable', 'min:0', 'max:999'])->half(),
        ];
    }
}
