<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * رسیدِ حواله — پرداخت بیرون از سایت، اثباتش داخل سایت.
 *
 * درگاهی در کار نیست: مشتری کارت‌به‌کارت یا حواله می‌کند و عکسِ رسید را
 * بالا می‌دهد؛ فروشنده در پنل تأیید می‌کند.
 *
 * یک رسید به ازای هر «سفارش و فروشنده» و نه هر سفارش. سفارشی که نزد دو
 * فروشنده رفته، دو حواله‌ی جدا به دو حساب دارد؛ با یک رسید برای کلِ سفارش،
 * فروشنده‌ی دوم هیچ راهی نداشت بفهمد سهمِ او پرداخت شده یا نه.
 *
 * مبلغ هم ذخیره می‌شود و از روی ردیف‌ها حساب نمی‌شود: رسید سندِ یک پرداختِ
 * انجام‌شده است و اگر فردا قیمتی عوض شود، سند نباید با آن تکان بخورد.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();

            // سهمِ همین فروشنده از سفارش، به تومان
            $table->unsignedBigInteger('amount');

            // شماره‌ی پیگیریِ بانک یا چهار رقم آخر کارت — هرچه مشتری دارد
            $table->string('reference', 60)->nullable();

            $table->string('image')->nullable();

            $table->string('status', 20)->default('pending');

            /*
             | یادداشتِ فروشنده هنگام رد — مشتری باید بداند چرا رد شد، وگرنه
             | همان رسید را دوباره می‌فرستد.
             */
            $table->string('note', 400)->nullable();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            /*
             | یکتا: دوباره‌فرستادن، همان ردیف را به‌روز می‌کند و ردیف تازه
             | نمی‌سازد. وگرنه فروشنده چند رسید می‌دید و نمی‌دانست کدام
             | معتبر است.
             */
            $table->unique(['order_id', 'vendor_id']);
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->string('bank_holder', 120)->nullable()->after('about');
            $table->string('bank_name', 60)->nullable()->after('bank_holder');
            $table->string('bank_card', 32)->nullable()->after('bank_name');
            $table->string('bank_iban', 34)->nullable()->after('bank_card');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['bank_holder', 'bank_name', 'bank_card', 'bank_iban']);
        });
    }
};
