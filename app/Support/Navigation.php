<?php

namespace App\Support;

/**
 * ساختار ناوبری اصلی. یک منبع حقیقت واحد برای هدر دسکتاپ، منوی موبایل و فوتر.
 */
class Navigation
{
    /** @return array<int, array{label: string, route: string, mega?: bool, children?: array}> */
    public static function items(): array
    {
        return [
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
