<?php

namespace App\Admin\Resources;

use App\Models\Document;
use App\Models\Product;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;

class DocumentResource extends Resource
{
    public static string $model = Document::class;

    public static string $slug = 'documents';

    public static string $label = 'فایل‌های فنی';

    public static string $singular = 'فایل فنی';

    public static string $icon = 'file';

    public static string $group = 'مرکز فنی';

    public static string $orderBy = 'position';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['title', 'title_en', 'description'];
    }

    public static function with(): array
    {
        return ['product'];
    }

    public static function fields(): array
    {
        return [
            Field::text('title', 'عنوان فایل')->rules(['required'])->inList(true),
            Field::text('title_en', 'عنوان انگلیسی')->rules(['nullable'])->half(),
            Field::slug('slug')->rules(['required'])->half(),
            Field::select('category', 'نوع فایل', Document::CATEGORIES)->rules(['required'])->inList()->third(),
            Field::select('audience', 'مخاطب', Document::AUDIENCES)->rules(['required'])->inList()->third(),
            Field::select('format', 'فرمت', [
                'pdf' => 'PDF', 'dwg' => 'DWG', 'dxf' => 'DXF', 'rvt' => 'Revit',
                'ifc' => 'IFC', 'skp' => 'SketchUp', 'xlsx' => 'Excel', 'zip' => 'ZIP',
            ])->rules(['required'])->inList()->third(),
            Field::relation('product_id', 'محصول مرتبط', Product::class)
                ->rules(['nullable', 'exists:products,id'])
                ->hint('برای فایل‌های عمومی خالی بگذارید.')->half(),
            Field::text('version', 'نسخه')->rules(['nullable', 'max:40'])->half()->section('فایل'),
            Field::textarea('description', 'توضیح')->rules(['nullable', 'max:600'])->section('فایل'),
            Field::file('file_path', 'فایل', 'documents')
                ->rules(['nullable'])
                ->hint('حداکثر ۲۰ مگابایت. تا وقتی فایلی آپلود نشده، دکمه‌ی دانلود کاربر را به فرم درخواست می‌برد.')
                ->section('فایل'),
            Field::number('position', 'ترتیب')->rules(['nullable', 'min:0', 'max:9999'])->half()->section('انتشار'),
            Field::readonly('download_count', 'تعداد دانلود')->inList()->section('انتشار'),
        ];
    }
}
