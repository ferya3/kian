<?php

namespace App\Admin\Resources;

use App\Models\Order;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;

class OrderResource extends Resource
{
    public static string $model = Order::class;

    public static string $slug = 'orders';

    public static string $label = 'سفارش‌ها';

    public static string $singular = 'سفارش';

    public static string $icon = 'file';

    public static string $group = 'فروشگاه';

    /*
     * سفارش از سایت می‌آید و در پنل ساخته نمی‌شود. حذفش هم نه: سابقه‌ی فروش
     * سند است و وضعیت «لغوشده» برای همین هست.
     */
    public static bool $creatable = false;

    public static bool $deletable = false;

    public static string $orderBy = 'created_at';

    public static function searchable(): array
    {
        return ['number', 'customer_name', 'customer_phone'];
    }

    public static function fields(): array
    {
        return [
            Field::readonly('number', 'شماره')->inList(true),
            Field::readonly('customer_name', 'مشتری')->inList(),
            Field::readonly('customer_phone', 'تلفن')->inList(),
            Field::readonly('total', 'مبلغ کل')->inList(),

            Field::select('status', 'وضعیت', config('shop.statuses'))
                ->rules(['required', 'in:'.implode(',', array_keys(config('shop.statuses')))])
                ->inList()
                ->half(),

            Field::readonly('province', 'استان'),
            Field::readonly('city', 'شهر'),
            Field::readonly('address', 'نشانی'),
            Field::readonly('note', 'توضیح مشتری'),
        ];
    }
}
