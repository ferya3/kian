<?php

namespace App\Admin\Resources;

use App\Models\Redirect;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

/**
 * ریدایرکتِ نشانی‌های سایتِ قدیم.
 *
 * سه منبع پرش می‌کنند: مدیر دستی، دستورِ redirects:import (از آرشیوِ وب یا
 * فهرستِ سرچ کنسول)، و خودِ سایت — هر ۴۰۴ اینجا با تعدادِ بازدید ثبت
 * می‌شود. پرتکرارها بالای فهرست‌اند: همان‌ها اول مقصد می‌خواهند.
 */
class RedirectResource extends Resource
{
    public static string $model = Redirect::class;

    public static string $slug = 'redirects';

    public static string $label = 'ریدایرکت نشانی‌های قدیم';

    public static string $singular = 'ریدایرکت';

    public static string $icon = 'external';

    public static string $group = 'سیستم';

    public static bool $adminOnly = true;

    public static string $orderBy = 'hits';

    public static string $orderDir = 'desc';

    public static int $perPage = 50;

    public static function searchable(): array
    {
        return ['from_path', 'to_url', 'note'];
    }

    public static function titleFor(Model $record): string
    {
        return $record->from_path;
    }

    public static function beforeSave(array $data, ?Model $record): array
    {
        if (isset($data['from_path'])) {
            $data['from_path'] = Redirect::normalize($data['from_path']);

            $taken = Redirect::query()->where('from_path', $data['from_path'])
                ->when($record, fn ($q) => $q->whereKeyNot($record->getKey()))
                ->exists();

            if ($taken) {
                throw ValidationException::withMessages(['from_path' => 'برای این نشانی پیش‌تر ریدایرکت ثبت شده است.']);
            }
        }

        if (! empty($data['to_url'])) {
            // فقط همین سایت و زیردامنه‌هایش — Redirect::safeTarget
            $to = Redirect::safeTarget($data['to_url']);
            if ($to === null) {
                throw ValidationException::withMessages(['to_url' => 'مقصد باید مسیری در همین سایت (مثل ‎/fa/products) یا نشانی‌ای روی همین دامنه باشد.']);
            }

            $from = $data['from_path'] ?? $record?->from_path;
            if ($from !== null && Redirect::normalize($to) === $from) {
                throw ValidationException::withMessages(['to_url' => 'مقصد همان نشانیِ مبدأ است و حلقه می‌سازد.']);
            }

            $data['to_url'] = $to;
        }

        return $data;
    }

    public static function fields(): array
    {
        return [
            Field::text('from_path', 'نشانی قدیم')->rules(['required', 'max:690'])
                ->hint('نشانیِ کامل یا فقط مسیر — مثل ‎/محصولات/بلوک-سفالی/ یا https://kiyanbehsaz.com/?p=12. فارسی و درصدی هر دو قبول است.')
                ->inList(true),
            Field::text('to_url', 'مقصد')->rules(['nullable', 'max:690'])
                ->hint('مثل ‎/fa/products یا ‎/fa/contact. خالی بماند، نشانی ۴۰۴ می‌دهد و فقط شمرده می‌شود.')
                ->inList(),
            Field::select('status_code', 'نوع', [
                301 => 'دائمی (۳۰۱) — برای گوگل',
                302 => 'موقت (۳۰۲)',
                410 => 'حذف‌شده (۴۱۰) — جایگزینی ندارد',
            ])->rules(['required'])->default(301)->inList()->half(),
            Field::boolean('is_active', 'فعال')->default(true)->inList()->half(),
            Field::text('note', 'یادداشت')->rules(['nullable', 'max:250']),
            Field::readonly('hits', 'بازدید')->inList(true)->third(),
            Field::readonly('last_hit_at', 'آخرین بازدید')->third(),
            Field::readonly('source', 'منبع')->inList()->third(),
        ];
    }
}
