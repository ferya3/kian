<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * نشانی‌ها — یکی، دو تا، هر چند تا.
 *
 * تا پیش از این نشانی یک میدان بود؛ کارخانه و دفتر فروش و انبار یا در یک
 * خط چپانده می‌شدند یا یکی‌شان گفته نمی‌شد.
 */
class LocationTest extends TestCase
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

    protected function office(array $overrides = []): Location
    {
        return Location::create(array_merge([
            'title' => 'دفتر فروش تهران',
            'address' => 'تهران، خیابان آزمایشی، پلاک ۷',
            'working_hours' => 'شنبه تا پنجشنبه ۹ تا ۱۸',
            'phone' => '021-88776655',
            'position' => 2,
            'is_active' => true,
        ], $overrides));
    }

    /** نشانیِ پیش از این مهاجرت، نخستین ردیف شد و چیزی گم نشد. */
    public function test_the_existing_address_became_the_first_location(): void
    {
        $primary = Location::primary();

        $this->assertNotNull($primary);
        $this->assertSame(config('kian.contact.address'), $primary->address);
        $this->assertSame(config('kian.contact.working_hours'), $primary->working_hours);
    }

    /** نشانیِ انگلیسیِ پرونده‌ی زبان، ترجمه‌ی همان ردیف شد. */
    public function test_the_english_translation_came_along(): void
    {
        $this->get('/en/contact')->assertOk()
            ->assertSee(trans('site.contact.address', [], 'en'), false)
            ->assertDontSee(config('kian.contact.address'), false);
    }

    public function test_every_location_shows_on_the_contact_page_with_its_own_details(): void
    {
        $office = $this->office();

        $page = $this->get(route('contact'))->assertOk();

        $page->assertSeeInOrder([Location::primary()->title, $office->title], false)
            ->assertSee($office->address, false)
            ->assertSee($office->working_hours, false)
            ->assertSee('tel:+982188776655', false)
            // در href، «&» به «&amp;» درمی‌آید — همان HTML ِ درست
            ->assertSee(e($office->mapUrl()), false);

        $this->assertSame(2, substr_count($page->getContent(), 'data-location'));
    }

    /** با یک نشانی عنوان در فوتر نوفه است؛ با دو تا لازم است تا معلوم شود کدام کجاست. */
    public function test_the_footer_titles_the_addresses_only_when_there_are_several(): void
    {
        $footer = fn () => str($this->get(route('home'))->getContent())->after('<footer')->toString();

        $this->assertStringContainsString(Location::primary()->address, $footer());
        $this->assertStringNotContainsString(Location::primary()->title.'</span>', $footer());

        $office = $this->office();

        $html = $footer();
        $this->assertStringContainsString($office->address, $html);
        $this->assertStringContainsString($office->title, $html);
    }

    /** نوار بالا ساعت کاریِ نشانیِ اصلی را دارد — کوچک‌ترین «ترتیب». */
    public function test_the_top_bar_takes_the_primary_hours(): void
    {
        $this->office(['position' => 0, 'working_hours' => 'هر روز ۷ تا ۲۲']);

        $this->get(route('home'))->assertOk()->assertSee('هر روز ۷ تا ۲۲', false);
    }

    public function test_a_hidden_location_is_not_shown(): void
    {
        $office = $this->office(['is_active' => false]);

        $this->get(route('contact'))->assertOk()->assertDontSee($office->address, false);
    }

    /** بی هیچ نشانی، صفحه‌ها نمی‌شکنند. */
    public function test_the_site_survives_with_no_location_at_all(): void
    {
        Location::query()->delete();

        $this->get(route('home'))->assertOk();
        $this->get(route('contact'))->assertOk()->assertDontSee('data-location', false);
    }

    /** گوگل نشانیِ اصلی را می‌گیرد، و بقیه را به‌عنوان مکان‌های دیگر. */
    public function test_schema_carries_the_primary_address_and_the_rest_as_places(): void
    {
        $office = $this->office();

        $graph = json_decode(
            str($this->get(route('home'))->getContent())
                ->between('<script type="application/ld+json">', '</script>')
                ->toString(),
            true,
        );

        $org = collect($graph['@graph'] ?? [$graph])->firstWhere('@type', 'Organization');

        $this->assertSame(Location::primary()->address, $org['address']['streetAddress']);
        $this->assertSame($office->address, $org['location'][0]['address']['streetAddress']);
        $this->assertSame('+982188776655', $org['location'][0]['telephone']);
    }

    public function test_an_admin_can_add_a_location_from_the_panel(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.resource.store', 'locations'), [
                'title' => 'انبار',
                'address' => 'اردبیل، جاده‌ی آزمایشی',
                'position' => 5,
                'is_active' => '1',
            ])
            ->assertSessionHasNoErrors();

        $this->get(route('contact'))->assertOk()->assertSee('اردبیل، جاده‌ی آزمایشی', false);
    }

    public function test_a_location_needs_a_title_and_an_address(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.resource.store', 'locations'), ['position' => 5])
            ->assertSessionHasErrors(['title', 'address']);
    }

    /** نشانی‌ها مالِ مدیر کل‌اند، مثل اطلاعات تماس. */
    public function test_an_editor_cannot_manage_locations(): void
    {
        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);

        $this->actingAs($editor)->get('/admin/locations')->assertForbidden();
    }

    /** ردیف‌های کهنه‌ی نشانی از تنظیمات پاک شدند تا دو منبع نماند. */
    public function test_the_old_address_settings_are_gone(): void
    {
        $this->assertSame(0, Setting::query()->whereIn('key', [
            'contact.address', 'contact.working_hours', 'contact.lat', 'contact.lng',
        ])->count());
    }

    public function test_the_map_link_uses_the_point_when_there_is_one(): void
    {
        $withPoint = $this->office(['lat' => 38.2498, 'lng' => 48.2933]);
        $withoutPoint = $this->office(['title' => 'انبار', 'lat' => null, 'lng' => null]);

        $this->assertStringContainsString(rawurlencode('38.2498,48.2933'), $withPoint->mapUrl());
        $this->assertStringContainsString(rawurlencode($withoutPoint->address), $withoutPoint->mapUrl());
    }
}
