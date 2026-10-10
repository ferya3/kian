<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * مدیرعامل — یک نفر، با بخشِ ویژه‌ی خودش در صفحه‌ی بیوگرافی.
 *
 * مهاجرتِ جدا و نه ویرایشِ ۰۰۰۰۱۴: آن ممکن است روی سرور اجرا شده باشد، و
 * مهاجرتِ اجراشده دوباره اجرا نمی‌شود.
 *
 * ردیفِ الگو ویژه می‌شود، ولی پنهان می‌ماند: نام و عکس و زندگی‌نامه‌ی
 * مدیرعامل را فقط خودِ شرکت دارد.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->text('quote')->nullable()->after('bio');
            $table->boolean('is_featured')->default(false)->after('quote');
        });

        DB::table('people')
            ->where('name', 'نام و نام خانوادگی')
            ->where('is_active', false)
            ->update([
                'role' => 'مدیرعامل',
                'quote' => 'یک یا دو جمله از زبانِ خودِ مدیرعامل — چرا این کار را می‌کند، یا چه قولی به مشتری می‌دهد.',
                'is_featured' => true,
            ]);
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn(['quote', 'is_featured']);
        });
    }
};
