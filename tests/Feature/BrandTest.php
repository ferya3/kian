<?php

namespace Tests\Feature;

use App\Models\Setting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase;

    /** نامِ پیشین — تنها جایی از کد که هنوز باید بشناسدش، همین تست است. */
    protected const OLD = 'سفال کیان';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_the_pages_carry_the_brand_name(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(config('kian.brand.name'), false)
            ->assertDontSee(self::OLD, false);
    }

    /**
     * نشانِ هدر نام را دو بار نمی‌گوید.
     *
     * خطِ دومِ نشان توضیحِ کالاست و نه ترجمه‌ی نام؛ اگر روزی کسی آن را به
     * name_en وصل کند، زیر «کیان بهساز» می‌نویسد «Kian Behsaz».
     */
    public function test_the_lockup_descriptor_is_not_the_brand_name(): void
    {
        $this->assertNotSame(
            config('kian.brand.name_en'),
            config('kian.brand.descriptor_en')
        );
    }

    public function test_the_seeded_content_uses_the_new_name(): void
    {
        $this->assertSame(
            0,
            DB::table('settings')->where('value', 'like', '%'.self::OLD.'%')->count()
        );
    }

    /**
     * مهاجرتِ تغییر نام روی نصبِ موجود.
     *
     * config و seeder فقط نصبِ تازه را درست می‌کنند؛ سایتی که بالا آمده،
     * متنش در دیتابیس است. این همان چیزی است که hero_eyebrow را روی صفحه‌ی
     * اصلی با نام قدیم نگه داشته بود.
     */
    public function test_the_rename_migration_reaches_content_already_stored(): void
    {
        Setting::put('hero_eyebrow', 'کارخانه '.self::OLD.' — از سال ۱۳۸۰');
        Setting::put('untouched', 'متنی که نامِ برند را ندارد');

        $this->migration()->up();

        $this->assertSame('کارخانه کیان بهساز — از سال ۱۳۸۰', Setting::text('hero_eyebrow'));
        $this->assertSame('متنی که نامِ برند را ندارد', Setting::text('untouched'));
    }

    /** دوبار اجرا شدن نباید چیزی را خراب کند. */
    public function test_the_rename_migration_is_idempotent(): void
    {
        Setting::put('hero_eyebrow', 'کارخانه '.self::OLD);

        $this->migration()->up();
        $this->migration()->up();

        $this->assertSame('کارخانه کیان بهساز', Setting::text('hero_eyebrow'));
    }

    public function test_the_rename_migration_can_be_rolled_back(): void
    {
        Setting::put('hero_eyebrow', 'کارخانه '.self::OLD);

        $this->migration()->up();
        $this->migration()->down();

        $this->assertSame('کارخانه '.self::OLD, Setting::text('hero_eyebrow'));
    }

    protected function migration(): object
    {
        return require database_path('migrations/2026_01_01_000008_rename_brand_in_stored_content.php');
    }
}
