<?php

namespace Tests\Feature;

use App\Admin\Resources\ProductResource;
use App\Admin\Resources\UserResource;
use App\Models\Document;
use App\Models\Offer;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Support\Media;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * «تکثیر» در پنل — بلوک تیغه‌ای ۸ در دو اندازه، بی پرکردنِ ده‌ها میدان از نو.
 */
class DuplicateRecordTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        Storage::fake('public');
    }

    protected function original(): Product
    {
        return Product::where('slug', 'partition-block-8')->firstOrFail();
    }

    protected function duplicate(Product $product)
    {
        return $this->actingAs($this->admin)->post(ProductResource::duplicateUrl($product));
    }

    protected function latestCopy(): Product
    {
        return Product::query()->latest('id')->firstOrFail();
    }

    public function test_the_copy_carries_every_field_with_fresh_unique_columns(): void
    {
        $original = $this->original();

        $this->duplicate($original)->assertRedirect();

        $copy = $this->latestCopy();

        $this->assertNotSame($original->id, $copy->id);
        $this->assertSame('partition-block-8-2', $copy->slug);
        $this->assertSame($original->sku.'-2', $copy->sku);
        $this->assertSame($original->name.' (کپی)', $copy->name);

        // همه‌ی مشخصات همان است — فقط آنچه فرق دارد باید عوض شود
        foreach (['length_mm', 'width_mm', 'height_mm', 'thickness_mm', 'weight_kg',
            'compressive_strength_mpa', 'thermal_conductivity', 'product_category_id',
            'summary', 'features', 'applications', 'wall_types'] as $column) {
            $this->assertEquals($original->{$column}, $copy->{$column}, "«{$column}» باید کپی شود.");
        }
    }

    /** دو کپیِ یکسان نباید حتی یک لحظه روی سایت کنار هم بنشینند. */
    public function test_the_copy_is_hidden_until_it_is_switched_on(): void
    {
        $this->duplicate($this->original());

        $copy = $this->latestCopy();

        $this->assertFalse($copy->is_active);
        $this->get(route('products.show', $copy))->assertNotFound();
    }

    public function test_it_lands_on_the_edit_page_of_the_copy_with_a_notice(): void
    {
        $response = $this->duplicate($this->original());

        $copy = $this->latestCopy();

        $response->assertRedirect(ProductResource::editUrl($copy));

        $this->actingAs($this->admin)->get(ProductResource::editUrl($copy))
            ->assertOk()
            ->assertSee('ساخته شد', false)
            ->assertSee('«فعال» را بزنید', false);
    }

    /** عددِ آخرِ اسلاگ اندازه‌ی بلوک است، نه پسوندِ کپی — نباید بریده شود. */
    public function test_a_slug_that_ends_in_the_block_size_keeps_it(): void
    {
        $this->duplicate(Product::where('slug', 'ceramic-block-25')->firstOrFail());

        $copy = $this->latestCopy();

        $this->assertSame('ceramic-block-25-2', $copy->slug);
        $this->assertSame('KW-25-2', $copy->sku);
    }

    public function test_a_second_copy_takes_the_next_free_slug(): void
    {
        $original = $this->original();

        $this->duplicate($original);
        $this->duplicate($original);

        $this->assertTrue(Product::where('slug', 'partition-block-8-2')->exists());
        $this->assertTrue(Product::where('slug', 'partition-block-8-3')->exists());
    }

    /** کپی از روی کپی، «-2-2» نمی‌سازد. */
    public function test_copying_a_copy_does_not_stack_suffixes(): void
    {
        $this->duplicate($this->original());
        $this->duplicate($this->latestCopy());

        $this->assertSame('partition-block-8-3', $this->latestCopy()->slug);
    }

    public function test_translations_come_along(): void
    {
        $original = $this->original();
        $original->putTranslations('en', ['name' => 'Partition block 8', 'summary' => 'English summary']);

        $this->duplicate($original);

        $copy = $this->latestCopy();

        $this->assertSame('Partition block 8', $copy->translation('name', 'en'));
        $this->assertSame('English summary', $copy->translation('summary', 'en'));
    }

    public function test_cross_section_points_and_solution_links_come_along(): void
    {
        $original = $this->original();

        $this->duplicate($original);

        $copy = $this->latestCopy();

        $this->assertSame($original->cavities()->count(), $copy->cavities()->count());
        $this->assertGreaterThan(0, $copy->cavities()->count());
        $this->assertEqualsCanonicalizing(
            $original->solutions()->pluck('solutions.id')->all(),
            $copy->solutions()->pluck('solutions.id')->all(),
        );
        $this->assertEqualsCanonicalizing(
            $original->projects()->pluck('projects.id')->all(),
            $copy->projects()->pluck('projects.id')->all(),
        );
    }

    /**
     * دیتاشیت مالِ همان اندازه است و قیمت تصمیمِ فروشنده.
     * هیچ‌کدام نباید با یک کلیکِ مدیر به اندازه‌ی تازه بچسبد.
     */
    public function test_datasheets_and_shop_offers_stay_with_the_original(): void
    {
        $original = $this->original();

        $vendor = Vendor::create(['name' => 'ف', 'slug' => 'v', 'is_active' => true]);
        Offer::create(['vendor_id' => $vendor->id, 'product_id' => $original->id, 'price' => 1000, 'min_order' => 1]);

        Document::query()->first()?->update(['product_id' => $original->id]);

        $offers = $original->offers()->count();
        $documents = $original->documents()->count();
        $this->assertGreaterThan(0, $offers);

        $this->duplicate($original);

        $copy = $this->latestCopy();

        $this->assertSame(0, $copy->offers()->count());
        $this->assertSame(0, $copy->documents()->count());

        // و اصل هم چیزی از دست نداد
        $this->assertSame($offers, $original->offers()->count());
        $this->assertSame($documents, $original->documents()->count());
    }

    /**
     * تصویرِ کپی فایلِ جدای خودش است.
     *
     * جایگزین‌کردنِ تصویر فایلِ قبلی را از دیسک پاک می‌کند؛ اگر کپی و اصل یک
     * فایل داشتند، عوض‌کردنِ عکسِ کپی عکسِ اصل را هم می‌برد.
     */
    public function test_images_are_copied_as_separate_files(): void
    {
        $original = $this->original();
        $path = UploadedFile::fake()->image('block.jpg')->store('products', 'public');
        $gallery = [UploadedFile::fake()->image('g.jpg')->store('products', 'public')];
        $original->update(['hero_image' => $path, 'gallery' => $gallery]);

        $this->duplicate($original);

        $copy = $this->latestCopy();

        $this->assertNotSame($path, $copy->hero_image);
        Storage::disk('public')->assertExists($copy->hero_image);
        $this->assertCount(1, $copy->gallery);
        $this->assertNotSame($gallery[0], $copy->gallery[0]);
        Storage::disk('public')->assertExists($copy->gallery[0]);

        // پاک‌کردنِ فایلِ کپی، فایلِ اصل را دست نمی‌زند
        Storage::disk('public')->delete($copy->hero_image);
        Storage::disk('public')->assertExists($path);
    }

    /**
     * کپی همیشه همان عکسِ اصل را نشان می‌دهد.
     *
     * نسخه‌ی اول هر تصویری را که فایلش روی دیسکِ پنل پیدا نمی‌شد خالی می‌کرد:
     * نشانیِ بیرونی، مسیرِ مطلق، یا فایلی که جای دیگری بود. کپی بی‌عکس — یا با
     * بلوکِ وکتوری — درمی‌آمد و اصل عکس داشت.
     */
    public function test_a_picture_that_is_not_a_panel_file_keeps_the_same_address(): void
    {
        foreach ([
            'https://cdn.example.com/blocks/b8.jpg',
            '/images/products/b8.jpg',
            'products/elsewhere.jpg',      // روی این دیسک نیست
        ] as $path) {
            $original = $this->original();
            $original->update(['hero_image' => $path, 'section_image' => $path, 'gallery' => [$path]]);

            $this->duplicate($original);

            $copy = $this->latestCopy();

            $this->assertSame($path, $copy->hero_image, "عکسِ اصلیِ کپی برای «{$path}»");
            $this->assertSame($path, $copy->section_image);
            $this->assertSame([$path], $copy->gallery);

            // و روی سایت همان عکس دیده می‌شود
            $copy->update(['is_active' => true]);
            $this->get(route('products.show', $copy))->assertOk()
                ->assertSee(e(Media::url($path)), false);
        }
    }

    /** روی سایت، کارت و صفحه‌ی کپی دقیقاً همان عکسِ اصل را دارند. */
    public function test_the_copy_looks_the_same_on_the_site(): void
    {
        $original = $this->original();
        $path = UploadedFile::fake()->image('block.jpg', 800, 500)->store('admin/products', 'public');
        $original->update(['hero_image' => $path]);

        $this->duplicate($original);

        $copy = $this->latestCopy();
        $copy->update(['is_active' => true]);

        $this->assertSame(
            Storage::disk('public')->get($path),
            Storage::disk('public')->get($copy->hero_image),
            'محتوای فایلِ کپی باید همان عکس باشد.'
        );

        $this->get(route('products.show', $copy))->assertOk()
            ->assertSee(e(Media::url($copy->hero_image)), false);
    }

    /**
     * اگر کپیِ فایل شکست بخورد، کپیِ محصول ساخته نمی‌شود.
     *
     * پیش‌تر copy() ِ ناموفق فقط false برمی‌گرداند و کپی به فایلی اشاره
     * می‌کرد که وجود نداشت — عکسِ شکسته، بی هیچ خطایی.
     */
    public function test_a_failed_file_copy_stops_the_whole_duplicate(): void
    {
        $original = $this->original();
        $path = UploadedFile::fake()->image('block.jpg')->store('admin/products', 'public');
        $original->update(['hero_image' => $path]);

        $before = Product::count();

        $disk = \Mockery::mock(Storage::disk('public'))->makePartial();
        $disk->shouldReceive('copy')->andReturn(false);
        Storage::set('public', $disk);

        $this->withoutExceptionHandling();

        try {
            $this->duplicate($original);
            $this->fail('باید خطا می‌داد.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('کپی نشد', $e->getMessage());
        }

        $this->assertSame($before, Product::count());
    }

    public function test_the_button_is_on_the_list_and_the_edit_page(): void
    {
        $original = $this->original();

        $this->actingAs($this->admin)->get('/admin/products')
            ->assertOk()->assertSee(ProductResource::duplicateUrl($original), false);

        $this->actingAs($this->admin)->get(ProductResource::editUrl($original))
            ->assertOk()->assertSee('form="duplicate-record"', false);
    }

    /** منبعی که تکثیر را روشن نکرده — مثل کاربران — نه دکمه دارد نه مسیر. */
    public function test_resources_without_duplication_refuse_it(): void
    {
        $this->actingAs($this->admin)->get('/admin/users')
            ->assertOk()->assertDontSee('/duplicate', false);

        $this->actingAs($this->admin)
            ->post(UserResource::duplicateUrl($this->admin))
            ->assertNotFound();

        $this->assertSame(1, User::where('email', $this->admin->email)->count());
    }

    public function test_a_guest_cannot_duplicate(): void
    {
        $before = Product::count();

        $this->post(ProductResource::duplicateUrl($this->original()))
            ->assertRedirect(route('admin.login'));

        $this->assertSame($before, Product::count());
    }
}
