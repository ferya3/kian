<?php

namespace App\Admin\Resources;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Vendor;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * عرضه‌ها — تنها جایی که فروشنده در پنل کار دارد.
 *
 * مدیر کل همه‌ی عرضه‌ها را می‌بیند؛ فروشنده فقط عرضه‌های فروشگاه خودش را.
 */
class OfferResource extends Resource
{
    public static string $model = Offer::class;

    public static string $slug = 'offers';

    public static string $label = 'عرضه‌ها و قیمت‌ها';

    public static string $singular = 'عرضه';

    public static string $icon = 'layers';

    public static string $group = 'فروشگاه';

    /** مدیر کل، و فروشنده برای فروشگاه خودش. ویرایشگر محتوا کاری با قیمت ندارد. */
    public static array $roles = ['admin', 'vendor'];

    public static string $orderBy = 'id';

    public static function searchable(): array
    {
        return [];
    }

    public static function with(): array
    {
        return ['vendor', 'product'];
    }

    /**
     * قید مالکیت، در سطح کوئری و نه در ویو.
     *
     * هر مسیرِ این منبع — فهرست، ویرایش، ذخیره، حذف — از همین‌جا می‌گذرد،
     * پس فروشنده حتی با حدسِ id به عرضه‌ی فروشنده‌ی دیگر نمی‌رسد: ردیف در
     * کوئری‌اش نیست و ۴۰۴ می‌گیرد.
     */
    public static function query(): Builder
    {
        $query = parent::query();

        if (auth()->user()?->isVendor()) {
            $query->where('vendor_id', auth()->user()->vendor_id);
        }

        return $query;
    }

    /**
     * شناسه‌ی فروشگاه از کاربر می‌آید، نه از فرم.
     *
     * نبودنِ فیلد در فرم کافی نیست: فرم را می‌شود دست‌کاری کرد و یک
     * vendor_id دلخواه فرستاد. اینجا مقدار بازنویسی می‌شود، پس مهم نیست
     * مرورگر چه فرستاده.
     */
    public static function beforeSave(array $data, ?Model $record): array
    {
        if (auth()->user()?->isVendor()) {
            $data['vendor_id'] = auth()->user()->vendor_id;
        }

        return $data;
    }

    public static function fields(): array
    {
        $isVendor = auth()->user()?->isVendor() ?? false;

        return array_values(array_filter([
            /*
             * فروشنده ستونِ فروشنده را نه می‌بیند و نه می‌نویسد.
             *
             * فرمی که فیلدِ vendor_id داشته باشد، یعنی فروشنده می‌تواند
             * عرضه‌اش را به نامِ فروشگاه دیگری ثبت کند. مقدارش هنگام ذخیره
             * از کاربرِ واردشده می‌آید و نه از فرم.
             */
            $isVendor ? null : Field::relation('vendor_id', 'فروشنده', Vendor::class)
                ->rules(['required', 'exists:vendors,id'])
                ->inList(true)
                ->half(),

            Field::relation('product_id', 'محصول', Product::class)
                ->rules(['required', 'exists:products,id'])
                ->inList(true)
                ->half(),

            Field::number('price', 'قیمت ('.config('shop.currency').')')
                ->rules(['required', 'integer', 'min:0'])
                ->inList()
                ->half(),

            Field::text('unit', 'واحد')->rules(['required', 'max:20'])->half(),

            Field::number('min_order', 'حداقل سفارش')
                ->rules(['required', 'integer', 'min:1'])
                ->half(),

            Field::number('stock', 'موجودی (خالی = اعلام‌نشده)')
                ->rules(['nullable', 'integer', 'min:0'])
                ->inList()
                ->half(),

            Field::number('lead_time_days', 'زمان تحویل (روز)')
                ->rules(['nullable', 'integer', 'min:0', 'max:365'])
                ->half(),

            Field::boolean('is_active', 'فعال')->inList()->half(),
        ]));
    }
}
