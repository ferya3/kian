<?php

namespace Tests\Feature;

use App\Models\Milestone;
use App\Models\Person;
use App\Models\User;
use App\Support\Media;
use App\Support\Navigation;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    /** ردیفِ الگوی مدیرعامل پنهان است: نام و عکس و زندگی‌نامه‌اش را فقط شرکت دارد. */
    public function test_the_people_template_stays_hidden(): void
    {
        $this->assertSame(1, Person::count());

        $template = Person::first();
        $this->assertFalse($template->is_active);
        $this->assertTrue($template->is_featured, 'الگو باید همان مدیرعامل باشد.');

        $this->get(route('biography'))->assertOk()
            ->assertDontSee('data-person', false)
            ->assertDontSee('data-ceo', false)
            ->assertDontSee('نام و نام خانوادگی', false);
    }

    public function test_an_active_person_shows_with_their_bio(): void
    {
        Person::first()->update([
            'name' => 'کیان احمدی',
            'role' => 'بنیان‌گذار',
            'bio' => "پاراگراف نخست.\nپاراگراف دوم.",
            'is_active' => true,
            'is_featured' => false,
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

    protected function ceo(array $overrides = []): Person
    {
        $ceo = Person::first();
        $ceo->update(array_merge([
            'name' => 'کیان احمدی',
            'role' => 'مدیرعامل',
            'bio' => "پاراگراف نخستِ زندگی‌نامه.\nپاراگراف دوم.",
            'quote' => 'هر بچ را پیش از بارگیری آزمون می‌کنیم.',
            'is_active' => true,
        ], $overrides));

        return $ceo;
    }

    public function test_the_ceo_gets_a_featured_section_with_photo_message_and_bio(): void
    {
        Storage::fake('public');
        $photo = UploadedFile::fake()->image('ceo.jpg', 800, 1000)->store('admin/people', 'public');

        $this->ceo(['photo' => $photo]);

        $section = str($this->get(route('biography'))->assertOk()->getContent())
            ->between('data-ceo', '</section>')->toString();

        $this->assertStringContainsString(e(Media::url($photo)), $section);
        $this->assertStringContainsString('هر بچ را پیش از بارگیری آزمون می‌کنیم.', $section);
        $this->assertStringContainsString('کیان احمدی', $section);
        $this->assertStringContainsString('<p>پاراگراف نخستِ زندگی‌نامه.</p>', $section);
    }

    /** مدیرعامل بخشِ خودش را دارد و در کارت‌ها تکرار نمی‌شود. */
    public function test_the_ceo_is_not_repeated_among_the_cards(): void
    {
        $this->ceo();
        Person::create(['name' => 'مریم رضایی', 'role' => 'مدیر فنی', 'is_active' => true, 'position' => 2]);

        $html = $this->get(route('biography'))->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'data-person'));
        $this->assertSame(1, substr_count($html, 'کیان احمدی'));
        $this->assertStringContainsString('مریم رضایی', $html);
    }

    /** بی عکس، حرفِ نخستِ نام جای عکس می‌نشیند. */
    public function test_the_ceo_without_a_photo_shows_the_initial(): void
    {
        $this->ceo(['photo' => null]);

        $section = str($this->get(route('biography'))->getContent())->between('data-ceo', '</section>')->toString();

        $this->assertStringNotContainsString('<img', $section);
        $this->assertMatchesRegularExpression('~<span aria-hidden="true"\s+class="grid aspect-\[4/5\][^"]*">\s*ک\s*</span>~u', $section);
    }

    /** دو مدیرعاملِ ویژه یعنی یکی بی‌صدا ناپدید می‌شود — پس فقط یکی. */
    public function test_marking_someone_featured_unmarks_the_rest(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $first = $this->ceo();

        $this->actingAs($admin)->post(route('admin.resource.store', 'people'), [
            'name' => 'مدیرعامل تازه',
            'is_featured' => '1',
            'is_active' => '1',
        ])->assertSessionHasNoErrors();

        $this->assertFalse($first->fresh()->is_featured);
        $this->assertSame(1, Person::where('is_featured', true)->count());
        $this->assertTrue(Person::where('name', 'مدیرعامل تازه')->value('is_featured'));
    }
}
