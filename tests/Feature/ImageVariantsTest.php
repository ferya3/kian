<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Setting;
use App\Models\SiteMedia;
use App\Models\User;
use App\Support\ImageVariants;
use App\Support\Media;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * نسخه‌های سبکِ عکس‌های آپلودشده.
 *
 * عکسِ ۸ مگابایتیِ هیرو، LCP ِ موبایل را به ۴۰ ثانیه می‌رساند. حالا کنارِ
 * هر عکس نسخه‌های WebP در چند پهنا ساخته می‌شود و سایت آن‌ها را می‌فرستد.
 */
class ImageVariantsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
    }

    protected function photo(string $path, int $w = 2400, int $h = 1600): string
    {
        $im = imagecreatetruecolor($w, $h);
        imagefill($im, 0, 0, imagecolorallocate($im, 180, 85, 45));
        ob_start();
        imagejpeg($im, null, 90);
        $bytes = ob_get_clean();
        Storage::disk('public')->put($path, $bytes);

        return $path;
    }

    public function test_variants_are_made_in_every_width_up_to_the_original(): void
    {
        $path = $this->photo('admin/site-media/hero.jpg');

        $this->assertSame(6, ImageVariants::generate($path));

        foreach ([480, 768, 1080, 1366, 1600, 1920] as $w) {
            $file = ImageVariants::pathFor($path, $w);
            Storage::disk('public')->assertExists($file);
            $this->assertSame($w, getimagesize(Storage::disk('public')->path($file))[0]);
        }

        // دوباره چیزی نمی‌سازد
        $this->assertSame(0, ImageVariants::generate($path));
    }

    /** عکسِ کوچک بزرگ نمی‌شود؛ ولی نخستین پهنای بزرگ‌تر به اندازه‌ی خودش ساخته می‌شود. */
    public function test_a_small_image_is_not_upscaled(): void
    {
        $path = $this->photo('admin/x/small.jpg', 1000, 700);

        ImageVariants::generate($path);

        $this->assertSame([480, 768, 1080], array_keys(ImageVariants::existing($path)));
        $this->assertSame(1000, getimagesize(Storage::disk('public')->path(ImageVariants::pathFor($path, 1080)))[0]);
    }

    public function test_the_site_serves_the_variant_with_a_srcset(): void
    {
        $path = $this->photo('admin/site-media/hero.jpg');
        ImageVariants::generate($path);
        SiteMedia::query()->where('key', 'hero.home')->update(['image' => $path]);
        Cache::forget('site_media.map');

        $url = Media::url($path);
        $this->assertStringEndsWith('variants/hero-1920w.webp', $url);
        $this->assertStringContainsString('hero-480w.webp 480w', Media::srcset($url));

        $this->get(route('home'))->assertOk()
            ->assertSee('variants/hero-1920w.webp', false)
            // پیش‌بارگذاری در head، با همان srcset
            ->assertSee('<link rel="preload" as="image" href="/storage/admin/site-media/variants/hero-1920w.webp" fetchpriority="high"', false)
            ->assertSee('sizes="100vw"', false)
            ->assertDontSee('/storage/admin/site-media/hero.jpg"', false);
    }

    /** پیش از بهینه‌سازی، همان عکسِ اصل — بی srcset و بی خطا. */
    public function test_an_image_without_variants_falls_back_to_the_original(): void
    {
        $path = $this->photo('admin/site-media/raw.jpg');

        $this->assertStringEndsWith('admin/site-media/raw.jpg', Media::url($path));
        $this->assertNull(Media::srcset(Media::url($path)));
    }

    public function test_an_upload_from_the_panel_gets_variants_and_the_old_ones_go(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $media = SiteMedia::query()->where('key', 'hero.home')->firstOrFail();

        $old = $this->photo('admin/site-media/old.jpg');
        ImageVariants::generate($old);
        $media->forceFill(['image' => $old])->save();

        $this->actingAs($admin)->put(route('admin.resource.update', ['resource' => 'site-media', 'id' => $media->id]), [
            'image' => UploadedFile::fake()->image('new.jpg', 2000, 1200),
            'alt' => 'کارخانه',
        ])->assertSessionHasNoErrors();

        $new = $media->fresh()->image;
        $this->assertNotSame($old, $new);
        $this->assertNotEmpty(ImageVariants::existing($new));
        Storage::disk('public')->assertMissing(ImageVariants::pathFor($old, 480));
    }

    public function test_the_optimize_command_covers_existing_uploads(): void
    {
        $a = $this->photo('admin/projects/a.jpg', 1600, 900);
        $this->photo('admin/projects/b.png', 900, 600);

        $this->artisan('media:optimize')->expectsOutputToContain('2 عکس')->assertSuccessful();

        $this->assertNotEmpty(ImageVariants::existing($a));
        // نسخه‌ها خودشان دوباره نسخه نمی‌گیرند
        $this->assertEmpty(collect(Storage::disk('public')->allFiles('admin'))->filter(fn ($f) => str_contains($f, 'variants/variants')));
    }

    public function test_variants_are_not_listed_as_orphans_in_the_media_library(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $path = $this->photo('admin/site-media/lonely.jpg');
        ImageVariants::generate($path);

        $this->actingAs($admin)->get(route('admin.media'))->assertOk()->assertDontSee('lonely-480w.webp', false);
    }

    /** نسخه‌های نسلِ پیشین (بی «w»، کیفیتِ دیگر) و نسخه‌ی عکسِ حذف‌شده پاک می‌شوند. */
    public function test_the_optimize_command_removes_stale_variants(): void
    {
        $path = $this->photo('admin/site-media/kept.jpg', 1000, 600);
        Storage::disk('public')->put('admin/site-media/variants/kept-768.webp', 'old generation');
        Storage::disk('public')->put('admin/site-media/variants/gone-480w.webp', 'original deleted');

        $this->artisan('media:optimize')->expectsOutputToContain('2 نسخه‌ی کهنه پاک شد')->assertSuccessful();

        Storage::disk('public')->assertMissing('admin/site-media/variants/kept-768.webp');
        Storage::disk('public')->assertMissing('admin/site-media/variants/gone-480w.webp');
        Storage::disk('public')->assertExists(ImageVariants::pathFor($path, 480));
    }

    /** ویدئوی هیرو منبع ندارد تا app.js پس از load و فقط روی دسکتاپ بدهد. */
    public function test_the_hero_video_does_not_load_with_the_page(): void
    {
        Setting::put('hero_video', '/storage/admin/hero.mp4');
        Cache::flush();

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('data-hero-video="/storage/admin/hero.mp4"', $html);
        $this->assertStringContainsString('preload="none"', $html);
        $this->assertStringNotContainsString('<source src="/storage/admin/hero.mp4"', $html);
        $this->assertStringNotContainsString(' autoplay', str($html)->betweenFirst('<video', '</video>')->toString());
    }

    /**
     * پیش‌نمایشِ لینک و favicon فایلِ اصل را می‌گیرند، نه WebP — و نشانیِ درست.
     *
     * og:image ِ محصول پیش‌تر ‎/admin/products/… بود (بی ‎/storage) و هیچ‌جا
     * باز نمی‌شد.
     */
    public function test_share_image_and_favicon_use_the_original_file(): void
    {
        $photo = $this->photo('admin/products/hero.jpg');
        ImageVariants::generate($photo);
        $product = Product::query()->where('slug', 'ceramic-block-20')->firstOrFail();
        $product->forceFill(['hero_image' => $photo])->save();

        $icon = $this->photo('admin/site-media/icon.png', 512, 512);
        ImageVariants::generate($icon);
        SiteMedia::query()->where('key', 'brand.favicon')->update(['image' => $icon]);
        Cache::forget('site_media.map');

        $html = $this->get(route('products.show', $product))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('~<meta property="og:image" content="https?://[^"]+/storage/admin/products/hero\.jpg">~', $html);
        $this->assertStringContainsString('<link rel="icon" href="/storage/admin/site-media/icon.png">', $html);
        // ولی خودِ صفحه نسخه‌ی سبک را نشان می‌دهد
        $this->assertStringContainsString('variants/hero-', $html);
    }
}
