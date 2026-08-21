<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Product;
use App\Models\Project;
use App\Models\Solution;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public static function staticRoutes(): array
    {
        return array_map(fn ($name) => [$name], [
            'home', 'products.index', 'finder.show', 'solutions.index', 'projects.index',
            'technology', 'factory', 'sustainability', 'technical.index', 'technical.downloads',
            'technical.installation', 'technical.certificates', 'technical.faq',
            'articles.index', 'about', 'distributors', 'contact', 'search', 'sitemap', 'robots',
        ]);
    }

    /** @dataProvider staticRoutes */
    public function test_static_pages_render(string $name): void
    {
        $this->get(route($name))->assertOk();
    }

    public function test_every_product_has_its_own_page(): void
    {
        foreach (Product::all() as $product) {
            $this->get(route('products.show', $product))
                ->assertOk()
                ->assertSee($product->name)
                ->assertSee('مشخصات فنی');
        }
    }

    public function test_detail_pages_render(): void
    {
        $this->get(route('projects.show', Project::first()))->assertOk();
        $this->get(route('solutions.show', Solution::first()))->assertOk();
        $this->get(route('articles.show', Article::query()->published()->first()))->assertOk();
    }

    public function test_inactive_product_is_not_reachable(): void
    {
        $product = Product::first();
        $product->update(['is_active' => false]);

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_unpublished_article_is_not_reachable(): void
    {
        $article = Article::first();
        $article->update(['published_at' => now()->addWeek()]);

        $this->get(route('articles.show', $article))->assertNotFound();
    }

    public function test_product_page_exposes_product_structured_data(): void
    {
        $product = Product::where('slug', 'ceramic-block-20')->firstOrFail();

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('"@type":"Product"', escape: false)
            ->assertSee('"@type":"BreadcrumbList"', escape: false)
            ->assertSee($product->sku);
    }

    public function test_sitemap_lists_every_product(): void
    {
        $response = $this->get(route('sitemap'))->assertOk();

        foreach (Product::query()->active()->get() as $product) {
            $response->assertSee(route('products.show', $product), escape: false);
        }
    }

    public function test_category_filter_narrows_the_catalogue(): void
    {
        $this->get(route('products.index', ['category' => 'insulating-blocks']))
            ->assertOk()
            ->assertSee('بلوک عایق ۲۵')
            ->assertDontSee('بلوک تیغه‌ای ۷');
    }

    public function test_search_finds_products_by_name(): void
    {
        $this->get(route('search', ['q' => 'بلوک سفالی ۲۰']))
            ->assertOk()
            ->assertSee('بلوک سفالی ۲۰');
    }
}
