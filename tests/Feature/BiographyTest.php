<?php

namespace Tests\Feature;

use App\Models\Milestone;
use App\Models\Person;
use App\Models\User;
use App\Support\Navigation;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * «درباره ما ← بیوگرافی».
 */
class BiographyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_the_page_renders_in_every_language(): void
    {
        foreach (['fa', 'en', 'ar'] as $code) {
            $this->get("/{$code}/about/biography")->assertOk()
                ->assertSee(trans('site.biography.intro_title', [], $code), false);
        }
    }

    public function test_it_sits_under_about_in_the_menu(): void
    {
        $about = collect(Navigation::items())->firstWhere('route', 'about');

        $this->assertContains('biography', array_column($about['children'], 'route'));

        $this->get(route('home'))->assertOk()->assertSee(route('biography'), false);
    }

    /** خطِ زمانِ آغازین از همان چیزی آمده که «درباره ما» می‌گوید — نه رویدادِ ساختگی. */
    public function test_the_timeline_starts_with_what_the_site_already_says(): void
    {
        $this->assertSame([1380, 1392, null], Milestone::query()->visible()->pluck('year')->all());

        $this->get(route('biography'))->assertOk()
            ->assertSeeInOrder(['۱۳۸۰', '۱۳۹۲', 'امروز'], false);
    }

    /** خط به ترتیبِ سال است؛ رویدادِ تازه در جای درستش می‌نشیند، بی شماره‌زدن. */
    public function test_a_new_milestone_lands_in_its_year(): void
    {
        Milestone::create(['year' => 1386, 'title' => 'نخستین قرارداد انبوه‌سازی', 'is_active' => true]);

        $this->get(route('biography'))->assertOk()
            ->assertSeeInOrder(['۱۳۸۰', 'نخستین قرارداد انبوه‌سازی', '۱۳۹۲', 'امروز'], false);
    }

    /** صفحه‌ی انگلیسی سال را میلادی می‌خواند — همان قاعده‌ی سالِ تأسیس. */
    public function test_english_shows_gregorian_years_and_translations(): void
    {
        $this->get('/en/about/biography')->assertOk()
            ->assertSeeInOrder(['2001', 'One kiln, five people', '2013', 'Today'], false)
            ->assertDontSee('یک کوره، پنج نفر', false);
    }

    /** ردیفِ الگوی مدیران پنهان است: نام و زندگی‌نامه‌ی آدمِ واقعی را فقط شرکت می‌داند. */
    public function test_the_people_template_stays_hidden(): void
    {
        $this->assertSame(1, Person::count());
        $this->assertFalse(Person::first()->is_active);

        $this->get(route('biography'))->assertOk()
            ->assertDontSee('data-person', false)
            ->assertDontSee('نام و نام خانوادگی', false);
    }

    public function test_an_active_person_shows_with_their_bio(): void
    {
        Person::first()->update([
            'name' => 'کیان احمدی',
            'role' => 'بنیان‌گذار',
            'bio' => "پاراگراف نخست.\nپاراگراف دوم.",
            'is_active' => true,
        ]);

        $page = $this->get(route('biography'))->assertOk();

        $page->assertSee('کیان احمدی', false)
            ->assertSee('بنیان‌گذار', false)
            ->assertSee('<p>پاراگراف نخست.</p>', false)
            ->assertSee('<p>پاراگراف دوم.</p>', false);

        // بی عکس، حرفِ نخست در جای عکس — خودِ آن عنصر، نه هر «ک» ی در نام
        $this->assertMatchesRegularExpression(
            '~<span aria-hidden="true"\s+class="grid h-28 w-28[^"]*">\s*ک\s*</span>~u',
            $page->getContent(),
        );
    }

    public function test_a_hidden_milestone_is_not_shown(): void
    {
        Milestone::where('year', 1392)->update(['is_active' => false]);

        $this->get(route('biography'))->assertOk()->assertDontSee('۱۳۹۲', false);
    }

    public function test_the_panel_manages_both_lists(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)->get('/admin/milestones')->assertOk()->assertSee('یک کوره، پنج نفر', false);
        $this->actingAs($admin)->get('/admin/people')->assertOk()->assertSee('نام و نام خانوادگی', false);

        $this->actingAs($admin)->post(route('admin.resource.store', 'milestones'), [
            'year' => '۱۳۹۸',
            'title' => 'گواهی استاندارد',
            'is_active' => '1',
        ])->assertSessionHasNoErrors();

        $this->get(route('biography'))->assertOk()->assertSee('گواهی استاندارد', false);
    }

    public function test_the_page_is_in_the_sitemap(): void
    {
        $this->get(route('sitemap'))->assertOk()->assertSee(route('biography'), false);
    }
}
