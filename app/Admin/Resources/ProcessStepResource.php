<?php

namespace App\Admin\Resources;

use App\Models\ProcessStep;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;

class ProcessStepResource extends Resource
{
    public static string $model = ProcessStep::class;

    public static string $slug = 'process-steps';

    public static string $label = 'مراحل تولید';

    public static string $singular = 'مرحله تولید';

    public static string $icon = 'factory';

    public static string $group = 'کارخانه';

    public static string $orderBy = 'step_no';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['title', 'title_en'];
    }

    public static function fields(): array
    {
        return [
            Field::number('step_no', 'شماره مرحله')->rules(['required', 'min:1', 'max:99'])->inList(true)->third(),
            Field::text('title', 'عنوان')->rules(['required'])->inList()->third(),
            Field::text('title_en', 'عنوان انگلیسی')->rules(['nullable'])->third(),
            Field::slug('slug')->rules(['required'])->half(),
            Field::text('duration', 'مدت زمان')->rules(['nullable'])
                ->hint('مثلاً: ۲۴ ساعت — خالی یعنی نمایش داده نشود.')->half(),
            Field::textarea('summary', 'خلاصه')->rules(['nullable', 'max:300']),
            Field::textarea('description', 'توضیح')->rules(['required', 'max:1200']),
            Field::text('metric_label', 'عنوان شاخص')->rules(['nullable'])
                ->hint('مثلاً: دمای پخت')->half(),
            Field::text('metric_value', 'مقدار شاخص')->rules(['nullable'])
                ->hint('مثلاً: ۹۰۰ °C')->inList()->half(),
        ];
    }
}
