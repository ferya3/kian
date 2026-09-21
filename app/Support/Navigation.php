<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * ساختار ناوبری اصلی. یک منبع حقیقت واحد برای هدر دسکتاپ، منوی موبایل و فوتر.
 *
 * برچسب‌ها اینجا نوشته نمی‌شوند، از lang/<code>/site.php می‌آیند و کلیدشان
 * نامِ همان مسیر است. پس فهرست یک جا تعریف می‌شود و ترجمه‌اش جای دیگر، و
 * افزودن یک آیتم یعنی یک ردیف اینجا و یک ردیف در هر پرونده‌ی زبان.
 */
class Navigation
{
    /** @return array<int, array{label: string, route: string, mega?: bool, children?: array}> */
    public static function items(): array
    {
        $items = [
            [
                'route' => 'products.index',
                'mega' => true,
            ],
            [
                'route' => 'solutions.index',
            ],
            [
                'route' => 'projects.index',
            ],
            [
                'route' => 'technology',
                'children' => ['technology', 'factory', 'sustainability'],
            ],
            [
                'route' => 'technical.index',
                'children' => [
                    'technical.index',
                    'technical.downloads',
                    'technical.installation',
                    'technical.certificates',
                    'technical.faq',
                    'articles.index',
                ],
            ],
            [
                'route' => 'about',
                'children' => ['about', 'distributors', 'contact'],
            ],
        ];

        /*
        | فروشگاه، اگر روشن باشد.
        |
        | کنارِ «محصولات» می‌نشیند و نه ته فهرست: کسی که آمده بخرد، اول از
        | همه دنبال همین است.
        |
        | شرطِ دوم — وجودِ خودِ مسیر — تشریفاتی نیست. مسیرها کش می‌شوند
        | (route:cache) ولی پیکربندی از .env می‌آید؛ اگر کسی کلید را روشن
        | کند و کشِ مسیرها را نسازد، منو نشانیِ مسیری را می‌سازد که وجود
        | ندارد و *هر صفحه‌ی سایت* با خطا می‌افتد، نه فقط فروشگاه.
        */
        if (Shop::enabled() && Route::has('shop.index')) {
            array_splice($items, 1, 0, [['route' => 'shop.index']]);
        }

        return array_map(static::label(...), $items);
    }

    /**
     * برچسب‌گذاریِ یک آیتم و فرزندانش از پرونده‌ی زبان.
     *
     * فرزندان در فهرست بالا فقط نامِ مسیرند؛ اینجا به ساختار کاملِ
     * [label, route] بازمی‌شوند، تا ویوها دست‌نخورده بمانند.
     *
     * @param  array{route: string, mega?: bool, children?: array<int, string>}  $item
     * @return array{label: string, route: string, mega?: bool, children?: array}
     */
    protected static function label(array $item): array
    {
        $item['label'] = static::labelFor($item['route']);

        if (! empty($item['children'])) {
            $item['children'] = array_map(
                fn (string $route) => [
                    'label' => static::labelFor($route, sub: true),
                    'route' => $route,
                ],
                $item['children'],
            );
        }

        return $item;
    }

    /**
     * برچسبِ یک مسیر.
     *
     * زیرمنو گاهی برچسبِ دیگری از خودِ صفحه می‌خواهد — «از خاک تا سازه» در
     * منو و «فناوری» در نوار بالا. کلیدِ nav.sub اگر نبود، به nav.items
     * برمی‌گردد؛ پس فقط استثناها دو بار نوشته می‌شوند.
     */
    public static function labelFor(string $route, bool $sub = false): string
    {
        /*
         * نقطه‌ی نام مسیر به زیرخط بدل می‌شود.
         *
         * کلیدِ ترجمه با نقطه، یعنی آرایه‌ی تودرتو: «technical.downloads»
         * دنبال items → technical → downloads می‌گشت و چیزی نمی‌یافت. با
         * زیرخط، کلید یک‌تکه می‌ماند و فهرست هم تخت و خواندنی.
         */
        $key = str_replace('.', '_', $route);

        foreach ($sub ? ["site.nav.sub.{$key}", "site.nav.items.{$key}"] : ["site.nav.items.{$key}"] as $candidate) {
            $label = __($candidate);

            if ($label !== $candidate) {
                return $label;
            }
        }

        // ترجمه‌ی نبوده، خودِ کلید را برمی‌گرداند — که برچسب نیست
        return $route;
    }

    /**
     * آیا این صفحه قهرمانِ تیره‌ی تمام‌قد دارد؟
     *
     * دو جا لازم است و باید یکی باشند: هدر از رویش تصمیم می‌گیرد شفاف بماند
     * یا نه، و چیدمان از رویش تصمیم می‌گیرد جبرانِ ارتفاع هدر بگذارد یا نه.
     */
    public static function overHero(): bool
    {
        return request()->routeIs('home');
    }

    /** آیا مسیر فعلی زیرمجموعه‌ی این آیتم است؟ برای aria-current و استایل فعال. */
    public static function isActive(array $item): bool
    {
        $routes = array_merge(
            [$item['route']],
            array_column($item['children'] ?? [], 'route')
        );

        foreach ($routes as $route) {
            $base = str_contains($route, '.') ? explode('.', $route)[0] : $route;

            if (request()->routeIs($route) || request()->routeIs($base.'.*')) {
                return true;
            }
        }

        return false;
    }
}
