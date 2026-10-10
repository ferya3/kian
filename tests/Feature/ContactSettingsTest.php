<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Support\Contact;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * «پنل ← اطلاعات تماس».
 *
 * پیش از این تلفن و نشانی فقط در پیکربندی بودند و از پنل دست‌نیافتنی.
 */
class ContactSettingsTest extends TestCase
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

    /** همه‌ی میدان‌ها با مقدارِ فعلی‌شان، و هر تغییری روی آن. */
    protected function form(array $overrides = []): array
    {
        $values = collect(array_keys(Contact::FIELDS))
            ->mapWithKeys(fn ($f) => [$f => Contact::stored($f) ?? (string) config(Contact::FIELDS[$f])])
            ->all();

        return array_merge($values, $overrides);
    }

    public function test_the_page_shows_the_current_values(): void
    {
        $this->actingAs($this->admin())->get(route('admin.contact'))
            ->assertOk()
            ->assertSee(Contact::get('phone'), false)
            // نشانی‌ها اینجا فهرست می‌شوند، با پیوند به جای ویرایششان
            ->assertSee(\App\Models\Location::primary()->title, false)
            ->assertSee(route('admin.resource.index', 'locations'), false);
    }

    /** آنچه در پنل ذخیره شود، همان است که روی سایت دیده می‌شود — همه‌جا. */
    public function test_a_saved_number_reaches_every_place_on_the_site(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.contact.update'), $this->form(['phone' => '045-33330000']))
            ->assertRedirect(route('admin.contact'))
            ->assertSessionHasNoErrors();

        $home = $this->get(route('home'))->assertOk();

        // نمایش با ارقام فارسی، و نشانیِ tel: از روی همان شماره
        $home->assertSee('۰۴۵-۳۳۳۳۰۰۰۰', false)
            ->assertSee('tel:+984533330000', false)
            // شماره‌ی قدیم نه در متن (ارقام فارسی) و نه در دکمه
            ->assertDontSee(\App\Support\Jalali::digits('045-3182'), false)
            ->assertDontSee('tel:'.Contact::tel('045-3182').'"', false);
    }

    /**
     * پاک‌کردنِ داخلی یعنی «داخلی نداریم».
     *
     * اگر خالی را «ذخیره‌نشده» می‌گرفتیم، ۱۰۶ِ پیش‌فرض از پیکربندی برمی‌گشت
     * و پاک‌کردن هیچ اثری نداشت.
     */
    public function test_clearing_the_extension_removes_it(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.contact.update'), $this->form(['sales_extension' => '']));

        $this->assertNull(\App\Support\Brand::salesExtension());
        $this->assertStringNotContainsString(',,', Contact::salesPhoneTel());

        $this->get(route('contact'))->assertOk()->assertDontSee('(داخلی', false);
    }

    public function test_an_invalid_number_is_refused_and_nothing_changes(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.contact.update'), $this->form(['phone' => 'زنگ بزنید']))
            ->assertSessionHasErrors('phone');

        $this->assertSame('045-3182', Contact::stored('phone'));
    }

    public function test_the_central_phone_cannot_be_emptied(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.contact.update'), $this->form(['phone' => '']))
            ->assertSessionHasErrors('phone');
    }

    /** نشانیِ بی نامِ کاربری پیوند نیست — به صفحه‌ی اصلیِ اینستاگرام می‌رسد. */
    public function test_a_social_link_without_a_profile_is_not_shown(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.contact.update'), $this->form([
                'instagram' => 'https://instagram.com/kianbehsaz',
                'telegram' => '',
            ]));

        $this->assertSame(['instagram' => 'https://instagram.com/kianbehsaz'], Contact::social());

        $this->get(route('home'))->assertOk()
            ->assertSee('https://instagram.com/kianbehsaz', false)
            ->assertDontSee('href="https://t.me/"', false);
    }

    public function test_only_a_full_admin_can_reach_it(): void
    {
        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);

        $this->actingAs($editor)->get(route('admin.contact'))->assertForbidden();
        $this->actingAs($editor)->put(route('admin.contact.update'), $this->form())->assertForbidden();

        auth()->logout();
        $this->get(route('admin.contact'))->assertRedirect(route('admin.login'));
    }

    /**
     * ردیف‌های تماس در فهرستِ خامِ «تنظیمات محتوا» نیستند.
     *
     * آنجا فرمی بی اعتبارسنجی است؛ اگر از آن راه هم ویرایش‌پذیر بودند،
     * اعتبارسنجیِ این صفحه را دور می‌زدند.
     */
    public function test_contact_rows_cannot_be_reached_through_the_raw_settings_list(): void
    {
        Setting::put(Contact::PREFIX.'phone', '045-1111', 'contact');
        $row = Setting::where('key', Contact::PREFIX.'phone')->firstOrFail();

        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/settings')->assertOk()->assertDontSee('contact.phone', false);
        $this->actingAs($admin)->get("/admin/settings/{$row->id}/edit")->assertNotFound();
    }

    public function test_tel_links_are_built_from_the_displayed_number(): void
    {
        $this->assertSame('+98453182', Contact::tel('045-3182'));
        $this->assertSame('+984533338748', Contact::tel('۰۴۵-۳۳۳۳ ۸۷۴۸'));
        $this->assertSame('+984533338748,,106', Contact::tel('045-33338748', '۱۰۶'));
        $this->assertSame('+4930123456', Contact::tel('0049 30 123456'));
        $this->assertSame('+982188776655', Contact::tel('+98 21 8877 6655'));
    }

    /**
     * شماره‌های واقعی، حتی اگر .env هنوز شماره‌ی نمونه را دارد.
     *
     * .env ِ سرورهای نصب‌شده از .env.example ِ قدیم آمده و KIAN_PHONE را روی
     * ۰۲۱ نگه داشته؛ پیش‌فرضِ کد زیرِ آن پنهان می‌ماند. ردیفِ مهاجرت هر دو
     * را می‌پوشاند.
     */
    public function test_the_real_numbers_win_over_a_stale_env(): void
    {
        config(['kian.contact.phone' => '۰۲۱-۹۱۰۰۲۲۳۳']);

        $this->get(route('home'))->assertOk()
            ->assertSee('tel:+98453182"', false)
            ->assertDontSee('tel:+982191002233', false);

        $this->assertSame('+984533338748,,106', Contact::salesPhoneTel());
    }

    /** مهاجرت شماره‌ای را که مدیر عوض کرده دوباره نمی‌نویسد. */
    public function test_the_migration_keeps_a_number_the_admin_changed(): void
    {
        Setting::put(Contact::PREFIX.'phone', '045-9999', 'contact');

        $migration = require database_path('migrations/2026_01_01_000012_store_factory_phone_numbers.php');
        $migration->up();
        $this->assertSame('045-9999', Contact::stored('phone'));

        $migration->down();
        $this->assertSame('045-9999', Contact::stored('phone'), 'down فقط مقدارِ خودش را برمی‌دارد.');
        $this->assertNull(Contact::stored('sales_extension'));
    }

}
