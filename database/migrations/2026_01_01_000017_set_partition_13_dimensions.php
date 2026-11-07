<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
| ابعادِ واقعیِ بلوک تیغه‌ای ۱۳، از سایتِ قبلیِ کارخانه: ۴۰ × ۱۸٫۵ × ۱۳.
|
| ابعادِ پیشین (۲۵ × ۱۳ × ۲۰) تخمینی بود. وزن و تعداد در متر مربع و پالت
| از روی حجم و رویه‌ی تازه بازحساب شده‌اند و همچنان تخمینی‌اند.
|
| فقط وقتی جایگزین می‌شود که همان تخمینِ دست‌نخورده سرِ جایش باشد؛ اگر
| مدیر از پنل عددی گذاشته، عددِ او می‌ماند.
*/
return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')
            ->where('slug', 'partition-block-13')
            ->where('length_mm', 250)->where('height_mm', 200)->where('width_mm', 130)
            ->update([
                'length_mm' => 400, 'height_mm' => 185,
                'weight_kg' => 8.90, 'units_per_sqm' => 12.5, 'units_per_pallet' => 100,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('products')
            ->where('slug', 'partition-block-13')
            ->where('length_mm', 400)->where('height_mm', 185)
            ->update([
                'length_mm' => 250, 'height_mm' => 200,
                'weight_kg' => 6.00, 'units_per_sqm' => 20, 'units_per_pallet' => 150,
                'updated_at' => now(),
            ]);
    }
};
