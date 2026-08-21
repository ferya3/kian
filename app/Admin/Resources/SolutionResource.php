<?php

namespace App\Admin\Resources;

use App\Models\Solution;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;
use Illuminate\Database\Eloquent\Model;

class SolutionResource extends Resource
{
    public static string $model = Solution::class;

    public static string $slug = 'solutions';

    public static string $label = 'راهکارها';

    public static string $singular = 'راهکار';

    public static string $icon = 'compass';

    public static string $group = 'کاتالوگ';

    public static string $orderBy = 'position';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['title', 'title_en', 'summary'];
    }

    public static function publicUrl(Model $record): ?string
    {
        return route('solutions.show', $record);
    }

    public static function fields(): array
    {
        return [
            Field::text('title', 'عنوان')->rules(['required'])->inList(true)->half(),
            Field::text('title_en', 'عنوان انگلیسی')->rules(['nullable'])->half(),
            Field::slug('slug')->rules(['required'])->half(),
            Field::text('subtitle', 'زیرعنوان')->rules(['nullable'])->half(),
            Field::textarea('summary', 'خلاصه')->rules(['nullable', 'max:600']),
            Field::longtext('description', 'توضیح کامل')->rules(['nullable', 'max:6000']),
            Field::lines('benefits', 'مزایا')->rules(['nullable']),
            Field::number('position', 'ترتیب')->rules(['nullable', 'min:0', 'max:999'])->half(),
        ];
    }
}
