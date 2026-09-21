<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * روشن یا خاموش بودنِ هر زبان.
 *
 * مشخصاتِ زبان — نام، جهت، قلم، ارقام، تقویم — در config/locales.php می‌ماند،
 * چون کد است: افزودن زبان تازه یعنی پوشه‌ی lang و ترجمه‌ی محتوا، نه یک دکمه.
 * ولی «کدام‌ها همین حالا روی سایت باشند» تصمیمِ مدیر است و نه برنامه‌نویس،
 * پس اینجا و نه در پرونده‌ی تنظیمات.
 *
 * جدول فقط کلید و وضعیت دارد. هر ستونِ دیگری اینجا، نسخه‌ی دومی از همان
 * مشخصات می‌شد که با config از هم می‌افتادند.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locales', function (Blueprint $table) {
            $table->id();
            $table->string('code', 12)->unique();
            $table->boolean('is_active')->default(true);
            // ترتیب سوئیچر زبان
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        /*
         * ردیف‌های اولیه از همان تنظیماتِ امروز ساخته می‌شوند تا سایتِ در حال
         * کار، پس از مهاجرت دقیقاً همان زبان‌های قبلی را داشته باشد.
         *
         * App\Models\Locale::sync هم همین کار را می‌کند، ولی مهاجرت نباید به
         * مدل تکیه کند: مدلِ فردا ممکن است ستونی بخواهد که امروز نیست.
         */
        $rows = [];
        $order = 0;

        foreach (config('locales.available', []) as $code => $meta) {
            $rows[] = [
                'code' => $code,
                'is_active' => (bool) ($meta['enabled'] ?? true),
                'order' => $order += 10,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($rows) {
            DB::table('locales')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('locales');
    }
};
