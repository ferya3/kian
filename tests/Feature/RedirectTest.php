<?php

namespace Tests\Feature;

use App\Models\Redirect;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * نشانی‌های سایتِ وردپرسیِ قبلی روی همین دامنه.
 *
 * گوگل آن‌ها را ایندکس کرده؛ هر کدام که ۴۰۴ بدهد، اعتبارش در جستجو و
 * بازدیدکننده‌اش از دست می‌رود.
 */
class RedirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    protected function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    /** نامکِ فارسیِ وردپرس درصدی‌رمز است؛ با اسلش و بی آن، یک نشانی‌اند. */
    public function test_normalize_reaches_one_key_for_every_spelling(): void
    {
        $encoded = '/'.rawurlencode('محصولات').'/'.rawurlencode('بلوک-سفالی').'/';

        $this->assertSame('/محصولات/بلوک-سفالی', Redirect::normalize($encoded));
        $this->assertSame('/محصولات/بلوک-سفالی', Redirect::normalize('https://www.kiyanbehsaz.com'.$encoded.'?utm_source=x'));
        $this->assertSame('/about-us', Redirect::normalize('/About-Us/'));
        $this->assertSame('/?p=12', Redirect::normalize('/?utm_medium=a&p=12'));
        $this->assertSame('/', Redirect::normalize('/?utm_source=google'));
    }

    public function test_an_old_persian_address_is_redirected_permanently(): void
    {
        Redirect::create(['from_path' => '/تماس-با-ما', 'to_url' => '/fa/contact']);

        $this->get('/'.rawurlencode('تماس-با-ما').'/')
            ->assertStatus(301)
            ->assertRedirect('/fa/contact');

        $this->assertSame(1, Redirect::first()->hits);
    }

    /** ‎/?p=12 در وردپرس یک نوشته بود — پارامتر روی ریشه خودِ نشانی است. */
    public function test_a_wordpress_query_address_on_the_root_is_redirected(): void
    {
        Redirect::create(['from_path' => '/?p=12', 'to_url' => '/fa/articles']);

        $this->get('/?p=12')->assertRedirect('/fa/articles');
        $this->get('/')->assertRedirect(); // ریشه همچنان به زبانِ بازدیدکننده می‌رود
        $this->assertStringNotContainsString('articles', $this->get('/')->headers->get('Location'));
    }

    public function test_wordpress_internals_are_gone_not_missing(): void
    {
        foreach (['/wp-admin/', '/wp-login.php', '/xmlrpc.php', '/feed/', '/wp-content/uploads/2023/05/a.jpg', '/some-post/feed/'] as $path) {
            $this->get($path)->assertStatus(410);
        }

        $this->assertSame(0, Redirect::count(), 'ساختارِ وردپرس نباید جدول را پر کند.');
    }

    /** هر ۴۰۴ شمرده می‌شود تا مدیر بداند کدام نشانی‌های قدیم هنوز بازدید دارند. */
    public function test_an_unknown_address_is_logged_with_its_hits(): void
    {
        $this->get('/old-page/')->assertNotFound();
        $this->get('/old-page')->assertNotFound();

        $row = Redirect::where('from_path', '/old-page')->firstOrFail();
        $this->assertSame(2, $row->hits);
        $this->assertSame('404', $row->source);
        $this->assertNull($row->to_url);

        // مدیر مقصد می‌دهد و از همان لحظه ریدایرکت می‌شود
        $row->update(['to_url' => '/fa/about']);
        $this->get('/old-page')->assertRedirect('/fa/about');
    }

    public function test_bot_scans_and_site_files_are_not_logged(): void
    {
        foreach (['/.env', '/phpmyadmin/index.php', '/build/missing.js', '/storage/gone.png', '/admin/nope'] as $path) {
            $this->get($path);
        }

        $this->assertSame(0, Redirect::count());
    }

    public function test_live_pages_are_untouched(): void
    {
        $this->get('/fa')->assertOk();
        $this->get('/fa/products')->assertOk();
        $this->assertSame(0, Redirect::count());
    }

    public function test_a_410_rule_from_the_panel_is_honoured(): void
    {
        Redirect::create(['from_path' => '/old-promo', 'status_code' => 410]);

        $this->get('/old-promo')->assertStatus(410);
    }

    public function test_an_inactive_rule_does_nothing(): void
    {
        Redirect::create(['from_path' => '/x', 'to_url' => '/fa', 'is_active' => false]);

        $this->get('/x')->assertNotFound();
    }

    public function test_guess_maps_old_pages_to_the_new_ones(): void
    {
        $this->assertSame('/fa/products/ceramic-block-20', Redirect::guess('/محصولات/بلوک-سفالی-۲۰'));
        $this->assertSame('/fa/products/partition-block-8', Redirect::guess('/product/block-8cm'));
        $this->assertSame('/fa/products', Redirect::guess('/محصولات'));
        $this->assertSame('/fa/contact', Redirect::guess('/تماس-با-ما'));
        $this->assertSame('/fa/about', Redirect::guess('/درباره-ما'));
        $this->assertSame('/fa/articles', Redirect::guess('/2023/05/some-news'));
        $this->assertSame('/fa', Redirect::guess('/something-else'));
    }

    public function test_the_panel_normalises_and_rejects_duplicates_and_loops(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/redirects', [
            'from_path' => 'https://kiyanbehsaz.com/'.rawurlencode('درباره-ما').'/',
            'to_url' => 'https://kiyanbehsaz.com/fa/about',
            'status_code' => 301, 'is_active' => 1,
        ])->assertSessionHasNoErrors();

        $row = Redirect::firstOrFail();
        $this->assertSame('/درباره-ما', $row->from_path);

        $this->actingAs($admin)->post('/admin/redirects', [
            'from_path' => '/درباره-ما/', 'to_url' => '/fa', 'status_code' => 301, 'is_active' => 1,
        ])->assertSessionHasErrors('from_path');

        $this->actingAs($admin)->post('/admin/redirects', [
            'from_path' => '/loop', 'to_url' => '/loop/', 'status_code' => 301, 'is_active' => 1,
        ])->assertSessionHasErrors('to_url');

        $this->actingAs($admin)->get('/admin/redirects')->assertOk()->assertSee('/درباره-ما');
    }

    public function test_import_from_the_web_archive(): void
    {
        Http::fake(['web.archive.org/*' => Http::response([
            ['original'],
            ['http://kiyanbehsaz.com/'],
            ['https://kiyanbehsaz.com/%D8%AA%D9%85%D8%A7%D8%B3-%D8%A8%D8%A7-%D9%85%D8%A7/'],
            ['https://www.kiyanbehsaz.com/product/ceramic-block-25/'],
            ['https://kiyanbehsaz.com/wp-content/uploads/a.jpg'],
            ['https://kiyanbehsaz.com/feed/'],
        ])]);

        // ردیفی که مدیر دستی گذاشته، دست نمی‌خورد
        Redirect::create(['from_path' => '/product/ceramic-block-25', 'to_url' => '/fa/products']);

        $this->artisan('redirects:import', ['--archive' => 'kiyanbehsaz.com'])->assertSuccessful();

        $this->assertSame('/fa/contact', Redirect::where('from_path', '/تماس-با-ما')->value('to_url'));
        $this->assertSame('/fa/products', Redirect::where('from_path', '/product/ceramic-block-25')->value('to_url'));
        $this->assertSame(2, Redirect::count(), 'ریشه، عکس و فید وارد نمی‌شوند.');

        Http::assertSent(fn ($r) => substr_count($r->url(), 'filter=') === 2);
    }

    public function test_import_from_a_file_with_explicit_targets(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'urls');
        file_put_contents($file, "URL,Clicks\nhttps://kiyanbehsaz.com/old-a/,12\n/old-b /fa/factory\n");

        $this->get('/old-a'); // ثبت‌شده از ۴۰۴، بی مقصد

        $this->artisan('redirects:import', ['file' => $file])->assertSuccessful();

        $this->assertSame('/fa', Redirect::where('from_path', '/old-a')->value('to_url'));
        $this->assertSame('/fa/factory', Redirect::where('from_path', '/old-b')->value('to_url'));
        $this->get('/old-b')->assertRedirect('/fa/factory');

        unlink($file);
    }
}
