<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // مرکز فنی: دیتاشیت، کاتالوگ، CAD، BIM، راهنمای اجرا، استاندارد
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->text('description')->nullable();
            $table->string('category');   // datasheet|catalog|cad|bim|installation|certificate|standard
            $table->string('format');     // pdf|dwg|rvt|ifc|skp|dxf|zip
            $table->string('audience')->default('engineer'); // customer|engineer|contractor
            $table->string('file_path')->nullable();
            $table->unsignedInteger('file_size_kb')->default(0);
            $table->string('version')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['category', 'position']);
            $table->index('audience');
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category')->default('technical');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('author')->nullable();
            $table->unsignedSmallInteger('reading_time')->default(5);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('published_at');
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general'); // general|technical|order|installation
            $table->string('question');
            $table->text('answer');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('general'); // general|quote|technical|distributor
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('city')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('status')->default('new');
            $table->ipAddress('ip')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('group')->default('general');
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('documents');
    }
};
