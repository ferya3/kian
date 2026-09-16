<?php

use App\Models\SiteMedia;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * جایگاه‌های تصویری سایت.
 *
 * برچسب و گروه و توضیحِ هر جایگاه در SiteMedia::SLOTS زندگی می‌کند، نه در
 * دیتابیس: جایگاه چیزی است که یک ویو واقعاً می‌خواند، پس تعریفش باید کنار کد
 * باشد. اینجا فقط چیزی ذخیره می‌شود که مدیر وارد می‌کند — تصویر و متن جایگزین.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_media', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->string('image')->nullable();
            $table->string('alt', 190)->nullable();
            $table->timestamps();
        });

        SiteMedia::sync();
    }

    public function down(): void
    {
        Schema::dropIfExists('site_media');
    }
};
