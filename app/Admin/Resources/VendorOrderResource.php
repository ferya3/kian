<?php

namespace App\Admin\Resources;

use App\Models\OrderItem;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;
use Illuminate\Database\Eloquent\Builder;

/**
 * سفارش‌های فروشنده — ردیف‌های او، نه سفارش‌های کامل.
 *
 * عمداً روی order_items سوار است و نه روی orders. یک سفارش می‌تواند ردیفِ
 * چند فروشنده داشته باشد؛ اگر فروشنده «سفارش» را می‌دید، ردیف و مبلغِ
 * رقیبش را هم می‌دید. اینجا هر فروشنده فقط ردیف‌های خودش را دارد، به‌اضافه‌ی
 * آنچه برای تماس لازم است.
 */
class VendorOrderResource extends Resource
{
    public static string $model = OrderItem::class;

    public static string $slug = 'sales';

    public static string $label = 'سفارش‌های من';

    public static string $singular = 'ردیف سفارش';

    public static string $icon = 'file';

    public static string $group = 'فروشگاه';

    /** فقط فروشنده. مدیر کل همه‌ی سفارش‌ها را در منبع «سفارش‌ها» می‌بیند. */
    public static array $roles = ['vendor'];

    public static bool $creatable = false;

    public static bool $deletable = false;

    public static string $orderBy = 'created_at';

    public static function searchable(): array
    {
        return ['product_name'];
    }

    public static function with(): array
    {
        return ['order'];
    }

    public static function query(): Builder
    {
        $query = parent::query();

        /*
         * بدون فروشگاه، هیچ ردیفی.
         *
         * whereRaw('0=1') به‌جای نادیده‌گرفتنِ شرط: اگر روزی کاربری با نقش
         * فروشنده و بدون vendor_id ساخته شود، نباید سفارش‌های همه را ببیند.
         */
        $vendorId = auth()->user()?->vendor_id;

        return $vendorId
            ? $query->where('vendor_id', $vendorId)
            : $query->whereRaw('1 = 0');
    }

    public static function fields(): array
    {
        return [
            Field::readonly('order_number', 'شماره سفارش')->inList(true),
            Field::readonly('product_name', 'محصول')->inList(),
            Field::readonly('quantity', 'تعداد')->inList(),
            Field::readonly('unit_price', 'قیمت واحد'),
            Field::readonly('total', 'جمع ردیف')->inList(),

            Field::select('status', 'وضعیت', config('shop.statuses'))
                ->rules(['required', 'in:'.implode(',', array_keys(config('shop.statuses')))])
                ->inList()
                ->half(),

            Field::readonly('customer_name', 'مشتری')->inList(),
            Field::readonly('customer_phone', 'تلفن مشتری')->inList(),
            Field::readonly('customer_address', 'نشانی تحویل'),
        ];
    }
}
