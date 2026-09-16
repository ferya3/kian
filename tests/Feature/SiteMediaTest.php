<?php

namespace Tests\Feature;

use App\Admin\Resources\SiteMediaResource;
use App\Models\ProductCategory;
use App\Models\Setting;
use App\Models\SiteMedia;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteMediaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    // --------------------------------------------------------- جایگاه‌ها --

    public function test_every_declared_slot_has_a_row_after_migration(): void
    {
        $this->assertSame(
            count(SiteMedia::SLOTS),
            SiteMedia::query()->whereIn('key', array_keys(SiteMedia::SLOTS))->count()
        );
    }

    /**
     * کلید هیروی هر صفحه باید با نام یک مسیر واقعی بخواند.
     *
     * x-page-hero جایگاهش را از نام مسیر می‌سازد؛ یک غلط تایپی در کلید یعنی
     * جایگاهی که در پنل دیده می‌شود ولی هیچ صفحه‌ای نشانش نمی‌دهد.
     */
    public function test_each_page_hero_slot_points_at_a_real_route(): void
    {
        foreach (SiteMedia::SLOTS as $key => [$label, $section]) {
            if ($section !== 'هیروی صفحات') {
                continue;
            }

            $this->assertTrue(
                Route::has($route = substr($key, strlen('hero.'))),
                "جایگاه «{$label}» به مسیر ناموجود «{$route}» اشاره می‌کند."
            );
        }
    }

    public function test_sync_adds_missing_slots_and_leaves_unknown_rows_alone(): void
    {
        SiteMedia::query()->where('key', 'hero.about')->delete();
        SiteMedia::create(['key' => 'legacy.slot', 'image' => 'admin/site/old.png']);

        SiteMedia::sync();

        $this->assertDatabaseHas('site_media', ['key' => 'hero.about']);
        $this->assertDatabaseHas('site_media', ['key' => 'legacy.slot', 'image' => 'admin/site/old.png']);

        // بار دوم چیزی اضافه نمی‌کند
        $before = SiteMedia::query()->count();
        SiteMedia::sync();
        $this->assertSame($before, SiteMedia::query()->count());
    }

    public function test_a_missing_slot_never_breaks_a_page(): void
    {
        SiteMedia::query()->delete();

        $this->assertNull(SiteMedia::url('hero.about'));
        $this->assertSame('', SiteMedia::alt('hero.about'));
        $this->assertFalse(SiteMedia::has('hero.about'));

        $this->get('/about')->assertOk();
        $this->get('/')->assertOk();
    }

    // ------------------------------------------------------ سایت عمومی --

    public function test_an_uploaded_hero_image_shows_up_on_its_page(): void
    {
        SiteMedia::query()->where('key', 'hero.about')->update([
            'image' => 'admin/site/about.jpg',
            'alt' => 'نمای هوایی کارخانه',
        ]);

        $this->get('/about')
            ->assertOk()
            ->assertSee('admin/site/about.jpg', false)
            ->assertSee('نمای هوایی کارخانه', false);
    }

    public function test_a_page_without_an_uploaded_hero_keeps_the_vector_layout(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertDontSee('admin/site/', false);
    }

    public function test_the_home_hero_can_be_an_image(): void
    {
        SiteMedia::query()->where('key', 'hero.home')->update(['image' => 'admin/site/hero.jpg']);

        $this->get('/')->assertOk()->assertSee('admin/site/hero.jpg', false);
    }

    public function test_the_video_poster_comes_from_the_panel(): void
    {
        Setting::put('hero_video', '/media/factory.mp4');
        SiteMedia::query()->where('key', 'hero.home_poster')->update(['image' => 'admin/site/poster.jpg']);

        $this->get('/')
            ->assertOk()
            ->assertSee('poster="/storage/admin/site/poster.jpg"', false);
    }

    public function test_the_poster_falls_back_to_the_hero_image(): void
    {
        Setting::put('hero_video', '/media/factory.mp4');
        SiteMedia::query()->where('key', 'hero.home')->update(['image' => 'admin/site/hero.jpg']);

        $this->get('/')
            ->assertOk()
            ->assertSee('poster="/storage/admin/site/hero.jpg"', false);
    }

    public function test_an_uploaded_logo_replaces_the_vector_mark(): void
    {
        SiteMedia::query()->where('key', 'brand.logo')->update(['image' => 'admin/site/logo.png']);

        $this->get('/')->assertOk()->assertSee('admin/site/logo.png', false);
    }

    public function test_the_share_image_comes_from_the_panel(): void
    {
        SiteMedia::query()->where('key', 'brand.og')->update(['image' => 'admin/site/og.jpg']);

        $this->get('/')
            ->assertOk()
            ->assertSee('property="og:image" content="'.url('/storage/admin/site/og.jpg').'"', false);
    }

    public function test_an_uploaded_favicon_replaces_the_default(): void
    {
        SiteMedia::query()->where('key', 'brand.favicon')->update(['image' => 'admin/site/icon.png']);

        $this->get('/')
            ->assertOk()
            ->assertSee('rel="icon" href="/storage/admin/site/icon.png"', false)
            ->assertDontSee('/favicon.svg', false);
    }

    public function test_a_filtered_category_lends_its_image_to_the_products_hero(): void
    {
        $category = ProductCategory::query()->whereNotNull('slug')->first();
        $category->update(['image' => 'admin/categories/wall.jpg']);

        $this->get('/products?category='.$category->slug)
            ->assertOk()
            ->assertSee('admin/categories/wall.jpg', false);
    }

    // -------------------------------------------------------------- پنل --

    public function test_an_admin_can_upload_an_image_into_a_slot(): void
    {
        Storage::fake('public');

        $slot = SiteMedia::query()->where('key', 'hero.factory')->firstOrFail();

        $this->actingAs($this->admin)
            ->put(SiteMediaResource::updateUrl($slot), [
                'image' => UploadedFile::fake()->image('factory.jpg'),
                'alt' => 'خط تولید',
            ])
            ->assertRedirect(SiteMediaResource::editUrl($slot));

        $slot->refresh();

        $this->assertNotNull($slot->image);
        $this->assertSame('خط تولید', $slot->alt);
        Storage::disk('public')->assertExists($slot->image);
    }

    public function test_slots_cannot_be_created_or_deleted_from_the_panel(): void
    {
        $slot = SiteMedia::query()->firstOrFail();

        $this->actingAs($this->admin)->get('/admin/site-media/create')->assertNotFound();
        $this->actingAs($this->admin)->post('/admin/site-media', ['alt' => 'x'])->assertNotFound();
        $this->actingAs($this->admin)->delete(SiteMediaResource::destroyUrl($slot))->assertNotFound();

        $this->assertDatabaseHas('site_media', ['key' => $slot->key]);
    }

    /**
     * فیلد فقط‌خواندنی در فرم دیده می‌شود ولی از فرم نوشته نمی‌شود.
     *
     * label و section اصلاً ستون نیستند؛ اگر ذخیره‌سازی فیلترشان نکند، یک فیلد
     * دست‌ساز در فرم کافی است تا ذخیره با خطای SQL بشکند.
     */
    public function test_readonly_fields_are_shown_but_never_saved(): void
    {
        $slot = SiteMedia::query()->where('key', 'hero.contact')->firstOrFail();

        $this->actingAs($this->admin)
            ->get(SiteMediaResource::editUrl($slot))
            ->assertOk()
            ->assertSee($slot->label)
            ->assertSee($slot->placement);

        $this->actingAs($this->admin)
            ->put(SiteMediaResource::updateUrl($slot), [
                'label' => 'دستکاری‌شده',
                'section' => 'دستکاری‌شده',
                'key' => 'brand.logo',
                'alt' => 'متن تازه',
            ])
            ->assertRedirect(SiteMediaResource::editUrl($slot));

        $slot->refresh();

        $this->assertSame('hero.contact', $slot->key);
        $this->assertSame('هیرو تماس', $slot->label);
        $this->assertSame('متن تازه', $slot->alt);
    }

    public function test_the_media_library_reports_slots_without_an_image(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/media')
            ->assertOk()
            ->assertSee(SiteMediaResource::$label);
    }
}
