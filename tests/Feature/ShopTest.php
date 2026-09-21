<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Support\Admin\Registry;
use App\Support\Cart;
use App\Support\Locales;
use App\Support\Navigation;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * فروشگاه چندفروشنده‌ای.
 *
 * نیمه‌ی اول این تست‌ها درباره‌ی «خاموش» است و نه «روشن» — چون فروشگاه قرار
 * است مدتی خاموش بماند و خاموشیِ نیم‌بند بدتر از نبودن است: نشانیِ نیمه‌کاره‌ای
 * که موتور جستجو ایندکسش کند، یا منبعی در پنل که مدیر رویش کار کند و اثری
 * روی سایت نبیند.
 */
class ShopTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * فروشگاه را برای این تست روشن می‌کند — پیکربندی و مسیرها با هم.
     *
     * مسیرها هنگام بوت ثبت شده‌اند و با کلیدِ خاموش، مسیرهای فروشگاه در
     * فهرست نیستند؛ پس تغییرِ config به‌تنهایی کافی نیست. مسیرها دقیقاً با
     * همان پیشوند و middleware ثبت می‌شوند که routes/web.php ثبت می‌کند،
     * تا تست چیزی را بیازماید که واقعاً اجرا می‌شود.
     */
    protected function enableShop(): void
    {
        $this->shopIsOn();

        Route::prefix('{locale}')
            ->where(['locale' => implode('|', Locales::codes())])
            ->middleware(['web', SetLocale::class])
            ->group(base_path('routes/shop.php'));

        Route::getRoutes()->refreshNameLookups();
    }

    /** فقط پیکربندی — برای آنچه به مسیر کاری ندارد: منو و منابع پنل. */
    protected function shopIsOn(): void
    {
        Config::set('shop.enabled', true);
    }

    // ------------------------------------------------------------ خاموش --

    public function test_the_shop_is_off_by_default(): void
    {
        $this->assertFalse(config('shop.enabled'));
    }

    public function test_no_shop_route_exists_while_it_is_off(): void
    {
        foreach (['shop.index', 'cart.show', 'checkout.show', 'orders.show'] as $name) {
            $this->assertFalse(Route::has($name), "مسیر «{$name}» نباید وجود داشته باشد.");
        }
    }

    public function test_shop_urls_are_plain_404_while_it_is_off(): void
    {
        foreach (['/fa/shop', '/fa/cart', '/fa/checkout'] as $path) {
            $this->get($path)->assertNotFound();
        }
    }

    public function test_the_menu_has_no_shop_link_while_it_is_off(): void
    {
        $this->assertNotContains('فروشگاه', array_column(Navigation::items(), 'label'));

        $this->get(route('home'))->assertOk()->assertDontSee('/fa/shop', false);
    }

    public function test_the_panel_has_no_shop_resource_while_it_is_off(): void
    {
        foreach (['vendors', 'offers', 'orders'] as $slug) {
            $this->assertNull(Registry::find($slug), "منبع «{$slug}» نباید در پنل باشد.");
        }

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)->get('/admin/offers')->assertNotFound();
    }

    /** جدول‌ها اما ساخته می‌شوند — روز فعال‌سازی نباید مهاجرت لازم باشد. */
    public function test_the_tables_exist_even_though_the_shop_is_off(): void
    {
        foreach (['vendors', 'offers', 'orders', 'order_items'] as $table) {
            $this->assertTrue(\Schema::hasTable($table), "جدول «{$table}» ساخته نشده.");
        }

        $this->assertTrue(\Schema::hasColumn('users', 'vendor_id'));
    }

    // ------------------------------------------------------------- روشن --

    public function test_the_menu_gains_a_shop_link_when_it_is_on(): void
    {
        $this->enableShop();

        $this->assertContains('فروشگاه', array_column(Navigation::items(), 'label'));
    }

    public function test_the_panel_gains_the_shop_resources_when_it_is_on(): void
    {
        $this->shopIsOn();

        foreach (['vendors', 'offers', 'orders'] as $slug) {
            $this->assertNotNull(Registry::find($slug), "منبع «{$slug}» باید در پنل باشد.");
        }
    }

    public function test_the_storefront_lists_only_products_that_have_a_sellable_offer(): void
    {
        $this->enableShop();

        [$sold, $unsold] = Product::query()->active()->take(2)->get()->all();
        $vendor = $this->vendor();

        Offer::create(['vendor_id' => $vendor->id, 'product_id' => $sold->id, 'price' => 120000, 'min_order' => 2]);

        $this->get(route('shop.index'))
            ->assertOk()
            ->assertSee($sold->name, false)
            ->assertDontSee($unsold->name, false);
    }

    public function test_an_offer_disappears_when_its_vendor_is_switched_off(): void
    {
        $this->enableShop();

        $product = Product::query()->active()->firstOrFail();
        $vendor = $this->vendor();
        Offer::create(['vendor_id' => $vendor->id, 'product_id' => $product->id, 'price' => 90000]);

        $this->get(route('shop.show', $product))->assertOk()->assertSee($vendor->name, false);

        $vendor->update(['is_active' => false]);

        // بدون فروشنده‌ی فعال، صفحه‌ی خرید اصلاً وجود ندارد
        $this->get(route('shop.show', $product))->assertNotFound();
    }

    public function test_a_basket_becomes_an_order_split_by_vendor(): void
    {
        $this->enableShop();

        $product = Product::query()->active()->firstOrFail();
        $one = $this->vendor('فروشنده یک', 'v-one');
        $two = $this->vendor('فروشنده دو', 'v-two');

        $offerOne = Offer::create(['vendor_id' => $one->id, 'product_id' => $product->id, 'price' => 100, 'min_order' => 1]);
        $offerTwo = Offer::create(['vendor_id' => $two->id, 'product_id' => $product->id, 'price' => 250, 'min_order' => 1]);

        $this->post(route('cart.store'), ['offer' => $offerOne->id, 'quantity' => 3])->assertRedirect();
        $this->post(route('cart.store'), ['offer' => $offerTwo->id, 'quantity' => 2])->assertRedirect();

        $this->post(route('checkout.store'), [
            'customer_name' => 'سازنده',
            'customer_phone' => '۰۹۱۲۰۰۰۰۰۰۰',
        ])->assertRedirect();

        $order = Order::query()->latest('id')->firstOrFail();

        $this->assertSame(3 * 100 + 2 * 250, $order->total);
        $this->assertSame(2, $order->items()->count());
        $this->assertEqualsCanonicalizing(
            [$one->id, $two->id],
            $order->items->pluck('vendor_id')->all()
        );

        // سبد پس از ثبت خالی می‌شود، وگرنه دوباره‌فرستادن فرم سفارش دوم می‌سازد
        $this->get(route('cart.show'))->assertOk()->assertSee('سبد خالی است.', false);
    }

    /**
     * قیمت از دیتابیس می‌آید و نه از فرم.
     *
     * بدون این، فرستادنِ یک قیمتِ دلخواه در فرمِ تسویه کافی بود تا سفارش با
     * همان مبلغ ثبت شود.
     */
    public function test_the_price_comes_from_the_database_not_the_form(): void
    {
        $this->enableShop();

        $product = Product::query()->active()->firstOrFail();
        $offer = Offer::create(['vendor_id' => $this->vendor()->id, 'product_id' => $product->id, 'price' => 500]);

        $this->post(route('cart.store'), ['offer' => $offer->id, 'quantity' => 2]);

        $this->post(route('checkout.store'), [
            'customer_name' => 'سازنده',
            'customer_phone' => '۰۹۱۲۰۰۰۰۰۰۰',
            'total' => 1,
            'unit_price' => 1,
        ]);

        $this->assertSame(1000, Order::query()->latest('id')->firstOrFail()->total);
    }

    public function test_the_quantity_never_falls_below_the_minimum_order(): void
    {
        $this->enableShop();

        $product = Product::query()->active()->firstOrFail();
        $offer = Offer::create([
            'vendor_id' => $this->vendor()->id, 'product_id' => $product->id,
            'price' => 100, 'min_order' => 10,
        ]);

        $this->post(route('cart.store'), ['offer' => $offer->id, 'quantity' => 1]);

        $this->assertSame(10, Cart::raw()[$offer->id]);
    }

    // ------------------------------------------------- فروشنده در پنل --

    public function test_a_vendor_sees_only_their_own_offers(): void
    {
        $this->enableShop();

        [$myProduct, $theirProduct] = Product::query()->active()->take(2)->get()->all();
        $mine = $this->vendor('مال من', 'mine');
        $theirs = $this->vendor('مال دیگری', 'theirs');

        $myOffer = Offer::create(['vendor_id' => $mine->id, 'product_id' => $myProduct->id, 'price' => 100]);
        $theirOffer = Offer::create(['vendor_id' => $theirs->id, 'product_id' => $theirProduct->id, 'price' => 200]);

        $seller = User::factory()->create([
            'role' => 'vendor', 'is_active' => true, 'vendor_id' => $mine->id,
        ]);

        // ستونِ فروشنده برای فروشنده پنهان است، پس محصول ملاکِ تشخیص است
        $this->actingAs($seller)->get('/admin/offers')
            ->assertOk()
            ->assertSee($myProduct->name, false)
            ->assertDontSee($theirProduct->name, false);

        // و با حدسِ نشانی هم به عرضه‌ی دیگری نمی‌رسد
        $this->actingAs($seller)->get("/admin/offers/{$theirOffer->id}/edit")->assertNotFound();
        $this->actingAs($seller)->get("/admin/offers/{$myOffer->id}/edit")->assertOk();
    }

    public function test_a_vendor_cannot_file_an_offer_under_another_shop(): void
    {
        $this->enableShop();

        $product = Product::query()->active()->firstOrFail();
        $mine = $this->vendor('مال من', 'mine');
        $theirs = $this->vendor('مال دیگری', 'theirs');

        $seller = User::factory()->create([
            'role' => 'vendor', 'is_active' => true, 'vendor_id' => $mine->id,
        ]);

        $this->actingAs($seller)->post('/admin/offers', [
            'vendor_id' => $theirs->id,
            'product_id' => $product->id,
            'price' => 700,
            'unit' => 'عدد',
            'min_order' => 1,
            'is_active' => 1,
        ])->assertRedirect();

        $this->assertSame($mine->id, Offer::query()->latest('id')->firstOrFail()->vendor_id);
    }

    public function test_a_vendor_cannot_reach_the_rest_of_the_panel(): void
    {
        $this->enableShop();

        $seller = User::factory()->create([
            'role' => 'vendor', 'is_active' => true, 'vendor_id' => $this->vendor()->id,
        ]);

        foreach (['products', 'articles', 'users', 'vendors', 'settings'] as $slug) {
            $this->actingAs($seller)->get("/admin/{$slug}")->assertForbidden();
        }
    }

    public function test_a_vendor_cannot_reach_the_dashboard_or_the_media_library(): void
    {
        $this->enableShop();

        $seller = User::factory()->create([
            'role' => 'vendor', 'is_active' => true, 'vendor_id' => $this->vendor()->id,
        ]);

        // داشبورد ۴۰۳ نمی‌دهد چون صفحه‌ی اولِ پس از ورود است — به کار خودش می‌رود
        $this->actingAs($seller)->get('/admin')
            ->assertRedirect(route('admin.resource.index', 'offers'));

        $this->actingAs($seller)->get('/admin/media')->assertForbidden();
    }

    public function test_a_vendor_sees_only_their_own_order_lines(): void
    {
        $this->enableShop();

        [$mineProduct, $theirProduct] = Product::query()->active()->take(2)->get()->all();
        $mine = $this->vendor('مال من', 'mine');
        $theirs = $this->vendor('مال دیگری', 'theirs');

        $order = Order::create([
            'number' => 'X-1', 'token' => 'tok-1', 'customer_name' => 'خریدار',
            'customer_phone' => '۰۹۱۲۰۰۰۰۰۰۰', 'total' => 300, 'status' => 'new',
        ]);

        $order->items()->create([
            'vendor_id' => $mine->id, 'product_id' => $mineProduct->id,
            'product_name' => $mineProduct->name, 'vendor_name' => $mine->name,
            'unit_price' => 100, 'quantity' => 1, 'total' => 100, 'status' => 'new',
        ]);
        $order->items()->create([
            'vendor_id' => $theirs->id, 'product_id' => $theirProduct->id,
            'product_name' => $theirProduct->name, 'vendor_name' => $theirs->name,
            'unit_price' => 200, 'quantity' => 1, 'total' => 200, 'status' => 'new',
        ]);

        $seller = User::factory()->create([
            'role' => 'vendor', 'is_active' => true, 'vendor_id' => $mine->id,
        ]);

        $this->actingAs($seller)->get('/admin/sales')
            ->assertOk()
            ->assertSee($mineProduct->name, false)
            ->assertDontSee($theirProduct->name, false)
            // تلفن مشتری لازم است — کلِ چرخه همین تماس است
            ->assertSee('۰۹۱۲۰۰۰۰۰۰۰', false);
    }

    /** مدیر کل سفارش‌های کامل را می‌بیند، فروشنده فقط ردیف‌های خودش. */
    public function test_the_two_order_views_belong_to_different_people(): void
    {
        $this->enableShop();

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $seller = User::factory()->create([
            'role' => 'vendor', 'is_active' => true, 'vendor_id' => $this->vendor()->id,
        ]);

        $this->actingAs($admin)->get('/admin/orders')->assertOk();
        $this->actingAs($admin)->get('/admin/sales')->assertForbidden();

        $this->actingAs($seller)->get('/admin/orders')->assertForbidden();
        $this->actingAs($seller)->get('/admin/sales')->assertOk();
    }

    protected function vendor(string $name = 'فروشنده آزمایشی', string $slug = 'test-vendor'): Vendor
    {
        return Vendor::create([
            'name' => $name,
            'slug' => $slug,
            'city' => 'اصفهان',
            'province' => 'اصفهان',
            'is_active' => true,
        ]);
    }
}
