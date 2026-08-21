<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'project_types' => 'array',
            'wall_types' => 'array',
            'features' => 'array',
            'applications' => 'array',
            'standards' => 'array',
            'gallery' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_loadbearing' => 'boolean',
            'weight_kg' => 'float',
            'compressive_strength_mpa' => 'float',
            'thermal_conductivity' => 'float',
            'thermal_resistance' => 'float',
            'water_absorption' => 'float',
            'units_per_sqm' => 'float',
            'mortar_per_sqm' => 'float',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function cavities(): HasMany
    {
        return $this->hasMany(ProductCavity::class)->orderBy('position');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class)->orderBy('position');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function solutions(): BelongsToMany
    {
        return $this->belongsToMany(Solution::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /** ابعاد به شکل ۲۰×۲۰×۴۰ سانتی‌متر */
    public function dimensionLabel(string $unit = 'cm'): string
    {
        $factor = $unit === 'cm' ? 10 : 1;

        return implode(' × ', array_map(
            fn ($mm) => rtrim(rtrim(number_format($mm / $factor, 1), '0'), '.'),
            [$this->length_mm, $this->width_mm, $this->height_mm]
        ));
    }

    public function thicknessCm(): float
    {
        return $this->thickness_mm / 10;
    }

    /** جدول مشخصات فنی — همان چیزی که در صفحه محصول رندر می‌شود. */
    public function specSheet(): array
    {
        return array_values(array_filter([
            ['key' => 'dimensions', 'label' => 'ابعاد اسمی', 'value' => $this->dimensionLabel(), 'unit' => 'cm'],
            ['key' => 'thickness', 'label' => 'ضخامت دیوار', 'value' => $this->thicknessCm(), 'unit' => 'cm'],
            ['key' => 'weight', 'label' => 'وزن هر عدد', 'value' => $this->weight_kg, 'unit' => 'kg'],
            ['key' => 'strength', 'label' => 'مقاومت فشاری', 'value' => $this->compressive_strength_mpa, 'unit' => 'MPa'],
            ['key' => 'lambda', 'label' => 'ضریب هدایت حرارتی (λ)', 'value' => $this->thermal_conductivity, 'unit' => 'W/m·K'],
            $this->thermal_resistance
                ? ['key' => 'r_value', 'label' => 'مقاومت حرارتی (R)', 'value' => $this->thermal_resistance, 'unit' => 'm²·K/W']
                : null,
            ['key' => 'absorption', 'label' => 'جذب آب', 'value' => $this->water_absorption, 'unit' => '%'],
            $this->sound_reduction_db
                ? ['key' => 'acoustic', 'label' => 'کاهش صوت', 'value' => $this->sound_reduction_db, 'unit' => 'dB']
                : null,
            $this->fire_resistance_min
                ? ['key' => 'fire', 'label' => 'مقاومت در برابر آتش', 'value' => $this->fire_resistance_min, 'unit' => 'دقیقه']
                : null,
            $this->void_ratio
                ? ['key' => 'void', 'label' => 'درصد تخلخل', 'value' => $this->void_ratio, 'unit' => '%']
                : null,
            ['key' => 'per_sqm', 'label' => 'تعداد در متر مربع', 'value' => $this->units_per_sqm, 'unit' => 'عدد'],
            ['key' => 'per_pallet', 'label' => 'تعداد در پالت', 'value' => $this->units_per_pallet, 'unit' => 'عدد'],
            $this->mortar_per_sqm
                ? ['key' => 'mortar', 'label' => 'ملات مصرفی', 'value' => $this->mortar_per_sqm, 'unit' => 'لیتر/m²']
                : null,
        ]));
    }

    /**
     * آرایش حفره‌ها برای رندر مقطع بلوک.
     * تعداد ردیف و ستون از ابعاد واقعی محصول مشتق می‌شود تا تصویر با عدد بخواند.
     */
    public function voidPattern(): array
    {
        return [
            'rows' => max(2, (int) round($this->width_mm / 55)),
            'cols' => max(3, min(9, (int) round($this->length_mm / 55))),
        ];
    }

    /** میله‌های عملکردی که روی کارت محصول با hover ظاهر می‌شوند. */
    public function performanceBars(): array
    {
        return [
            ['label' => 'عایق حرارتی', 'label_en' => 'Thermal', 'value' => $this->thermal_score],
            ['label' => 'عایق صوتی', 'label_en' => 'Acoustic', 'value' => $this->acoustic_score],
            ['label' => 'مقاومت سازه‌ای', 'label_en' => 'Strength', 'value' => $this->strength_score],
        ];
    }
}
