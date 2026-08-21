<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_category_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('client')->nullable();
            $table->string('architect')->nullable();
            $table->string('city');
            $table->string('province')->nullable();
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('area_sqm')->nullable();
            $table->unsignedInteger('blocks_used')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['project_category_id', 'year']);
        });

        Schema::create('product_project', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'project_id']);
        });

        Schema::create('solutions', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->json('benefits')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('product_solution', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('solution_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'solution_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_solution');
        Schema::dropIfExists('solutions');
        Schema::dropIfExists('product_project');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_categories');
    }
};
