<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
| ریدایرکتِ نشانی‌های سایتِ قدیم (وردپرس) به صفحه‌های تازه.
|
| from_path شکلِ نرمال‌شده‌ی نشانیِ قدیم است (Redirect::normalize): رمزگشایی‌شده،
| بی اسلشِ پایانی، با حروفِ کوچک. to_url خالی یعنی «هنوز مقصد ندارد»: سایت
| ۴۰۴ می‌دهد ولی تعدادِ بازدید شمرده می‌شود تا مدیر ببیند کدام نشانی‌ها مهم‌اند.
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path', 700)->unique();
            $table->string('to_url', 700)->nullable();
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->string('source', 20)->default('manual');
            $table->string('note')->nullable();
            $table->unsignedInteger('hits')->default(0);
            $table->timestamp('last_hit_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hits']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
