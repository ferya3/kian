<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Product;
use App\Models\User;
use App\Support\Locales;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    // ------------------------------------------------------------ مسیرها --

    public function test_every_enabled_locale_serves_the_home_page(): void
    {
        foreach (Locales::codes() as $code) {
            $this->get("/{$code}")->assertOk();
        }
    }

    public function test_an_unknown_locale_is_not_a_locale(): void
    {
        // «de» زبان سایت نیست، پس باید مثل یک نشانی ناموجود رفتار شود
        $this->get('/de')->assertNotFound();
    }

    /** زبانی که سایت ندارد، به پیش‌فرض می‌رسد و نه به ۴۰۴. */
    public function test_the_root_falls_back_when_no_language_matches(): void
    {
        $this->get('/', ['Accept-Language' => 'de-DE,de;q=0.9'])
            ->assertRedirect(route('home', ['locale' => Locales::default()]));
    }

    public function test_the_root_follows_the_browser_language(): void
    {
        $this->get('/', ['Accept-Language' => 'en-GB,en;q=0.9'])
            ->assertRedirect(route('home', ['locale' => 'en']));
    }

    public function test_a_remembered_choice_beats_the_browser_language(): void
    {
        $this->withCookie(Locales::COOKIE, 'ar')
            ->get('/', ['Accept-Language' => 'en-GB,en;q=0.9'])
            ->assertRedirect(route('home', ['locale' => 'ar']));
    }

    /** نشانی‌های پیش از چندزبانه‌شدن نباید ۴۰۴ بدهند. */
    public function test_an_old_unprefixed_url_moves_to_the_default_locale(): void
    {
        $this->get('/products')->assertRedirect('/'.Locales::default().'/products');
        $this->get('/technical/faq')->assertRedirect('/'.Locales::default().'/technical/faq');
    }

    public function test_a_path_that_exists_in_no_locale_is_still_missing(): void
    {
        $this->get('/no-such-page')->assertNotFound();
    }

    /**
     * مهم‌ترین تستِ مسیرها.
     *
     * ویوها route('products.index') صدا می‌زنند و هیچ‌کدام زبان را پاس
     * نمی‌دهند؛ URL::defaults باید آن را پر کند. اگر این بشکند، هر پیوند
     * سایت به فارسی برمی‌گردد و کاربر انگلیسی در اولین کلیک بیرون می‌افتد.
     */
    public function test_links_stay_inside_the_current_language(): void
    {
        $this->get('/en/products')
            ->assertOk()
            ->assertSee('/en/product-finder', false)
            ->assertDontSee('"/fa/product-finder"', false);
    }

    // ------------------------------------------------------- صفحه و سئو --

    public function test_the_page_carries_the_language_direction_and_font(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('<html lang="en" dir="ltr" data-font="inter"', false);

        $this->get('/ar')
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl" data-font="vazirmatn"', false);
    }

    public function test_every_page_points_at_its_other_languages(): void
    {
        $page = $this->get('/en/products')->assertOk();

        foreach (Locales::codes() as $code) {
            $page->assertSee('hreflang="'.Locales::html($code).'"', false);
            $page->assertSee('/'.$code.'/products"', false);
        }

        $page->assertSee('hreflang="x-default"', false);
    }

    public function test_the_sitemap_lists_every_language_with_its_alternates(): void
    {
        $sitemap = $this->get(route('sitemap'))->assertOk();

        foreach (Locales::codes() as $code) {
            $sitemap->assertSee('<loc>'.route('home', ['locale' => $code]).'</loc>', false);
        }

        $sitemap->assertSee('xhtml:link', false);
    }

    public function test_the_switcher_keeps_you_on_the_same_page(): void
    {
        $product = Product::query()->active()->firstOrFail();

        $this->get(route('products.show', ['locale' => 'fa', 'product' => $product]))
            ->assertOk()
            ->assertSee(route('products.show', ['locale' => 'en', 'product' => $product]), false);
    }

    // -------------------------------------------------- ترجمه‌ی محتوا --

    public function test_a_translated_field_replaces_the_default_one(): void
    {
        $faq = Faq::query()->firstOrFail();
        $faq->putTranslations('en', ['question' => 'How thick should the wall be?']);

        app()->setLocale('en');
        $this->assertSame('How thick should the wall be?', $faq->fresh()->question);

        app()->setLocale('fa');
        $this->assertNotSame('How thick should the wall be?', $faq->fresh()->question);
    }

    /** سایتِ نیمه‌ترجمه باید کار کند، نه اینکه جای متن خالی بماند. */
    public function test_an_untranslated_field_falls_back_to_the_default(): void
    {
        $faq = Faq::query()->firstOrFail();
        $persian = $faq->question;

        $faq->putTranslations('en', ['answer' => 'Only the answer is translated.']);

        app()->setLocale('en');
        $fresh = $faq->fresh();

        $this->assertSame($persian, $fresh->question);
        $this->assertSame('Only the answer is translated.', $fresh->answer);
    }

    public function test_clearing_a_translation_brings_the_default_back(): void
    {
        $faq = Faq::query()->firstOrFail();
        $persian = $faq->question;

        $faq->putTranslations('en', ['question' => 'Something']);
        $faq->putTranslations('en', ['question' => '']);

        app()->setLocale('en');
        $this->assertSame($persian, $faq->fresh()->question);
        $this->assertSame(0, $faq->translations()->count());
    }

    /** فیلدی که مدل اعلام نکرده، از راه ترجمه هم نباید نوشته شود. */
    public function test_only_declared_fields_can_be_translated(): void
    {
        $faq = Faq::query()->firstOrFail();
        $faq->putTranslations('en', ['group' => 'hacked']);

        $this->assertSame(0, $faq->translations()->count());
    }

    /**
     * زبان پیش‌فرض نباید هزینه‌ی چندزبانه‌بودن را بدهد.
     *
     * و زبان دیگر باید یک کوئری بدهد برای کل مجموعه، نه یکی به‌ازای هر
     * رکورد — که همان دام N+1 است.
     */
    public function test_translations_cost_one_query_and_only_when_needed(): void
    {
        $count = function (string $locale): int {
            app()->setLocale($locale);

            $queries = 0;
            DB::listen(function () use (&$queries) {
                $queries++;
            });

            Faq::query()->get()->each(fn (Faq $faq) => $faq->question);

            DB::getEventDispatcher()->forget('Illuminate\\Database\\Events\\QueryExecuted');

            return $queries;
        };

        $this->assertGreaterThan(1, Faq::query()->count(), 'تست به چند ردیف نیاز دارد.');

        $this->assertSame(1, $count(Locales::default()));
        $this->assertSame(2, $count('en'));
    }

    // ------------------------------------------------------------- پنل --

    public function test_an_admin_can_enter_a_translation(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $faq = Faq::query()->firstOrFail();

        $this->actingAs($admin)
            ->put("/admin/faqs/{$faq->id}", [
                'group' => $faq->group,
                'question' => $faq->question,
                'answer' => $faq->answer,
                'order' => $faq->order ?? 0,
                'translations' => [
                    'en' => ['question' => 'Translated question'],
                ],
            ])
            ->assertRedirect();

        app()->setLocale('en');
        $this->assertSame('Translated question', $faq->fresh()->question);
    }

    /**
     * زبان پیش‌فرض از این راه نوشته نمی‌شود.
     *
     * متنِ فارسی ستونِ خود جدول است؛ اگر فرمِ ترجمه می‌توانست آن را هم
     * بنویسد، دو منبع حقیقت پیدا می‌کرد.
     */
    public function test_the_translation_form_cannot_write_the_default_language(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $faq = Faq::query()->firstOrFail();
        $persian = $faq->question;

        $this->actingAs($admin)
            ->put("/admin/faqs/{$faq->id}", [
                'group' => $faq->group,
                'question' => $persian,
                'answer' => $faq->answer,
                'order' => $faq->order ?? 0,
                'translations' => [
                    Locales::default() => ['question' => 'overwritten'],
                    'de' => ['question' => 'unknown locale'],
                ],
            ])
            ->assertRedirect();

        $this->assertSame($persian, $faq->fresh()->question);
        $this->assertSame(0, $faq->translations()->count());
    }

    public function test_the_admin_form_offers_a_box_for_every_extra_language(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $faq = Faq::query()->firstOrFail();

        $page = $this->actingAs($admin)->get("/admin/faqs/{$faq->id}/edit")->assertOk();

        foreach (Locales::all()->except(Locales::default()) as $code => $meta) {
            $page->assertSee($meta['name'], false);
            $page->assertSee("translations[{$code}][question]", false);
        }

        $page->assertDontSee('translations['.Locales::default().']', false);
    }

    // ------------------------------------------------------ پیکربندی --

    /**
     * هر زبانِ اعلام‌شده باید پرونده‌ی رشته‌ها و مشخصات کامل داشته باشد.
     *
     * این تست همان چیزی است که افزودن زبان تازه را بی‌خطر می‌کند: مدخلِ
     * ناقص در تنظیمات، همین‌جا گیر می‌افتد و نه روی سایت.
     */
    public function test_every_declared_locale_is_complete(): void
    {
        foreach (Locales::all() as $code => $meta) {
            foreach (['name', 'short', 'dir', 'html', 'font', 'digits', 'calendar'] as $key) {
                $this->assertArrayHasKey($key, $meta, "زبان «{$code}» کلید «{$key}» را ندارد.");
            }

            $this->assertContains($meta['dir'], ['rtl', 'ltr'], "جهت زبان «{$code}» نامعتبر است.");
            $this->assertFileExists(lang_path("{$code}/site.php"), "زبان «{$code}» پرونده‌ی رشته‌ها ندارد.");
        }
    }

    public function test_the_default_locale_is_one_of_the_available_ones(): void
    {
        $this->assertTrue(Locales::supports(Locales::default()));
        $this->assertTrue(Locales::supports(config('locales.fallback')));
    }

    public function test_routes_exist_for_the_locale_parameter(): void
    {
        // هر مسیر عمومی باید پارامتر زبان داشته باشد، وگرنه سوئیچر به آن نمی‌رسد
        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();

            // مسیرهای بی‌زبانِ سایت، و مسیرهای خودِ لاراول
            $exempt = ['sitemap', 'robots', 'root'];
            $internal = str_starts_with($name, 'admin.') || str_starts_with($name, 'storage.');

            if (! $name || $internal || in_array($name, $exempt, true)) {
                continue;
            }

            $this->assertContains('locale', $route->parameterNames(), "مسیر «{$name}» پیشوند زبان ندارد.");
        }
    }
}
