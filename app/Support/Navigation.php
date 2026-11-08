<?php

namespace App\Support;

/**
 * ساختار ناوبری اصلی. یک منبع حقیقت واحد برای هدر دسکتاپ، منوی موبایل و فوتر.
 */
class Navigation
{
    /** @return array<int, array{label: string, route?: string, url?: string, mega?: bool, children?: array}> */
    public static function items(): array
    {
        return [
            [
                'label' => __('site.nav.home'),
                'route' => 'home',
            ],
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
            [
                // سامانه‌ی نوبت‌دهی اپلیکیشن دیگری روی زیردامنه‌ی خودش است:
                // نه route دارد و نه در این مخزن است، پس با url می‌آید.
                'label' => __('site.nav.booking'),
                'url' => config('kian.links.booking'),
            ],
        ];
    }

    /** نشانی آیتم — مسیر داخلی باشد یا پیوند بیرونی. */
    public static function href(array $item): string
    {
        return $item['url'] ?? route($item['route']);
    }

    /** آیتمی که بیرون از این سایت می‌رود، در تب تازه و با rel امن باز می‌شود. */
    public static function isExternal(array $item): bool
    {
        return isset($item['url']);
    }

    /**
     * کلید یکتای آیتم، برای وضعیت باز/بسته‌ی منو در Alpine.
     *
     * آیتم بیرونی route ندارد و خواندن مستقیم $item['route'] در قالب‌ها
     * خطای کلید ناموجود می‌دهد؛ این متد همان‌جا را یکدست می‌کند.
     */
    public static function key(array $item): string
    {
        return $item['route'] ?? $item['url'];
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
        // پیوند بیرونی هیچ‌وقت «صفحه‌ی فعلی» نیست
        if (! isset($item['route'])) {
            return false;
        }

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
