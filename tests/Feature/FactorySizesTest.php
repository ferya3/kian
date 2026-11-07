<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * کاتالوگ همان هفت سایزی است که کارخانه تولید می‌کند — نه بیشتر.
 *
 * ابعاد دقیقاً به شکلی که کارخانه نوشته نشان داده می‌شوند؛ ترتیبشان با
 * طول × عرض × ارتفاع یکی نیست («۲۰×۲۰×۴۰» ولی «۴۰×۱۸٫۵×۸»).
 */
class FactorySizesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_the_catalogue_is_exactly_the_seven_factory_sizes(): void
    {
        $this->assertSame([
            '40 × 18.5 × 8', '20 × 18.5 × 8', '40 × 18.5 × 13', '20 × 18.5 × 13',
            '20 × 20 × 40', '20 × 25 × 40', '20 × 25 × 50',
        ], Product::query()->active()->orderBy('position')->get()->map->dimensionLabel()->all());
    }

    public function test_the_product_page_shows_the_size_as_the_factory_writes_it(): void
    {
        $this->get(route('products.show', 'partition-block-8-20'))
            ->assertOk()
            ->assertSee('۲۰ × ۱۸٫۵ × ۸');

        $this->get(route('products.show', 'roof-block-50'))
            ->assertOk()
            ->assertSee('۲۰ × ۲۵ × ۵۰');
    }

    /** پنل رقمِ فارسی و ممیزِ «٫» را می‌پذیرد و به یک شکل ذخیره می‌کند. */
    public function test_the_panel_accepts_persian_digits_in_the_size(): void
    {
        $product = Product::where('slug', 'ceramic-block-20')->firstOrFail();
        $product->update(['size_label' => '۲۰ * ۲۰٫۵ * ۴۰']);

        $this->assertSame('20 * 20.5 * 40', $product->fresh()->size_label);
        $this->assertSame('20 × 20.5 × 40', $product->fresh()->dimensionLabel());
    }

    /** بی برچسب، ابعاد از طول × عرض × ارتفاع ساخته می‌شود — مثل همیشه. */
    public function test_without_a_label_the_size_comes_from_the_dimensions(): void
    {
        $product = Product::where('slug', 'ceramic-block-20')->firstOrFail();
        $product->update(['size_label' => null]);

        $this->assertSame('40 × 20 × 20', $product->fresh()->dimensionLabel());
    }

    public function test_the_panel_rejects_a_malformed_size(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $product = Product::where('slug', 'ceramic-block-20')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.resource.update', ['resource' => 'products', 'id' => $product->id]), ['size_label' => '20 by 40'])
            ->assertSessionHasErrors('size_label');
    }
}
