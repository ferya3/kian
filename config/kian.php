<?php

/*
|--------------------------------------------------------------------------
| پیکربندی دامنه‌ی سایت کارخانه سفال
|--------------------------------------------------------------------------
| هرچیزی که ساختار سایت به آن وابسته است — و نه محتوای قابل ویرایش —
| اینجا نگهداری می‌شود: گزینه‌های موتور انتخاب محصول، مخاطبان مرکز فنی،
| نقشه‌ی رنگ‌ها و اطلاعات پیش‌فرض سازمان برای Schema.org.
*/

return [

    'brand' => [
        'name' => env('KIAN_BRAND_NAME', 'سفال کیان'),
        'name_en' => env('KIAN_BRAND_NAME_EN', 'Kian Ceramic Blocks'),
        'legal_name' => env('KIAN_LEGAL_NAME', 'شرکت صنایع سفال کیان'),
        'tagline' => 'ساخته‌شده از خاک، مهندسی‌شده برای آینده',
        'tagline_en' => 'Born of Earth. Engineered for the Future.',
        'founded' => 2001,
    ],

    'contact' => [
        'phone' => env('KIAN_PHONE', '۰۲۱-۹۱۰۰۲۲۳۳'),
        'phone_raw' => env('KIAN_PHONE_RAW', '+982191002233'),
        'sales_phone' => env('KIAN_SALES_PHONE', '۰۹۱۲-۱۲۳۴۵۶۷'),
        'email' => env('KIAN_EMAIL', 'info@kian-ceramic.ir'),
        'technical_email' => env('KIAN_TECH_EMAIL', 'technical@kian-ceramic.ir'),
        'address' => env('KIAN_ADDRESS', 'اصفهان، شهرک صنعتی مبارکه، خیابان صنعت ۱۲، کارخانه سفال کیان'),
        'address_locality' => 'اصفهان',
        'address_region' => 'اصفهان',
        'postal_code' => '۸۴۸۱۱۳۳۴۵۶',
        'lat' => 32.3456,
        'lng' => 51.5041,
        'working_hours' => 'شنبه تا چهارشنبه ۸:۰۰ تا ۱۷:۰۰ — پنجشنبه ۸:۰۰ تا ۱۳:۰۰',
    ],

    'social' => [
        'instagram' => env('KIAN_INSTAGRAM', 'https://instagram.com/'),
        'linkedin' => env('KIAN_LINKEDIN', 'https://linkedin.com/'),
        'aparat' => env('KIAN_APARAT', 'https://aparat.com/'),
        'telegram' => env('KIAN_TELEGRAM', 'https://t.me/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | موتور انتخاب محصول (Product Finder)
    |--------------------------------------------------------------------------
    | این گزینه‌ها هم فرم صفحه اصلی را می‌سازند و هم قواعد اعتبارسنجی را.
    */
    'finder' => [

        'project_types' => [
            'residential' => ['label' => 'ساختمان مسکونی', 'hint' => 'آپارتمان، ویلا، مجتمع'],
            'commercial' => ['label' => 'تجاری و اداری', 'hint' => 'مجتمع تجاری، برج اداری'],
            'industrial' => ['label' => 'صنعتی', 'hint' => 'سوله، کارخانه، انبار'],
            'mass' => ['label' => 'انبوه‌سازی', 'hint' => 'پروژه‌های مسکن ملی و شهرسازی'],
        ],

        'wall_types' => [
            'exterior' => ['label' => 'دیوار خارجی', 'hint' => 'نمای بیرونی و پوسته حرارتی'],
            'interior' => ['label' => 'دیوار داخلی', 'hint' => 'جداکننده فضاهای اصلی'],
            'partition' => ['label' => 'دیوار تیغه‌ای', 'hint' => 'پارتیشن سبک غیرباربر'],
            'infill' => ['label' => 'دیوار میان‌قاب', 'hint' => 'پرکننده قاب بتنی یا فولادی'],
            'roof' => ['label' => 'سقف', 'hint' => 'بلوک سقفی تیرچه‌بلوک'],
        ],

        // ضخامت بر حسب سانتی‌متر
        'thicknesses' => [7, 10, 15, 20, 25, 30],

        'insulation_levels' => [
            'low' => ['label' => 'کم', 'hint' => 'فضای داخلی، بدون نیاز حرارتی'],
            'medium' => ['label' => 'متوسط', 'hint' => 'مطابق حداقل مبحث ۱۹'],
            'high' => ['label' => 'زیاد', 'hint' => 'اقلیم سرد یا گرم، صرفه‌جویی انرژی'],
            'very_high' => ['label' => 'خیلی زیاد', 'hint' => 'ساختمان کم‌مصرف و گواهی انرژی'],
        ],

        // وزن‌دهی امتیاز تطابق — مجموع = ۱۰۰
        'weights' => [
            'project_type' => 25,
            'wall_type' => 30,
            'thickness' => 25,
            'insulation' => 20,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | سه پرسونای اصلی سایت
    |--------------------------------------------------------------------------
    */
    'audiences' => [
        'customer' => [
            'label' => 'کارفرما و مشتری',
            'question' => 'چه محصولی برای پروژه‌ام مناسب است؟',
            'cta' => 'انتخاب محصول',
            'route' => 'products.index',
            'icon' => 'compass',
        ],
        'engineer' => [
            'label' => 'مهندس و معمار',
            'question' => 'مشخصات فنی و فایل‌های اجرایی کجاست؟',
            'cta' => 'مرکز فنی',
            'route' => 'technical.index',
            'icon' => 'blueprint',
        ],
        'contractor' => [
            'label' => 'پیمانکار و مجری',
            'question' => 'روش صحیح اجرا چیست؟',
            'cta' => 'راهنمای اجرا',
            'route' => 'technical.installation',
            'icon' => 'trowel',
        ],
    ],

    'seo' => [
        'default_title' => 'تولید بلوک سفالی و مصالح ساختمانی سرامیکی',
        'default_description' => 'تولید بلوک‌های سفالی و مصالح ساختمانی سرامیکی با تمرکز بر دوام، عایق‌کاری و عملکرد مهندسی — به‌همراه دیتاشیت، فایل CAD و آبجکت BIM برای مهندسان و معماران.',
        'og_image' => '/og-image.svg',
    ],
];
