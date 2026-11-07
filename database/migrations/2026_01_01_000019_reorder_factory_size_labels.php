<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
| ابعاد به ترتیبی که کارخانه می‌نویسد: ضخامت (یا طول برای سقفی) اول.
|
|   ۸×۱۸٫۵×۴۰   ۸×۱۸٫۵×۲۰   ۱۳×۱۸٫۵×۴۰   ۱۳×۱۸٫۵×۲۰
|   ۴۰×۲۰×۲۰    ۴۰×۲۵×۲۰    ۵۰×۲۵×۲۰
|
| فقط برچسبی عوض می‌شود که هنوز همان ترتیبِ پیشین است؛ آنچه مدیر در پنل
| نوشته می‌ماند.
*/
return new class extends Migration
{
    protected array $map = [
        'partition-block-8' => ['40×18.5×8', '8×18.5×40'],
        'partition-block-8-20' => ['20×18.5×8', '8×18.5×20'],
        'partition-block-13' => ['40×18.5×13', '13×18.5×40'],
        'partition-block-13-20' => ['20×18.5×13', '13×18.5×20'],
        'ceramic-block-20' => ['20×20×40', '40×20×20'],
        'roof-block-40' => ['20×25×40', '40×25×20'],
        'roof-block-50' => ['20×25×50', '50×25×20'],
    ];

    public function up(): void
    {
        foreach ($this->map as $slug => [$old, $new]) {
            DB::table('products')->where('slug', $slug)->where('size_label', $old)->update(['size_label' => $new]);
        }
    }

    public function down(): void
    {
        foreach ($this->map as $slug => [$old, $new]) {
            DB::table('products')->where('slug', $slug)->where('size_label', $new)->update(['size_label' => $old]);
        }
    }
};
