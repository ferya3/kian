<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ۰۱ تا ۰۹ — از خاک تا سازه
        Schema::create('process_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('step_no');
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('metric_label')->nullable();  // مثلا «دمای پخت»
            $table->string('metric_value')->nullable();  // مثلا «۹۰۰ درجه»
            $table->string('duration')->nullable();
            $table->string('image')->nullable();
            $table->string('video_url')->nullable();
            $table->timestamps();

            $table->index('step_no');
        });

        // نقاط تعاملی روی نمای هوایی کارخانه
        Schema::create('factory_sections', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->text('description')->nullable();
            $table->decimal('hotspot_x', 5, 2);
            $table->decimal('hotspot_y', 5, 2);
            $table->json('stats')->nullable();
            $table->string('image')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('factory'); // factory|sustainability
            $table->decimal('value', 12, 2);
            $table->string('prefix')->nullable();
            $table->string('suffix')->nullable();
            $table->unsignedTinyInteger('decimals')->default(0);
            $table->string('label');
            $table->string('description')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('issuer')->nullable();
            $table->string('number')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('image')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('distributors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('manager')->nullable();
            $table->string('province');
            $table->string('city');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index('province');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distributors');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('stats');
        Schema::dropIfExists('factory_sections');
        Schema::dropIfExists('process_steps');
    }
};
