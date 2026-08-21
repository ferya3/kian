<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    protected function block(array $overrides = []): Product
    {
        return new Product(array_merge([
            'length_mm' => 400,
            'width_mm' => 200,
            'height_mm' => 200,
            'thickness_mm' => 200,
            'void_ratio' => 58,
            'weight_kg' => 11.5,
            'compressive_strength_mpa' => 6.5,
            'thermal_conductivity' => 0.26,
            'thermal_resistance' => 0.769,
            'water_absorption' => 13.8,
            'fire_resistance_min' => 180,
            'sound_reduction_db' => 48,
            'units_per_pallet' => 96,
            'units_per_sqm' => 12.5,
            'mortar_per_sqm' => 21,
            'thermal_score' => 68,
            'acoustic_score' => 80,
            'strength_score' => 72,
        ], $overrides));
    }

    public function test_it_renders_dimensions_the_way_the_industry_writes_them(): void
    {
        $this->assertSame('40 × 20 × 20', $this->block()->dimensionLabel());
    }

    public function test_it_trims_trailing_zeroes_from_half_centimetre_sizes(): void
    {
        $this->assertSame('25 × 7.5 × 20', $this->block([
            'length_mm' => 250, 'width_mm' => 75, 'height_mm' => 200,
        ])->dimensionLabel());
    }

    public function test_the_spec_sheet_skips_values_the_product_does_not_have(): void
    {
        $keys = collect($this->block(['sound_reduction_db' => 0, 'fire_resistance_min' => 0])->specSheet())
            ->pluck('key');

        $this->assertFalse($keys->contains('acoustic'));
        $this->assertFalse($keys->contains('fire'));
        $this->assertTrue($keys->contains('lambda'));
    }

    public function test_the_void_pattern_follows_the_real_geometry(): void
    {
        $thin = $this->block(['length_mm' => 200, 'width_mm' => 70])->voidPattern();
        $thick = $this->block(['length_mm' => 400, 'width_mm' => 300])->voidPattern();

        $this->assertLessThan($thick['rows'], $thin['rows']);
        $this->assertLessThan($thick['cols'], $thin['cols']);
        $this->assertGreaterThanOrEqual(2, $thin['rows']);
    }

    public function test_performance_bars_cover_the_three_headline_metrics(): void
    {
        $this->assertSame(
            ['Thermal', 'Acoustic', 'Strength'],
            array_column($this->block()->performanceBars(), 'label_en')
        );
    }
}
