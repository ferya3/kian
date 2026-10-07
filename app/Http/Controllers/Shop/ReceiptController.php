<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Receipt;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * آپلودِ رسیدِ حواله، از صفحه‌ی پیگیریِ سفارش.
 *
 * مجوزش نشانیِ غیرقابل‌حدسِ خودِ سفارش است و نه حساب کاربری — مشتری در این
 * فروشگاه حساب نمی‌سازد. پس هر چیزی که از فرم می‌آید با همان سفارش سنجیده
 * می‌شود و نه با آنچه فرم ادعا می‌کند.
 */
class ReceiptController extends Controller
{
    public function store(Request $request, Order $order)
    {
        $data = $request->validate([
            'vendor_id' => ['required', 'integer'],
            'reference' => ['nullable', 'string', 'max:60'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        /*
         * فروشنده باید واقعاً در همین سفارش ردیف داشته باشد.
         *
         * بی این، کسی که نشانیِ سفارش را دارد می‌توانست با دست‌کاریِ
         * vendor_id برای فروشنده‌ای که در این سفارش نیست رسید بسازد.
         */
        $items = $order->items->where('vendor_id', (int) $data['vendor_id']);

        if ($items->isEmpty()) {
            abort(404);
        }

        $vendor = Vendor::find($data['vendor_id']);

        if (! $vendor?->acceptsTransfer()) {
            return back()->withErrors(['image' => __('site.shop.receipt_no_account')]);
        }

        $existing = Receipt::where('order_id', $order->id)
            ->where('vendor_id', $vendor->id)
            ->first();

        // رسیدِ تأییدشده سند است؛ جایگزین‌کردنش یعنی خالی‌کردنِ زیر پای فروشنده
        if ($existing?->isLocked()) {
            return back()->withErrors(['image' => __('site.shop.receipt_locked')]);
        }

        $path = $request->file('image')->store('receipts', 'public');

        // عکسِ قبلی پس از نشستنِ عکسِ تازه پاک می‌شود، نه پیش از آن
        $previous = $existing?->image;

        Receipt::updateOrCreate(
            ['order_id' => $order->id, 'vendor_id' => $vendor->id],
            [
                // مبلغ از ردیف‌های همین سفارش می‌آید و نه از فرم
                'amount' => (int) $items->sum('total'),
                'reference' => $data['reference'] ?? null,
                'image' => $path,
                'status' => 'pending',
                'note' => null,
                'reviewed_at' => null,
            ],
        );

        if ($previous && $previous !== $path) {
            Storage::disk('public')->delete($previous);
        }

        return back()->with('receipt_saved', $vendor->id);
    }
}
