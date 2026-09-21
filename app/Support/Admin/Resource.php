<?php

namespace App\Support\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * پایه‌ی یک منبع قابل مدیریت.
 *
 * هر منبع فقط اعلام می‌کند «چه مدلی، چه فیلدهایی، چه برچسبی». ساخت فرم،
 * اعتبارسنجی، جدول، جستجو و ذخیره‌سازی یک‌بار در ResourceController نوشته شده
 * و برای همه‌ی منابع یکسان است.
 */
abstract class Resource
{
    /** @var class-string<Model> */
    public static string $model;

    public static string $slug;

    public static string $label;

    public static string $singular;

    public static string $icon = 'grid';

    public static string $group = 'محتوا';

    public static int $position = 0;

    public static bool $creatable = true;

    public static bool $deletable = true;

    /** فقط مدیر کل — نه ویرایشگر محتوا. */
    public static bool $adminOnly = false;

    /**
     * نقش‌هایی که این منبع را می‌بینند. آرایه‌ی خالی یعنی «کارکنان سایت».
     *
     * تا پیش از فروشگاه، هرکه وارد پنل می‌شد کارمندِ سایت بود و همه‌چیز را
     * می‌دید؛ فروشنده کارمند نیست. پس به‌جای افزودنِ شرطِ «و فروشنده نباشد»
     * به هر منبع — که یکی‌اش فراموش می‌شد و دادهٔ بقیه را لو می‌داد — خودِ
     * منبع اعلام می‌کند مالِ چه کسی است.
     *
     * @var array<int, string>
     */
    public static array $roles = [];

    /**
     * در فهرست گروه‌بندی‌شده‌ی منو دیده شود؟
     *
     * منبعی که پیوند سنجاق‌شده‌ی خودش را در بالای منو دارد، اینجا false می‌شود
     * تا دوبار فهرست نشود.
     */
    public static bool $inNavigation = true;

    public static string $orderBy = 'id';

    public static string $orderDir = 'desc';

    public static int $perPage = 25;

    /** @return array<int, Field> */
    abstract public static function fields(): array;

    /** ستون‌هایی که در جستجوی فهرست بررسی می‌شوند. */
    public static function searchable(): array
    {
        return ['name'];
    }

    /** روابطی که برای جدول eager-load می‌شوند. */
    public static function with(): array
    {
        return [];
    }

    /**
     * یک رکورد — از همان کوئریِ منبع و نه مستقیم از مدل.
     *
     * تفاوتش امنیتی است: منبعی که query را قید می‌زند (مثل عرضه‌ها که به
     * فروشگاهِ کاربر محدود می‌شود) باید همان قید را در ویرایش و حذف هم
     * داشته باشد. با findOrFail روی مدل، فهرست قید می‌خورد ولی نشانیِ
     * ویرایش نه — و حدسِ یک id کافی بود.
     */
    public static function findOrFail(string|int $id): Model
    {
        return static::query()->findOrFail($id);
    }

    /**
     * آخرین فرصتِ منبع پیش از ذخیره.
     *
     * برای مقدارهایی که نباید از فرم بیایند — مثل شناسه‌ی فروشگاه، که از
     * کاربرِ واردشده خوانده می‌شود و نه از چیزی که مرورگر فرستاده.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function beforeSave(array $data, ?Model $record): array
    {
        return $data;
    }

    public static function query(): Builder
    {
        return static::$model::query()->with(static::with());
    }

    /** عنوان خوانای یک رکورد در فهرست، نان و لاگ. */
    public static function titleFor(Model $record): string
    {
        foreach (['title', 'name', 'question', 'label', 'key'] as $column) {
            if (filled($record->{$column} ?? null)) {
                return (string) $record->{$column};
            }
        }

        return static::$singular.' #'.$record->getKey();
    }

    /** @return array<int, Field> */
    public static function listFields(): array
    {
        return array_values(array_filter(static::fields(), fn (Field $f) => $f->inList));
    }

    /**
     * فیلدهایی که در فرم *دیده* می‌شوند — شامل فیلدهای فقط‌خواندنی.
     *
     * فیلد readonly برای نمایش زمینه است (کلید تنظیم، جایگاه تصویر در طراحی)
     * و بدون آن مدیر نمی‌فهمد دارد کدام رکورد را ویرایش می‌کند.
     *
     * @return array<int, Field>
     */
    public static function formFields(bool $creating): array
    {
        return array_values(array_filter(
            static::fields(),
            fn (Field $field) => $creating ? ! $field->onlyOnEdit : ! $field->onlyOnCreate
        ));
    }

    /**
     * فیلدهایی که از فرم *ذخیره* می‌شوند.
     *
     * عمداً از formFields جداست: هرچه readonly است نه اعتبارسنجی می‌شود نه در
     * دیتابیس می‌نشیند، حتی اگر مرورگر مقداری برایش بفرستد.
     *
     * @return array<int, Field>
     */
    public static function savableFields(bool $creating): array
    {
        return array_values(array_filter(
            static::formFields($creating),
            fn (Field $field) => $field->isEditable()
        ));
    }

    public static function field(string $key): ?Field
    {
        foreach (static::fields() as $field) {
            if ($field->key === $key) {
                return $field;
            }
        }

        return null;
    }

    /*
    | آدرس‌های پنل عمداً با کلید اصلی ساخته می‌شوند.
    | چند مدل getRouteKeyName را روی slug گذاشته‌اند تا URL عمومی خوانا باشد؛
    | اگر پنل هم از همان استفاده کند، findOrFail با slug شکست می‌خورد.
    */
    public static function editUrl(Model $record): string
    {
        return route('admin.resource.edit', [static::$slug, $record->getKey()]);
    }

    public static function updateUrl(Model $record): string
    {
        return route('admin.resource.update', [static::$slug, $record->getKey()]);
    }

    public static function destroyUrl(Model $record): string
    {
        return route('admin.resource.destroy', [static::$slug, $record->getKey()]);
    }

    /** پیوند «مشاهده در سایت» برای رکوردهایی که صفحه‌ی عمومی دارند. */
    public static function publicUrl(Model $record): ?string
    {
        return null;
    }
}
