<?php

namespace App\Models;

use App\Support\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasTranslations;

    /** فیلدهایی که در پنل برای هر زبان جداگانه پر می‌شوند. */
    public array $translatable = [
        'name',
        'subtitle',
        'summary',
        'description',
    ];

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

    /**
     * عرضه‌های این محصول — فروشگاه.
     *
     * محصول خودش قیمت ندارد و نباید داشته باشد: قیمت خاصیتِ رابطه‌ی
     * «فروشنده و محصول» است. تا وقتی کلید فروشگاه خاموش است، این رابطه
     * فقط خالی برمی‌گردد و هیچ‌جا خوانده نمی‌شود.
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
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
            ['key' => 'dimensions', 'label' => __('site.spec.row.dimensions'), 'value' => $this->dimensionLabel(), 'unit' => 'cm'],
            ['key' => 'thickness', 'label' => __('site.spec.row.thickness'), 'value' => $this->thicknessCm(), 'unit' => 'cm'],
            ['key' => 'weight', 'label' => __('site.spec.row.weight'), 'value' => $this->weight_kg, 'unit' => 'kg'],
            ['key' => 'strength', 'label' => __('site.spec.row.strength'), 'value' => $this->compressive_strength_mpa, 'unit' => 'MPa'],
            ['key' => 'lambda', 'label' => __('site.spec.row.lambda'), 'value' => $this->thermal_conductivity, 'unit' => 'W/m·K'],
            $this->thermal_resistance
                ? ['key' => 'r_value', 'label' => __('site.spec.row.r_value'), 'value' => $this->thermal_resistance, 'unit' => 'm²·K/W']
                : null,
            ['key' => 'absorption', 'label' => __('site.spec.row.absorption'), 'value' => $this->water_absorption, 'unit' => '%'],
            $this->sound_reduction_db
                ? ['key' => 'acoustic', 'label' => __('site.spec.row.acoustic'), 'value' => $this->sound_reduction_db, 'unit' => 'dB']
                : null,
            $this->fire_resistance_min
                ? ['key' => 'fire', 'label' => __('site.spec.row.fire'), 'value' => $this->fire_resistance_min, 'unit' => __('site.product.minutes')]
                : null,
            $this->void_ratio
                ? ['key' => 'void', 'label' => __('site.spec.row.void'), 'value' => $this->void_ratio, 'unit' => '%']
                : null,
            ['key' => 'per_sqm', 'label' => __('site.spec.row.per_sqm'), 'value' => $this->units_per_sqm, 'unit' => __('site.spec.units')],
            ['key' => 'per_pallet', 'label' => __('site.spec.row.per_pallet'), 'value' => $this->units_per_pallet, 'unit' => __('site.spec.units')],
            $this->mortar_per_sqm
                ? ['key' => 'mortar', 'label' => __('site.spec.row.mortar'), 'value' => $this->mortar_per_sqm, 'unit' => __('site.spec.litres_sqm')]
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
