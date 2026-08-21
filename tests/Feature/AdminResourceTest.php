<?php

namespace Tests\Feature;

use App\Admin\Resources\DocumentResource;
use App\Admin\Resources\FaqResource;
use App\Admin\Resources\ProductResource;
use App\Admin\Resources\UserResource;
use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\Faq;
use App\Models\Product;
use App\Models\User;
use App\Support\Admin\Registry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_every_registered_resource_lists_and_opens_a_form(): void
    {
        foreach (Registry::all() as $resource) {
            $this->actingAs($this->admin)
                ->get('/admin/'.$resource::$slug)
                ->assertOk();

            $record = $resource::$model::first();

            if ($record) {
                $this->actingAs($this->admin)
                    ->get($resource::editUrl($record))
                    ->assertOk();
            }

            if ($resource::$creatable) {
                $this->actingAs($this->admin)
                    ->get('/admin/'.$resource::$slug.'/create')
                    ->assertOk();
            }
        }
    }

    public function test_the_edit_url_uses_the_primary_key_not_the_public_slug(): void
    {
        $product = Product::first();

        // مدل route key اش slug است؛ پنل نباید از آن استفاده کند
        $this->assertSame('slug', $product->getRouteKeyName());
        $this->assertStringEndsWith("/admin/products/{$product->id}/edit", ProductResource::editUrl($product));
    }

    public function test_it_creates_a_record_and_logs_it(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/faqs', [
                'question' => 'پرسش آزمایشی؟',
                'answer' => 'پاسخ آزمایشی برای بررسی ذخیره‌سازی.',
                'group' => 'general',
            ])->assertRedirect();

        $faq = Faq::where('question', 'پرسش آزمایشی؟')->firstOrFail();

        $this->assertDatabaseHas('activity_logs', [
            'event' => 'created',
            'subject_type' => Faq::class,
            'subject_id' => $faq->id,
        ]);
    }

    public function test_an_empty_optional_number_falls_back_to_the_database_default(): void
    {
        // position در دیتابیس NOT NULL با پیش‌فرض صفر است
        $this->actingAs($this->admin)
            ->post('/admin/faqs', [
                'question' => 'بدون ترتیب؟',
                'answer' => 'باید بدون خطا ذخیره شود.',
                'group' => 'general',
                'position' => '',
            ])->assertRedirect();

        $this->assertSame(0, Faq::where('question', 'بدون ترتیب؟')->firstOrFail()->position);
    }

    public function test_it_records_only_the_names_of_changed_fields(): void
    {
        $product = Product::first();

        $this->actingAs($this->admin)
            ->put(ProductResource::updateUrl($product), $this->productPayload($product, ['subtitle' => 'زیرعنوان تازه']))
            ->assertRedirect();

        $log = ActivityLog::where('event', 'updated')->latest()->firstOrFail();

        $this->assertContains('subtitle', $log->changes);
        // مقدارها هرگز در گزارش ذخیره نمی‌شوند
        $this->assertStringNotContainsString('زیرعنوان تازه', json_encode($log->changes, JSON_UNESCAPED_UNICODE));
    }

    public function test_columns_that_are_not_declared_as_fields_cannot_be_written(): void
    {
        $document = Document::first();
        $before = $document->download_count;

        $this->actingAs($this->admin)
            ->put(DocumentResource::updateUrl($document), [
                'title' => $document->title,
                'slug' => $document->slug,
                'category' => $document->category,
                'audience' => $document->audience,
                'format' => $document->format,
                // این ستون فقط readonly اعلام شده — نباید از فرم نوشته شود
                'download_count' => 999999,
            ])->assertRedirect();

        $this->assertSame($before, $document->fresh()->download_count);
    }

    public function test_validation_rejects_an_out_of_range_value(): void
    {
        $product = Product::first();

        $this->actingAs($this->admin)
            ->put(ProductResource::updateUrl($product),
                $this->productPayload($product, ['thermal_score' => 5000]))
            ->assertSessionHasErrors('thermal_score');
    }

    public function test_a_disallowed_file_type_is_rejected(): void
    {
        Storage::fake('public');
        $document = Document::first();

        $this->actingAs($this->admin)
            ->put(DocumentResource::updateUrl($document), [
                'title' => $document->title,
                'slug' => $document->slug,
                'category' => $document->category,
                'audience' => $document->audience,
                'format' => $document->format,
                'file_path' => UploadedFile::fake()->create('payload.php', 12),
            ])->assertSessionHasErrors('file_path');

        $this->assertNull($document->fresh()->file_path);
    }

    public function test_an_allowed_upload_is_stored_with_a_generated_name(): void
    {
        Storage::fake('public');
        $document = Document::first();

        $this->actingAs($this->admin)
            ->put(DocumentResource::updateUrl($document), [
                'title' => $document->title,
                'slug' => $document->slug,
                'category' => $document->category,
                'audience' => $document->audience,
                'format' => $document->format,
                'file_path' => UploadedFile::fake()->create('راهنما.pdf', 40, 'application/pdf'),
            ])->assertRedirect();

        $stored = $document->fresh()->file_path;

        $this->assertNotNull($stored);
        Storage::disk('public')->assertExists($stored);
        // نام فایل کاربر هرگز روی دیسک نمی‌نشیند
        $this->assertStringNotContainsString('راهنما', $stored);
    }

    public function test_an_admin_cannot_delete_their_own_account(): void
    {
        $this->actingAs($this->admin)
            ->delete(UserResource::destroyUrl($this->admin))
            ->assertSessionHasErrors('record');

        $this->assertModelExists($this->admin);
    }

    public function test_an_admin_cannot_strip_their_own_access(): void
    {
        $this->actingAs($this->admin)
            ->put(UserResource::updateUrl($this->admin), [
                'name' => $this->admin->name,
                'email' => $this->admin->email,
                'role' => 'editor',
                'is_active' => '1',
            ])->assertStatus(422);

        $this->assertSame('admin', $this->admin->fresh()->role);
    }

    public function test_deleting_a_record_is_logged(): void
    {
        $faq = Faq::first();
        $label = $faq->question;

        $this->actingAs($this->admin)
            ->delete(FaqResource::destroyUrl($faq))
            ->assertRedirect();

        $this->assertModelMissing($faq);
        $this->assertDatabaseHas('activity_logs', ['event' => 'deleted', 'subject_label' => $label]);
    }

    public function test_list_sorting_ignores_columns_the_resource_did_not_declare(): void
    {
        // اگر ستون دلخواه از URL پذیرفته شود، می‌توان ساختار جدول را حدس زد
        $this->actingAs($this->admin)
            ->get('/admin/products?sort=password&dir=asc')
            ->assertOk();
    }

    public function test_persian_digits_in_a_number_field_are_accepted(): void
    {
        // قلم پنل ارقام را فارسی نشان می‌دهد، پس مدیر هم فارسی تایپ می‌کند.
        $product = Product::first();

        $this->actingAs($this->admin)
            ->put(ProductResource::updateUrl($product), $this->productPayload($product, [
                'fire_resistance_min' => '۱۲۰',
                'thermal_conductivity' => '۰٫۲۸',
            ]))
            ->assertRedirect();

        $product->refresh();

        $this->assertSame(120, $product->fire_resistance_min);
        $this->assertEqualsWithDelta(0.28, (float) $product->thermal_conductivity, 0.0001);
    }

    public function test_a_persian_number_still_fails_when_it_is_out_of_range(): void
    {
        // تبدیل ارقام نباید اعتبارسنجی را دور بزند.
        $product = Product::first();

        $this->actingAs($this->admin)
            ->put(ProductResource::updateUrl($product), $this->productPayload($product, [
                'thermal_score' => '۹۹۹',
            ]))
            ->assertSessionHasErrors('thermal_score');
    }

    public function test_the_product_form_is_split_into_sections(): void
    {
        // فرم ۳۵ فیلدی بدون بخش‌بندی روی موبایل یک ستون بی‌پایان می‌شود.
        $sections = collect(ProductResource::fields())
            ->pluck('section')
            ->unique();

        $this->assertGreaterThan(4, $sections->count());
        $this->assertContains('ابعاد', $sections);
        $this->assertContains(null, $sections, 'فیلدهای شناسه باید در پنل نخستِ بی‌عنوان بمانند.');
    }

    /** بدنه‌ی کامل فرم محصول — چون اعتبارسنجی فیلدهای الزامی را می‌خواهد. */
    protected function productPayload(Product $product, array $overrides = []): array
    {
        return array_merge([
            'name' => $product->name,
            'sku' => $product->sku,
            'slug' => $product->slug,
            'product_category_id' => $product->product_category_id,
            'length_mm' => $product->length_mm,
            'width_mm' => $product->width_mm,
            'height_mm' => $product->height_mm,
            'thickness_mm' => $product->thickness_mm,
            'weight_kg' => $product->weight_kg,
            'compressive_strength_mpa' => $product->compressive_strength_mpa,
            'thermal_conductivity' => $product->thermal_conductivity,
            'water_absorption' => $product->water_absorption,
            'thermal_score' => $product->thermal_score,
            'acoustic_score' => $product->acoustic_score,
            'strength_score' => $product->strength_score,
            'insulation_level' => $product->insulation_level,
        ], $overrides);
    }
}
