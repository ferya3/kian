<?php

namespace App\Admin\Resources;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;
use Illuminate\Database\Eloquent\Model;

class ProjectResource extends Resource
{
    public static string $model = Project::class;

    public static string $slug = 'projects';

    public static string $label = 'پروژه‌ها';

    public static string $singular = 'پروژه';

    public static string $icon = 'blueprint';

    public static string $group = 'کاتالوگ';

    public static string $orderBy = 'position';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['title', 'title_en', 'city', 'client'];
    }

    public static function with(): array
    {
        return ['category'];
    }

    public static function publicUrl(Model $record): ?string
    {
        return route('projects.show', $record);
    }

    public static function fields(): array
    {
        return [
            Field::text('title', 'عنوان پروژه')->rules(['required'])->inList(true)->half(),
            Field::text('title_en', 'عنوان انگلیسی')->rules(['nullable'])->half(),
            Field::slug('slug')->rules(['required'])->half(),
            Field::relation('project_category_id', 'دسته‌بندی', ProjectCategory::class)
                ->relationName('category')
                ->rules(['required', 'exists:project_categories,id'])->inList()->half(),
            Field::text('client', 'کارفرما')->rules(['nullable'])->half()->section('مشخصات پروژه'),
            Field::text('architect', 'طراح / مشاور')->rules(['nullable'])->half()->section('مشخصات پروژه'),
            Field::text('city', 'شهر')->rules(['required'])->inList()->third()->section('مشخصات پروژه'),
            Field::text('province', 'استان')->rules(['nullable'])->third()->section('مشخصات پروژه'),
            Field::number('year', 'سال اجرا (شمسی)')->rules(['required', 'min:1300', 'max:1500'])->inList(true)->third()->section('مشخصات پروژه'),
            Field::number('area_sqm', 'زیربنا')->rules(['nullable', 'min:0', 'max:9999999'])->suffix('m²')->half()->section('مشخصات پروژه'),
            Field::number('blocks_used', 'تعداد بلوک مصرفی')->rules(['nullable', 'min:0', 'max:99999999'])->half()->section('مشخصات پروژه'),
            Field::textarea('summary', 'خلاصه')->rules(['nullable', 'max:600'])->section('روایت پروژه'),
            Field::longtext('description', 'مسئله و راه‌حل')->rules(['nullable', 'max:6000'])->section('روایت پروژه'),
            Field::number('position', 'ترتیب')->rules(['nullable', 'min:0', 'max:999'])->half()->section('انتشار'),
            Field::boolean('is_featured', 'نمایش در صفحه اصلی')->inList()->half()->section('انتشار'),
        ];
    }
}
