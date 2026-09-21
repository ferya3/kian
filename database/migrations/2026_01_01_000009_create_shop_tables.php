<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * جدول‌های فروشگاه چندفروشنده‌ای.
 *
 * جدول‌ها ساخته می‌شوند حتی وقتی کلید فروشگاه خاموش است — و خاموش هم هست.
 * دلیلش ساده است: جدولِ خالی هزینه‌ای ندارد، ولی مهاجرتی که روز فعال‌سازی
 * باید روی دیتابیسِ پرِ سایتِ زنده اجرا شود، دارد.
 *
 * چهار جدول و یک ستون:
 *
 *   vendors      فروشنده. می‌تواند به یک نمایندگی موجود وصل باشد یا نباشد.
 *   offers       عرضه‌ی یک محصول توسط یک فروشنده: قیمت، موجودی، حداقل سفارش.
 *   orders       یک بار تسویه‌ی مشتری.
 *   order_items  ردیف‌های همان سفارش، هرکدام متعلق به یک فروشنده.
 *   users.vendor_id  کاربری که به‌جای پنل مدیریت، پنل فروشنده را می‌بیند.
 *
 * چرا offers جدا از products؟ چون قیمت خاصیتِ محصول نیست، خاصیتِ رابطه‌ی
 * «فروشنده و محصول» است. با ستون price روی products، دومین فروشنده جایی
 * برای نوشتن قیمتش نداشت و کلِ طرح باید بازنویسی می‌شد.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();

            /*
             * پیوند اختیاری به نمایندگی.
             *
             * نماینده می‌تواند فروشنده هم بشود، ولی لازم نیست: فروشنده‌ای که
             * نمایندگی نیست هم وجود دارد. nullOnDelete است تا حذف یک نمایندگی،
             * فروشگاهش را با خودش نبرد.
             */
            $table->foreignId('distributor_id')->nullable()->constrained()->nullOnDelete();

            $table->string('phone')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->text('about')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index('is_active');
        });

        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            /*
             * مبلغ عدد صحیح است و نه اعشاری.
             *
             * پول با float جمع زده نمی‌شود؛ خطای گردکردن در فاکتور دیده
             * می‌شود و اعتماد را می‌برد. واحد در config/shop.php است.
             */
            $table->unsignedBigInteger('price');

            $table->string('unit')->default('عدد');
            $table->unsignedInteger('min_order')->default(1);

            /* null یعنی «موجودی را اعلام نمی‌کنم»، نه «ناموجود». */
            $table->unsignedInteger('stock')->nullable();

            $table->unsignedSmallInteger('lead_time_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // هر فروشنده برای هر محصول یک عرضه دارد، نه دو تا
            $table->unique(['vendor_id', 'product_id']);
            $table->index(['product_id', 'is_active']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();

            /*
             * پیگیری بدون حساب کاربری.
             *
             * مشتری ثبت‌نام نمی‌کند، پس سفارشش را با یک نشانی غیرقابل‌حدس
             * دنبال می‌کند. id در نشانی یعنی هر کسی با شمردن، سفارش دیگران
             * را می‌بیند.
             */
            $table->string('token', 64)->unique();

            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('total');
            $table->string('status', 20)->default('new');
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            /*
             * restrictOnDelete عمدی است: فروشنده‌ای که سفارش ثبت‌شده دارد
             * حذف نمی‌شود. سابقه‌ی فروش، سندِ مالی است و با رفتن یک ردیف
             * از جدول دیگر نباید ناقص شود.
             */
            $table->foreignId('vendor_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();

            /*
             * و با همین حساب، نام‌ها هم کپی می‌شوند.
             *
             * فاکتور باید همان چیزی را نشان دهد که روز خرید بوده. اگر
             * فروشنده فردا نامش را عوض کند، سفارش دیروز نباید تغییر کند.
             */
            $table->string('product_name');
            $table->string('vendor_name');

            $table->unsignedBigInteger('unit_price');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('total');
            $table->string('status', 20)->default('new');
            $table->timestamps();

            // فروشنده فقط ردیف‌های خودش را می‌بیند؛ این ایندکس همان کوئری است
            $table->index(['vendor_id', 'status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vendor_id');
        });

        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('offers');
        Schema::dropIfExists('vendors');
    }
};
