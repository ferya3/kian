<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Admin\Field;
use App\Support\Admin\Registry;
use App\Support\Media;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * کتابخانه‌ی تصاویر — نمای یک‌جا از همه‌ی عکس‌های سایت.
 *
 * تصویرها مالک دارند: هر عکس به یک رکورد (محصول، پروژه، مرحله…) وابسته است و
 * ویرایشش در فرم همان رکورد انجام می‌شود. این صفحه جای آپلود نیست؛ جایی است
 * که می‌بینید چه چیزی کجا نشسته و کدام بخش هنوز عکس ندارد.
 */
class MediaController extends Controller
{
    public function __invoke()
    {
        $groups = collect(Registry::all())
            ->filter(fn (string $resource) => Registry::accessible($resource))
            ->map(fn (string $resource) => $this->groupFor($resource))
            ->filter(fn (?array $group) => $group !== null)
            ->values();

        $used = $groups->flatMap(fn (array $g) => collect($g['items'])->pluck('path'))->all();

        return view('admin.media', [
            'groups' => $groups,
            'totalImages' => count($used),
            'orphans' => $this->orphans($used),
        ]);
    }

    /** @return array{label: string, slug: string, items: array, missing: int}|null */
    protected function groupFor(string $resource): ?array
    {
        $fields = collect($resource::fields())
            ->filter(fn (Field $f) => in_array($f->type, ['image', 'gallery'], true));

        if ($fields->isEmpty()) {
            return null;
        }

        $items = [];
        $missing = 0;

        foreach ($resource::$model::all() as $record) {
            $before = count($items);

            foreach ($fields as $field) {
                $paths = $field->type === 'gallery'
                    ? collect((array) $record->{$field->key})
                    : collect([$record->{$field->key}]);

                foreach ($paths->filter() as $path) {
                    $items[] = [
                        'path' => $path,
                        'url' => Media::url($path),
                        'field' => $field->label,
                        'owner' => $resource::titleFor($record),
                        'edit' => $resource::editUrl($record),
                    ];
                }
            }

            if (count($items) === $before) {
                $missing++;
            }
        }

        return [
            'label' => $resource::$label,
            'slug' => $resource::$slug,
            'items' => $items,
            'missing' => $missing,
        ];
    }

    /**
     * فایل‌هایی که روی دیسک مانده‌اند ولی هیچ رکوردی به آن‌ها اشاره نمی‌کند.
     *
     * فقط گزارش می‌شود، حذف نمی‌شود: تشخیص «بی‌استفاده» به اسکن همه‌ی رکوردها
     * تکیه دارد و یک اشتباه در آن یعنی حذف فایل زنده.
     */
    protected function orphans(array $used): Collection
    {
        $disk = Storage::disk(Media::$disk);

        if (! $disk->exists('admin')) {
            return collect();
        }

        return collect($disk->allFiles('admin'))
            ->reject(fn (string $path) => in_array($path, $used, true))
            ->map(fn (string $path) => [
                'path' => $path,
                'url' => Media::url($path),
                'size_kb' => (int) ceil($disk->size($path) / 1024),
            ])
            ->values();
    }
}
