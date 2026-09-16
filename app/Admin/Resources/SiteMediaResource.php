<?php

namespace App\Admin\Resources;

use App\Models\SiteMedia;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * تصویرهای ثابت سایت.
 *
 * فهرست جایگاه‌ها از کد می‌آید، پس ساخت و حذف بسته است: مدیر تصویر یک جایگاهِ
 * موجود را عوض می‌کند، نه اینکه جایگاه تازه بسازد. هر ردیف می‌گوید کجای سایت
 * دیده می‌شود تا انتخاب تصویر حدسی نباشد.
 */
class SiteMediaResource extends Resource
{
    public static string $model = SiteMedia::class;

    public static string $slug = 'site-media';

    public static string $label = 'تصاویر سایت';

    public static string $singular = 'تصویر سایت';

    public static string $icon = 'image';

    public static string $group = 'محتوا';

    // پیوند خودش بالای منو، کنار کتابخانه‌ی تصاویر، سنجاق شده است
    public static bool $inNavigation = false;

    public static bool $creatable = false;

    public static bool $deletable = false;

    public static string $orderBy = 'id';

    public static string $orderDir = 'asc';

    public static int $perPage = 50;

    public static function searchable(): array
    {
        return ['key', 'alt'];
    }

    /**
     * پیش از نمایش فهرست، جایگاه‌های تازه ساخته می‌شوند.
     *
     * جایگاه با افزوده‌شدن یک ویو به کد اضافه می‌شود، ولی مسیر به‌روزرسانی فقط
     * مهاجرت‌ها را اجرا می‌کند. بدون این، مدیر پس از هر به‌روزرسانی جایگاه تازه
     * را در پنل نمی‌دید.
     */
    public static function query(): Builder
    {
        SiteMedia::sync();

        return parent::query();
    }

    public static function titleFor(Model $record): string
    {
        return $record->label;
    }

    public static function fields(): array
    {
        return [
            Field::readonly('label', 'جایگاه')->inList(),
            Field::readonly('section', 'بخش')->inList(),
            Field::readonly('placement', 'کجا دیده می‌شود'),

            Field::image('image', 'تصویر', 'site')
                ->rules(['nullable'])
                ->hint('JPG، PNG، WebP یا SVG تا ۴ مگابایت. برای هیرو، تصویر افقی با عرض دست‌کم ۱۹۲۰ پیکسل.')
                ->inList(),

            Field::text('alt', 'متن جایگزین')
                ->rules(['nullable', 'max:190'])
                ->hint('توصیف کوتاه تصویر برای screen reader و موتور جستجو. برای تصویرهای صرفاً تزئینی خالی بماند.'),
        ];
    }
}
