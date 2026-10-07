<?php

namespace App\Admin\Resources;

use App\Models\Receipt;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;
use Illuminate\Database\Eloquent\Builder;

/**
 * رسیدهای حواله — برای تأیید یا رد.
 *
 * هم مدیر کل می‌بیند و هم فروشنده، ولی فروشنده فقط رسیدهای خودش را: مبلغی
 * که به حسابِ او رفته به رقیبش ربطی ندارد. همان قاعده‌ای که در
 * VendorOrderResource هست، با همان روش.
 *
 * ساختنی نیست؛ رسید را مشتری از صفحه‌ی سفارش می‌فرستد. اینجا فقط داوری
 * می‌شود.
 */
class ReceiptResource extends Resource
{
    public static string $model = Receipt::class;

    public static string $slug = 'receipts';

    public static string $label = 'رسیدهای حواله';

    public static string $singular = 'رسید';

    public static string $icon = 'file';

    public static string $group = 'فروشگاه';

    public static bool $creatable = false;

    public static string $orderBy = 'created_at';

    public static string $orderDir = 'desc';

    public static function with(): array
    {
        return ['order', 'vendor'];
    }

    public static function query(): Builder
    {
        $query = parent::query();

        $user = auth()->user();

        // مدیر و کارمندِ دفتر همه را می‌بینند؛ فروشنده فقط مالِ خودش را
        if ($user?->role !== 'vendor') {
            return $query;
        }

        return $user->vendor_id
            ? $query->where('vendor_id', $user->vendor_id)
            : $query->whereRaw('1 = 0');
    }

    public static function fields(): array
    {
        return [
            Field::readonly('order_number', 'شماره سفارش')->inList(true),
            Field::readonly('vendor_name', 'فروشنده')->inList(),
            Field::readonly('amount_label', 'مبلغ')->inList(),
            Field::readonly('reference', 'شماره پیگیری')->inList(),

            /*
            | عکسِ رسید خواندنی است و نه آپلودی: این پرونده را مشتری فرستاده
            | و سند است. اگر اینجا قابل جایگزینی بود، همان سند زیر دستِ
            | طرفِ مقابل عوض می‌شد.
            */
            Field::readonly('image_link', 'عکس رسید'),

            Field::select('status', 'وضعیت', config('shop.receipt_statuses'))
                ->rules(['required', 'in:'.implode(',', array_keys(config('shop.receipt_statuses')))])
                ->inList()
                ->half(),

            Field::text('note', 'علت رد (برای مشتری)')->rules(['nullable', 'max:400'])
                ->hint('اگر رد می‌کنید، بنویسید چرا — وگرنه مشتری همان رسید را دوباره می‌فرستد.')
                ->half(),
        ];
    }
}
