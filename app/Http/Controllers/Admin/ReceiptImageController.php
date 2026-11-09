<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Resources\ReceiptResource;
use App\Http\Controllers\Controller;
use App\Support\Admin\Registry;
use Illuminate\Support\Facades\Storage;

/**
 * عکسِ رسیدِ واریز — فقط برای کسی که حقِ دیدنش را دارد.
 *
 * رسید نام، شماره‌ی کارت و مبلغ را نشان می‌دهد. پیش‌تر روی دیسکِ public بود و
 * هرکه نشانی‌اش را داشت — بی ورود — می‌دیدش. حالا روی دیسکِ خصوصی است و تنها
 * راهِ رسیدن به آن همین‌جاست: همان دسترسی‌ای که ردیفِ رسید در پنل دارد. مدیر
 * و کارمند همه را، فروشنده فقط رسیدهای خودش را (ReceiptResource::query).
 */
class ReceiptImageController extends Controller
{
    public function __invoke(string $id)
    {
        abort_unless(Registry::find('receipts') && Registry::accessible(ReceiptResource::class), 404);

        $receipt = ReceiptResource::findOrFail($id);
        $disk = Storage::disk('local');

        abort_unless($receipt->image && $disk->exists($receipt->image), 404);

        return $disk->response($receipt->image, null, [
            // رسید در کشِ مرورگر یا پراکسیِ میانی نمی‌ماند
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; img-src 'self'; sandbox",
        ]);
    }
}
