<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * ساختار ناوبری اصلی. یک منبع حقیقت واحد برای هدر دسکتاپ، منوی موبایل و فوتر.
 */
class Navigation
{
    /** @return array<int, array{label: string, route: string, mega?: bool, children?: array}> */
    public static function items(): array
    {
        $items = [
            [
                'label' => 'محصولات',
                'route' => 'products.index',
                'mega' => true,
            ],
            [
                'label' => 'راهکارها',
                'route' => 'solutions.index',
            ],
            [
                'label' => 'پروژه‌ها',
                'route' => 'projects.index',
            ],
            [
                'label' => 'فناوری',
                'route' => 'technology',
                'children' => [
                    ['label' => 'از خاک تا سازه', 'route' => 'technology'],
                    ['label' => 'کارخانه', 'route' => 'factory'],
                    ['label' => 'پایداری', 'route' => 'sustainability'],
                ],
            ],
            [
                'label' => 'دانش فنی',
                'route' => 'technical.index',
                'children' => [
                    ['label' => 'مرکز فنی', 'route' => 'technical.index'],
                    ['label' => 'دانلود دیتاشیت، CAD و BIM', 'route' => 'technical.downloads'],
                    ['label' => 'راهنمای اجرا', 'route' => 'technical.installation'],
                    ['label' => 'گواهی‌نامه‌ها و استانداردها', 'route' => 'technical.certificates'],
                    ['label' => 'پرسش‌های متداول', 'route' => 'technical.faq'],
                    ['label' => 'مقالات تخصصی', 'route' => 'articles.index'],
                ],
            ],
            [
                'label' => 'درباره ما',
                'route' => 'about',
                'children' => [
                    ['label' => 'معرفی شرکت', 'route' => 'about'],
                    ['label' => 'نمایندگان فروش', 'route' => 'distributors'],
                    ['label' => 'تماس با ما', 'route' => 'contact'],
                ],
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
            array_splice($items, 1, 0, [[
                'label' => 'فروشگاه',
                'route' => 'shop.index',
            ]]);
        }

        return $items;
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
