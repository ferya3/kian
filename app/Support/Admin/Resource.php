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

    /** @return array<int, Field> */
    public static function formFields(bool $creating): array
    {
        return array_values(array_filter(static::fields(), function (Field $field) use ($creating) {
            if (! $field->isEditable()) {
                return false;
            }

            return $creating ? ! $field->onlyOnEdit : ! $field->onlyOnCreate;
        }));
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
