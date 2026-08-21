<?php

namespace App\Admin\Resources;

use App\Models\Faq;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;

class FaqResource extends Resource
{
    public static string $model = Faq::class;

    public static string $slug = 'faqs';

    public static string $label = 'پرسش‌های متداول';

    public static string $singular = 'پرسش';

    public static string $icon = 'compass';

    public static string $group = 'مرکز فنی';

    public static string $orderBy = 'position';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['question', 'answer'];
    }

    public static function fields(): array
    {
        return [
            Field::text('question', 'پرسش')->rules(['required'])->inList(true),
            Field::textarea('answer', 'پاسخ')->rules(['required', 'max:2000']),
            Field::select('group', 'دسته', [
                'technical' => 'مشخصات فنی',
                'installation' => 'اجرا و کارگاه',
                'order' => 'سفارش و تحویل',
                'general' => 'عمومی',
            ])->rules(['required'])->inList()->half(),
            Field::number('position', 'ترتیب')->rules(['nullable', 'min:0', 'max:999'])->half(),
        ];
    }
}
