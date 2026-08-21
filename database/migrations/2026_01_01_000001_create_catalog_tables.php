<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['parent_id', 'position']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();

            // --- ابعاد و هندسه ---
            $table->unsignedSmallInteger('length_mm');
            $table->unsignedSmallInteger('width_mm');
            $table->unsignedSmallInteger('height_mm');
            $table->unsignedSmallInteger('thickness_mm');   // ضخامت دیوار حاصل
            $table->unsignedTinyInteger('void_ratio')->default(0); // درصد تخلخل

            // --- مشخصات فنی ---
            $table->decimal('weight_kg', 6, 2);
            $table->decimal('compressive_strength_mpa', 5, 2);
            $table->decimal('thermal_conductivity', 5, 3); // λ  W/m.K
            $table->decimal('thermal_resistance', 5, 3)->nullable(); // R  m².K/W
            $table->decimal('water_absorption', 5, 2);     // %
            $table->unsignedSmallInteger('fire_resistance_min')->default(0);
            $table->unsignedTinyInteger('sound_reduction_db')->default(0);
            $table->unsignedSmallInteger('units_per_pallet')->default(0);
            $table->decimal('units_per_sqm', 5, 2)->default(0);
            $table->decimal('mortar_per_sqm', 5, 2)->nullable(); // لیتر ملات

            // --- امتیازهای عملکردی برای نمودارهای میله‌ای (۰ تا ۱۰۰) ---
            $table->unsignedTinyInteger('thermal_score')->default(0);
            $table->unsignedTinyInteger('acoustic_score')->default(0);
            $table->unsignedTinyInteger('strength_score')->default(0);
            $table->unsignedTinyInteger('sustainability_score')->default(0);

            // --- فیلدهای موتور «انتخاب محصول» ---
            $table->json('project_types')->nullable();  // residential|commercial|industrial|mass
            $table->json('wall_types')->nullable();     // exterior|interior|partition|infill|roof
            $table->string('insulation_level')->default('medium'); // low|medium|high|very_high
            $table->boolean('is_loadbearing')->default(false);

            $table->json('features')->nullable();
            $table->json('applications')->nullable();
            $table->json('standards')->nullable();

            $table->string('hero_image')->nullable();
            $table->string('section_image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('color_hex')->default('#B4552D');

            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();

            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['is_active', 'position']);
            $table->index('thickness_mm');
            $table->index('insulation_level');
        });

        // نقاط تعاملی روی مقطع بلوک
        Schema::create('product_cavities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->text('description')->nullable();
            $table->decimal('x', 5, 2); // درصد
            $table->decimal('y', 5, 2); // درصد
            $table->string('metric_label')->nullable();
            $table->string('metric_value')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_cavities');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
    }
};
