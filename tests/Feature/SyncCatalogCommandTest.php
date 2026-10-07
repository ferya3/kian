<?php

namespace Tests\Feature;

use App\Models\Offer;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Vendor;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * هم‌خوان‌کردن کاتالوگِ سرور با کاتالوگِ کد.
 *
 * سرورِ موجود فقط migrate می‌گیرد، پس محصولی که از خط تولید رفته سرِ جایش
 * می‌ماند. این تست‌ها همان حالت را می‌سازند: محصولی که در کد نیست ولی در
 * دیتابیس هست.
 */
class SyncCatalogCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /** محصولی که دیگر تولید نمی‌شود، شبیه‌سازیِ کاتالوگِ کهنه‌ی سرور. */
    protected function discontinued(string $slug = 'insulating-block-30'): Product
    {
        return Product::create([
            'product_category_id' => ProductCategory::where('slug', 'wall-blocks')->firstOrFail()->id,
            'slug' => $slug,
            'sku' => 'OLD-30',
            'name' => 'بلوک عایق ۳۰',
            'length_mm' => 400, 'width_mm' => 300, 'height_mm' => 250, 'thickness_mm' => 300,
            'void_ratio' => 60, 'weight_kg' => 17, 'compressive_strength_mpa' => 7,
            'thermal_conductivity' => 0.17, 'water_absorption' => 13, 'is_active' => true,
            'position' => 99,
        ]);
    }

    public function test_it_removes_what_the_factory_no_longer_makes(): void
    {
        $old = $this->discontinued();

        $this->artisan('catalog:sync', ['--force' => true])->assertSuccessful();

        $this->assertNull(Product::find($old->id));
    }

    public function test_it_keeps_every_product_the_code_declares(): void
    {
        $before = Product::pluck('slug')->sort()->values()->all();

        $this->discontinued();
        $this->artisan('catalog:sync', ['--force' => true])->assertSuccessful();

        $this->assertSame($before, Product::pluck('slug')->sort()->values()->all());
    }

    /**
     * محصولی که در فروشگاه عرضه دارد حذف نمی‌شود.
     *
     * عرضه یعنی فروشنده‌ای رویش قیمت گذاشته و شاید سفارشی در راه باشد؛
     * بردنش از اینجا یعنی گرفتنِ تصمیمِ تجاریِ کسِ دیگری.
     */
    public function test_a_product_with_a_live_offer_is_left_alone(): void
    {
        $old = $this->discontinued();

        $vendor = Vendor::create(['name' => 'ف', 'slug' => 'v', 'is_active' => true]);
        Offer::create(['vendor_id' => $vendor->id, 'product_id' => $old->id, 'price' => 1000, 'min_order' => 1]);

        $this->artisan('catalog:sync', ['--force' => true])
            ->expectsOutputToContain('عرضه دارند')
            ->assertSuccessful();

        $this->assertNotNull(Product::find($old->id));
    }

    /** دسته‌ای که نه محصول دارد نه زیرمجموعه، در منو به صفحه‌ی خالی می‌رسد. */
    public function test_it_prunes_a_category_left_empty(): void
    {
        $orphan = ProductCategory::create([
            'parent_id' => ProductCategory::where('slug', 'ceramic-blocks')->firstOrFail()->id,
            'slug' => 'insulating-blocks',
            'name' => 'بلوک عایق',
            'position' => 9,
        ]);

        $this->artisan('catalog:sync', ['--force' => true])->assertSuccessful();

        $this->assertNull(ProductCategory::find($orphan->id));
        $this->assertNotNull(ProductCategory::where('slug', 'wall-blocks')->first());
    }

    /** دو بار اجرا نباید چیزی را دوبرابر کند. */
    public function test_running_it_twice_changes_nothing(): void
    {
        $this->artisan('catalog:sync', ['--force' => true])->assertSuccessful();

        $products = Product::count();
        $categories = ProductCategory::count();
        $cavities = Product::query()->withCount('cavities')->get()->sum('cavities_count');

        $this->artisan('catalog:sync', ['--force' => true])->assertSuccessful();

        $this->assertSame($products, Product::count());
        $this->assertSame($categories, ProductCategory::count());
        $this->assertSame($cavities, Product::query()->withCount('cavities')->get()->sum('cavities_count'),
            'حفره‌ها باید پیش از ساختِ دوباره پاک شوند، وگرنه هر اجرا چهارتا اضافه می‌کند.');
    }
}
