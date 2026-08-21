<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\ProductFinder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFinderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_an_exterior_wall_needing_heavy_insulation_gets_an_insulating_block(): void
    {
        $matches = app(ProductFinder::class)->search([
            'project_type' => 'residential',
            'wall_type' => 'exterior',
            'thickness' => 25,
            'insulation' => 'high',
        ]);

        $this->assertSame('insulating-block-25', $matches->first()['product']->slug);
        $this->assertSame(100, $matches->first()['score']);
        $this->assertNotEmpty($matches->first()['reasons']);
    }

    public function test_a_light_internal_partition_gets_a_partition_block(): void
    {
        $best = app(ProductFinder::class)->search([
            'project_type' => 'residential',
            'wall_type' => 'partition',
            'thickness' => 10,
            'insulation' => 'low',
        ])->first();

        $this->assertSame('partition-block-10', $best['product']->slug);
    }

    public function test_it_always_returns_suggestions_even_with_no_perfect_match(): void
    {
        // ضخامتی که هیچ محصولی دقیقاً ندارد
        $matches = app(ProductFinder::class)->search([
            'wall_type' => 'roof',
            'thickness' => 30,
            'insulation' => 'very_high',
        ]);

        $this->assertCount(3, $matches);
        $this->assertGreaterThan(0, $matches->first()['score']);
    }

    public function test_the_results_endpoint_can_return_a_partial_for_live_updates(): void
    {
        $response = $this->withHeader('X-Partial', '1')
            ->get(route('finder.results', [
                'project_type' => 'mass',
                'wall_type' => 'interior',
            ]))
            ->assertOk();

        // پاسخ جزئی است: بدون layout کامل
        $this->assertStringNotContainsString('<!DOCTYPE html>', $response->getContent());
        $response->assertSee('بهترین تطابق');
    }

    public function test_it_rejects_unknown_criteria(): void
    {
        $this->get(route('finder.results', ['wall_type' => 'ceiling-of-the-sky']))
            ->assertSessionHasErrors('wall_type');
    }

    public function test_results_are_not_indexed_by_search_engines(): void
    {
        $this->get(route('finder.results', ['wall_type' => 'exterior']))
            ->assertOk()
            ->assertSee('name="robots" content="noindex, follow"', escape: false);
    }

    public function test_every_seeded_product_is_reachable_by_some_criteria(): void
    {
        $this->assertGreaterThanOrEqual(10, Product::query()->active()->count());
    }
}
