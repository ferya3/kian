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

    public function test_an_exterior_wall_needing_heavy_insulation_gets_the_thickest_block(): void
    {
        $matches = app(ProductFinder::class)->search([
            'project_type' => 'residential',
            'wall_type' => 'exterior',
            'thickness' => 25,
            'insulation' => 'high',
        ]);

        $this->assertSame('ceramic-block-25', $matches->first()['product']->slug);
        $this->assertSame(100, $matches->first()['score']);
        $this->assertNotEmpty($matches->first()['reasons']);
    }

    public function test_a_light_internal_partition_gets_a_partition_block(): void
    {
        $best = app(ProductFinder::class)->search([
            'project_type' => 'residential',
            'wall_type' => 'partition',
            'thickness' => 13,
            'insulation' => 'low',
        ])->first();

        $this->assertSame('partition-block-13', $best['product']->slug);
    }

    /** ضخامتی که تولید نمی‌شود، به نزدیک‌ترین سایزِ موجود می‌رسد. */
    public function test_a_thickness_the_factory_does_not_make_falls_to_the_nearest_one(): void
    {
        $best = app(ProductFinder::class)->search([
            'wall_type' => 'partition',
            'thickness' => 10,          // بین ۸ و ۱۳، نزدیک‌تر به ۸
            'insulation' => 'low',
        ])->first();

        $this->assertSame('partition-block-8', $best['product']->slug);
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

    /**
     * محصولی که از هیچ راهی پیشنهاد نمی‌شود، در عمل وجود ندارد.
     *
     * این تست پیش‌تر فقط تعداد محصول‌ها را می‌شمرد — نامش چیزی می‌گفت و
     * کارش چیز دیگری. با عوض‌شدنِ کاتالوگ به پنج سایزِ واقعی، همان شمارش
     * هم شکست و معلوم شد چیزی را نگه نمی‌داشته.
     */
    public function test_every_seeded_product_is_reachable_by_some_criteria(): void
    {
        $products = Product::query()->active()->get();

        $this->assertNotEmpty($products);

        $reachable = collect(array_keys(config('kian.finder.wall_types')))
            ->flatMap(fn (string $wallType) => app(ProductFinder::class)
                ->search(['wall_type' => $wallType])
                ->pluck('product.slug'))
            ->unique();

        foreach ($products as $product) {
            $this->assertContains(
                $product->slug,
                $reachable->all(),
                "محصول «{$product->slug}» با هیچ نوع دیواری پیشنهاد نمی‌شود."
            );
        }
    }
}
