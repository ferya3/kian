<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ترجمه‌ی محتوا.
 *
 * ترجمه‌ها روی ستون‌های موجود می‌نشینند و جایشان را نمی‌گیرند: ستونِ خود
 * جدول همچنان متنِ زبان پیش‌فرض است و هر زبان دیگر یک ردیف اینجا دارد.
 *
 * چرا این شکل و نه ستون JSON روی هر جدول:
 *   — داده‌ی موجود دست نمی‌خورد و مهاجرت داده لازم نیست؛
 *   — زبان تازه یعنی ردیف تازه، نه تغییر ساختار؛
 *   — فیلدِ تازه هم فقط یک اسم در $translatable مدل است.
 *
 * هزینه‌اش یک join است، که با eager loading جبران می‌شود — و فقط وقتی
 * زبان جاری پیش‌فرض نیست اصلاً اتفاق می‌افتد.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->morphs('translatable');
            $table->string('locale', 12);
            $table->string('field', 64);
            $table->longText('value')->nullable();
            $table->timestamps();

            // یک ترجمه برای هر فیلد در هر زبان — نه بیشتر
            $table->unique(
                ['translatable_type', 'translatable_id', 'locale', 'field'],
                'translations_unique'
            );

            $table->index(['translatable_type', 'locale'], 'translations_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
