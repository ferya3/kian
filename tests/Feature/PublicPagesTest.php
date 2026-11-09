<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Product;
use App\Models\Project;
use App\Models\Solution;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public static function staticRoutes(): array
    {
        return array_map(fn ($name) => [$name], [
            'home', 'products.index', 'finder.show', 'solutions.index', 'projects.index',
            'technology', 'factory', 'sustainability', 'technical.index', 'technical.downloads',
            'technical.installation', 'technical.certificates', 'technical.faq',
            'articles.index', 'about', 'distributors', 'contact', 'search', 'sitemap', 'robots',
        ]);
    }

    /** @dataProvider staticRoutes */
    public function test_static_pages_render(string $name): void
    {
        $this->get(route($name))->assertOk();
    }

    public function test_every_product_has_its_own_page(): void
    {
        foreach (Product::all() as $product) {
            $this->get(route('products.show', $product))
                ->assertOk()
                ->assertSee($product->name)
                ->assertSee('مشخصات فنی');
        }
    }

    public function test_detail_pages_render(): void
    {
        $this->get(route('projects.show', Project::first()))->assertOk();
        $this->get(route('solutions.show', Solution::first()))->assertOk();
        $this->get(route('articles.show', Article::query()->published()->first()))->assertOk();
    }

    public function test_inactive_product_is_not_reachable(): void
    {
        $product = Product::first();
        $product->update(['is_active' => false]);

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_unpublished_article_is_not_reachable(): void
    {
        $article = Article::first();
        $article->update(['published_at' => now()->addWeek()]);

        $this->get(route('articles.show', $article))->assertNotFound();
    }

    /**
     * بی فروشگاه (و بی قیمت) Product اعلام نمی‌شود — گوگل آن را بی offers
     * نامعتبر می‌داند. حالتِ با قیمت در ShopTest آزموده می‌شود.
     */
    public function test_product_page_exposes_breadcrumbs_and_no_priceless_product(): void
    {
        $product = Product::where('slug', 'ceramic-block-20')->firstOrFail();

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('"@type":"BreadcrumbList"', escape: false)
            ->assertDontSee('"@type":"Product"', escape: false)
            ->assertSee($product->sku);
    }

    public function test_sitemap_lists_every_product(): void
    {
        $response = $this->get(route('sitemap'))->assertOk();

        foreach (Product::query()->active()->get() as $product) {
            $response->assertSee(route('products.show', $product), escape: false);
        }
    }

    /**
     * سنجش روی نشانیِ محصول است و نه نامش.
     *
     * پیش‌تر این تست نامِ نمایشی را می‌سنجید و توخالی بود: «بلوک عایق ۲۵»
     * یک برچسبِ ثابت در مگامنو هم هست و در هر صفحه‌ای می‌آید، پس حتی وقتی
     * آن محصول از کاتالوگ حذف شد، تست همچنان سبز ماند. نشانی فقط جایی
     * می‌آید که خودِ محصول فهرست شده باشد.
     */
    public function test_category_filter_narrows_the_catalogue(): void
    {
        $wall = Product::where('slug', 'ceramic-block-20')->firstOrFail();
        $partition = Product::where('slug', 'partition-block-8')->firstOrFail();

        $this->get(route('products.index', ['category' => 'wall-blocks']))
            ->assertOk()
            ->assertSee(route('products.show', $wall), escape: false)
            ->assertDontSee(route('products.show', $partition), escape: false);
    }

    public function test_search_finds_products_by_name(): void
    {
        $this->get(route('search', ['q' => 'بلوک سفالی ۲۰']))
            ->assertOk()
            ->assertSee('بلوک سفالی ۲۰');
    }

    /**
     * جبرانِ ارتفاع هدر باید داخل قاب باشد، نه بیرونش.
     *
     * بیرونِ قاب، صفحه‌های بدون قهرمان با یک نوار از زمینه‌ی تیره شروع می‌شدند
     * و قاب چند ده پیکسل پایین‌تر از هدر آغاز می‌شد.
     */
    public function test_the_header_offset_sits_inside_the_shell(): void
    {
        $inner = $this->get(route('about'))->assertOk()->getContent();
        $shell = strpos($inner, 'page-shell');
        $offset = strpos($inner, 'header-offset');

        $this->assertNotFalse($offset, 'صفحه‌ی بدون قهرمان باید جبران ارتفاع هدر داشته باشد.');
        $this->assertGreaterThan($shell, $offset, 'جبران ارتفاع هدر باید داخل قاب باشد.');

        // صفحه‌ی اصلی قهرمان تمام‌قد دارد و نباید جبران بگیرد
        $this->get(route('home'))->assertOk()->assertDontSee('header-offset');
    }

    /**
     * هیروی صفحه‌ی اصلی، روی گوشی و دسکتاپ، فقط دکمه دارد.
     *
     * تیتر حذف نمی‌شود و فقط پنهانِ دیداری است: همان h1 ِ صفحه است، و
     * صفحه‌ی اصلیِ بی h1 در جستجو عقب می‌افتد.
     */
    public function test_the_home_hero_shows_only_its_buttons(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();
        $hero = str($html)->betweenFirst('<section', '</section>')->toString();

        $this->assertMatchesRegularExpression('~<h1 class="sr-only">\s*'.preg_quote(e(\App\Models\Setting::text('hero_title', \App\Support\Brand::tagline())), '~').'~u', $hero,
            'تیتر باید برای گوگل و صفحه‌خوان بماند.');

        // برچسب و زیرتیتر در هیچ اندازه‌ای نیستند — نه فقط پنهان روی دسکتاپ
        $this->assertStringNotContainsString(e(\App\Models\Setting::text('hero_subtitle', config('kian.seo.default_description'))), $hero);
        $this->assertStringNotContainsString('class="eyebrow', $hero);

        // دکمه‌ها همچنان هستند
        $this->assertStringContainsString(route('products.index'), $hero);
        $this->assertStringContainsString(route('factory'), $hero);
    }

    /**
     * هیچ تگی دو ویژگیِ هم‌نام ندارد.
     *
     * مرورگر از ویژگی‌های تکراری فقط اولی را نگه می‌دارد و بقیه را بی‌صدا دور
     * می‌ریزد. کشوی منوی موبایل دو style داشت؛ display:none ِ دومی گم می‌شد و
     * روی گوشی، تا رسیدنِ جاوااسکریپت، منو باز روی صفحه می‌ماند.
     */
    public function test_no_tag_repeats_an_attribute(): void
    {
        foreach (['home', 'products.index', 'about', 'contact'] as $name) {
            $html = $this->get(route($name))->assertOk()->getContent();

            preg_match_all('~<[a-z][a-z0-9-]*\s[^<>]*>~is', $html, $tags);
            foreach ($tags[0] as $tag) {
                preg_match_all('~\s([a-z][a-z0-9:_.@-]*)=["\']~i', preg_replace('~(["\'])(?:(?!\1).)*\1~s', '$1$1', $tag), $attrs);
                $dupes = array_keys(array_filter(array_count_values(array_map('strtolower', $attrs[1])), fn ($n) => $n > 1));

                $this->assertSame([], $dupes, "ویژگیِ تکراری در صفحه‌ی {$name}: ".\Illuminate\Support\Str::limit($tag, 160));
            }
        }

        $panel = str($this->get(route('home'))->getContent())->betweenFirst('x-ref="panel"', '>')->toString();
        $this->assertStringContainsString('display: none', $panel);
        $this->assertStringContainsString('x-cloak', $panel);
    }
    /**
     * آنچه PageSpeed ِ موبایل را از ۶۹ به ۹۹ رساند؛ هیچ‌کدام نباید بی‌صدا برود.
     */
    public function test_the_home_page_keeps_its_speed_measures(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        // قلم‌ها همراهِ CSS، نه بعدش
        $this->assertMatchesRegularExpression('~<link rel="preload" href="[^"]*vazirmatn-subset[^"]*\.woff2" as="font"~', $html);
        $this->assertMatchesRegularExpression('~<link rel="preload" href="[^"]*inter-subset[^"]*\.woff2" as="font"~', $html);

        // بخش‌های زیرِ صفحه: رندر و Alpine ِ تنبل
        $this->assertGreaterThanOrEqual(6, substr_count($html, 'class="defer-render" data-alpine-lazy'));

        // منوی موبایل تا نخستین لمس راه نمی‌افتد
        $this->assertStringContainsString('x-data="mobileNav" data-alpine-on="toggle-mobile-nav"', $html);

        // متنِ صفحه‌ی اول منتظرِ جاوااسکریپت نمی‌ماند
        $this->assertStringContainsString("document.querySelectorAll('main [data-reveal]')", $html);
    }
}
