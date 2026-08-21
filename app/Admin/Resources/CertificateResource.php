<?php

namespace App\Admin\Resources;

use App\Models\Certificate;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;

class CertificateResource extends Resource
{
    public static string $model = Certificate::class;

    public static string $slug = 'certificates';

    public static string $label = 'گواهی‌نامه‌ها';

    public static string $singular = 'گواهی‌نامه';

    public static string $icon = 'shield';

    public static string $group = 'کارخانه';

    public static string $orderBy = 'position';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['title', 'issuer', 'number'];
    }

    public static function fields(): array
    {
        return [
            Field::text('title', 'عنوان')->rules(['required'])->inList(true),
            Field::text('issuer', 'صادرکننده')->rules(['nullable'])->inList()->half(),
            Field::text('number', 'شماره')->rules(['nullable', 'max:60'])->half(),
            Field::number('year', 'سال (شمسی)')->rules(['nullable', 'min:1300', 'max:1500'])->inList()->half(),
            Field::number('position', 'ترتیب')->rules(['nullable', 'min:0', 'max:999'])->half(),
            Field::file('file_path', 'فایل گواهی‌نامه', 'certificates')->rules(['nullable']),
        ];
    }
}
