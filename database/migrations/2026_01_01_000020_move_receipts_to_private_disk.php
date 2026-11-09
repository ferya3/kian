<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/*
| رسیدهای واریز از دیسکِ public به دیسکِ خصوصی.
|
| رسید نام و شماره‌ی کارت و مبلغ دارد و تا اینجا با نشانیِ ‎/storage/receipts/…
| بی ورود باز می‌شد. مسیرِ نسبی در دیتابیس همان می‌ماند؛ فقط دیسک عوض
| می‌شود، و Receipt::image_link حالا از کنترلرِ پنل می‌گذرد.
|
| کپی پیش از پاک‌کردن، و پاک‌کردن فقط وقتی کپی سرِ جایش است.
*/
return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('receipts')) {
            return;
        }

        $public = Storage::disk('public');
        $private = Storage::disk('local');

        foreach (DB::table('receipts')->whereNotNull('image')->pluck('image') as $path) {
            if (! $public->exists($path)) {
                continue;
            }

            if (! $private->exists($path)) {
                $private->put($path, $public->get($path));
            }

            if ($private->exists($path) && $private->size($path) === $public->size($path)) {
                $public->delete($path);
            }
        }
    }

    public function down(): void
    {
        // برگرداندنِ رسیدها به دیسکِ عمومی عمداً انجام نمی‌شود
    }
};
