<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // admin: دسترسی کامل — editor: فقط محتوا، بدون کاربران و تنظیمات
            $table->string('role')->default('editor')->after('email');
            $table->boolean('is_active')->default(true)->after('role');
            $table->timestamp('last_login_at')->nullable();
            $table->ipAddress('last_login_ip')->nullable();

            $table->index('role');
        });

        // گزارش تغییرات: چه کسی، چه چیزی، چه زمانی — برای پاسخ‌گویی و ردیابی
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name');                 // نام در لحظه‌ی ثبت، حتی اگر کاربر بعداً حذف شود
            $table->string('event');                     // created|updated|deleted|login|login_failed|logout
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_label')->nullable();
            $table->json('changes')->nullable();          // فقط نام فیلدهای تغییریافته، نه مقادیر
            $table->ipAddress('ip')->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index(['event', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'is_active', 'last_login_at', 'last_login_ip']);
        });
    }
};
