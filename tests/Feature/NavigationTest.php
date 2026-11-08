<?php

namespace Tests\Feature;

use App\Support\Navigation;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ناوبری یک منبع حقیقت دارد و سه مصرف‌کننده: هدر دسکتاپ، منوی موبایل و
 * مگا منو. آیتمی که route ندارد — مثل سامانه‌ی نوبت‌دهی که روی زیردامنه‌ی
 * خودش است — در هر سه جا باید بی‌سروصدا کار کند، نه اینکه کل صفحه را
 * با «کلید ناموجود» یا «مسیر تعریف‌نشده» بیندازد.
 */
class NavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_the_menu_opens_with_home_and_ends_with_the_booking_system(): void
    {
        $items = Navigation::items();

        $this->assertSame('home', $items[0]['route']);
        $this->assertSame(config('kian.links.booking'), end($items)['url']);
    }

    public function test_an_external_item_never_goes_through_the_router(): void
    {
        $booking = ['label' => 'سامانه نوبت‌دهی', 'url' => 'https://nobat.example.test'];

        $this->assertTrue(Navigation::isExternal($booking));
        $this->assertSame('https://nobat.example.test', Navigation::href($booking));
        $this->assertSame('https://nobat.example.test', Navigation::key($booking));

        // بدون route، «صفحه‌ی فعلی» هم بی‌معناست
        $this->assertFalse(Navigation::isActive($booking));
    }

    public function test_the_booking_link_is_on_every_page_in_a_new_tab(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString(config('kian.links.booking'), $html);
        $this->assertStringContainsString('rel="noopener"', $html);
    }

    /** @dataProvider locales */
    public function test_the_menu_is_labelled_in_each_language(string $locale, string $home, string $booking): void
    {
        $this->get("/{$locale}")
            ->assertOk()
            ->assertSee($home, false)
            ->assertSee($booking, false);
    }

    public static function locales(): array
    {
        return [
            'fa' => ['fa', 'صفحه اصلی', 'سامانه نوبت‌دهی'],
            'en' => ['en', 'Home', 'Truck booking'],
            'ar' => ['ar', 'الصفحة الرئيسية', 'نظام حجز المواعيد'],
        ];
    }
}
