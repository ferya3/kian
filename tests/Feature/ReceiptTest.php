<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Vendor;
use App\Support\Jalali;
use App\Support\Locales;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * رسیدِ حواله.
 *
 * درگاهی در کار نیست: مشتری کارت‌به‌کارت می‌کند و عکسِ رسید را بالا می‌دهد.
 * مجوزِ این کار نشانیِ غیرقابل‌حدسِ سفارش است و نه حساب کاربری، پس بیشترِ
 * این تست‌ها درباره‌ی همان مرزند — چه کسی با آن نشانی چه کاری می‌تواند بکند.
 */
class ReceiptTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        Config::set('shop.enabled', true);
        Offer::query()->delete();
        Vendor::query()->delete();

        Route::prefix('{locale}')
            ->where(['locale' => implode('|', Locales::codes())])
            ->middleware(['web', SetLocale::class])
            ->group(base_path('routes/shop.php'));

        Route::getRoutes()->refreshNameLookups();

        Storage::fake('public');
    }

    /** یک سفارش با یک فروشنده، و برمی‌گرداندِ هر دو. */
    protected function order(bool $withBank = true): array
    {
        $product = Product::query()->active()->firstOrFail();

        $vendor = Vendor::create([
            'name' => 'فروشنده آزمایشی',
            'slug' => 'test-vendor',
            'is_active' => true,
        ] + ($withBank ? ['bank_holder' => 'کیان', 'bank_card' => '6104337712345678'] : []));

        $offer = Offer::create([
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'price' => 500,
            'min_order' => 1,
        ]);

        $this->post(route('cart.store'), ['offer' => $offer->id, 'quantity' => 4]);
        $this->post(route('checkout.store'), [
            'customer_name' => 'خریدار',
            'customer_phone' => '۰۹۱۲۰۰۰۰۰۰۰',
        ]);

        return [Order::latest('id')->firstOrFail(), $vendor];
    }

    protected function image(): UploadedFile
    {
        return UploadedFile::fake()->image('receipt.jpg', 600, 900);
    }

    public function test_the_transfer_box_needs_a_bank_account(): void
    {
        [$withAccount] = $this->order(withBank: true);
        $this->get(route('orders.show', $withAccount))
            ->assertOk()
            ->assertSee(__('site.shop.transfer_title'), false)
            // ارقام فارسی، چون همان چیزی است که مشتری می‌بیند
            ->assertSee(Jalali::digits('6104337712345678'), false);

        Vendor::query()->update(['bank_card' => null, 'bank_iban' => null]);

        // بی شماره‌ی حساب، فرم نباید باشد — مشتری نباید رسیدی بفرستد برای جایی که نمی‌داند کجاست
        $this->get(route('orders.show', $withAccount))
            ->assertOk()
            ->assertSee(__('site.shop.transfer_unavailable'), false)
            ->assertDontSee(__('site.shop.receipt_send'), false);
    }

    public function test_a_receipt_takes_its_amount_from_the_order_not_the_form(): void
    {
        [$order, $vendor] = $this->order();

        $this->post(route('orders.receipt', $order), [
            'vendor_id' => $vendor->id,
            'reference' => '۱۲۳۴',
            'image' => $this->image(),
            // اگر مبلغ از فرم خوانده می‌شد، این عدد می‌نشست
            'amount' => 1,
        ])->assertRedirect();

        $receipt = Receipt::firstOrFail();

        $this->assertSame(2000, $receipt->amount, 'مبلغ باید جمعِ ردیف‌های همان فروشنده باشد.');
        $this->assertSame('pending', $receipt->status);
        $this->assertNull($receipt->reviewed_at);
        Storage::disk('public')->assertExists($receipt->image);
    }

    /**
     * فروشنده‌ای که در این سفارش ردیف ندارد، رسید هم نمی‌گیرد.
     *
     * وگرنه هر کسی که نشانیِ سفارش را دارد می‌توانست با دست‌کاریِ vendor_id
     * برای فروشنده‌ای بیگانه رسید بسازد.
     */
    public function test_a_vendor_outside_the_order_is_rejected(): void
    {
        [$order] = $this->order();

        $outsider = Vendor::create(['name' => 'بیگانه', 'slug' => 'outsider', 'is_active' => true]);

        $this->post(route('orders.receipt', $order), [
            'vendor_id' => $outsider->id,
            'image' => $this->image(),
        ])->assertNotFound();

        $this->assertSame(0, Receipt::count());
    }

    public function test_sending_again_replaces_the_row_instead_of_adding_one(): void
    {
        [$order, $vendor] = $this->order();

        $this->post(route('orders.receipt', $order), ['vendor_id' => $vendor->id, 'image' => $this->image()]);
        $first = Receipt::firstOrFail();

        $this->post(route('orders.receipt', $order), ['vendor_id' => $vendor->id, 'image' => $this->image()]);

        $this->assertSame(1, Receipt::count(), 'رسیدِ دوم باید همان ردیف را به‌روز کند.');

        // عکسِ قبلی نباید روی دیسک جا بماند
        Storage::disk('public')->assertMissing($first->image);
    }

    public function test_a_confirmed_receipt_cannot_be_replaced(): void
    {
        [$order, $vendor] = $this->order();

        $this->post(route('orders.receipt', $order), ['vendor_id' => $vendor->id, 'image' => $this->image()]);

        $receipt = Receipt::firstOrFail();
        $receipt->update(['status' => 'confirmed']);

        $this->assertNotNull($receipt->fresh()->reviewed_at, 'تأیید باید مهرِ زمان بزند.');

        $this->post(route('orders.receipt', $order), ['vendor_id' => $vendor->id, 'image' => $this->image()])
            ->assertSessionHasErrors('image');

        $this->assertSame('confirmed', $receipt->fresh()->status);

        // و فرم هم دیگر به مشتری نشان داده نمی‌شود
        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertDontSee(__('site.shop.receipt_send'), false);
    }

    public function test_a_rejected_receipt_shows_the_reason_and_allows_another(): void
    {
        [$order, $vendor] = $this->order();

        $this->post(route('orders.receipt', $order), ['vendor_id' => $vendor->id, 'image' => $this->image()]);

        Receipt::firstOrFail()->update(['status' => 'rejected', 'note' => 'مبلغ نمی‌خواند.']);

        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('مبلغ نمی‌خواند.', false)
            ->assertSee(__('site.shop.receipt_send'), false);

        $this->post(route('orders.receipt', $order), ['vendor_id' => $vendor->id, 'image' => $this->image()])
            ->assertRedirect();

        // فرستادنِ دوباره، حالت را به «در انتظار» برمی‌گرداند و علتِ کهنه را پاک می‌کند
        $receipt = Receipt::firstOrFail();
        $this->assertSame('pending', $receipt->status);
        $this->assertNull($receipt->note);
        $this->assertNull($receipt->reviewed_at);
    }

    public function test_only_images_are_accepted(): void
    {
        [$order, $vendor] = $this->order();

        $this->post(route('orders.receipt', $order), [
            'vendor_id' => $vendor->id,
            'image' => UploadedFile::fake()->create('invoice.pdf', 40, 'application/pdf'),
        ])->assertSessionHasErrors('image');

        $this->assertSame(0, Receipt::count());
    }
}
