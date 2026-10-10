<?php

namespace App\Admin\Resources;

use App\Models\Location;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;

/**
 * نشانی‌ها — کارخانه، دفتر فروش، انبار، هر چند تا که باشد.
 *
 * تلفن و ایمیلِ کلیِ شرکت در «اطلاعات تماس» است؛ اینجا فقط آنچه به یک
 * مکان بند است: نشانی، ساعت کاری، تلفنِ همان‌جا و نقطه‌ی نقشه.
 */
class LocationResource extends Resource
{
    public static string $model = Location::class;

    public static string $slug = 'locations';

    public static string $label = 'نشانی‌ها و ساعت کاری';

    public static string $singular = 'نشانی';

    public static string $icon = 'pin';

    public static string $group = 'تماس';

    public static bool $adminOnly = true;

    public static string $orderBy = 'position';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['title', 'address', 'city'];
    }

    public static function fields(): array
    {
        $phone = 'regex:/^[+\d۰-۹٠-٩\s\-()]{3,30}$/u';

        return [
            Field::text('title', 'عنوان')->rules(['required', 'max:120'])
                ->hint('مثل: کارخانه، دفتر فروش تهران، انبار')
                ->inList(true)->half(),
            Field::number('position', 'ترتیب')->rules(['nullable', 'min:0', 'max:999'])
                ->hint('کوچک‌ترین عدد، نشانیِ اصلی است: ساعت کاری‌اش در نوار بالای سایت می‌آید.')
                ->inList()->half(),

            Field::textarea('address', 'نشانی کامل')->rules(['required', 'max:300'])->inList(),
            Field::text('working_hours', 'ساعت کاری')->rules(['nullable', 'max:200'])
                ->hint('مثل: شنبه تا چهارشنبه ۸ تا ۱۷ — پنجشنبه ۸ تا ۱۳')
                ->inList(),
            Field::text('phone', 'تلفن همین نشانی')->rules(['nullable', $phone])
                ->hint('اختیاری. خالی بماند، فقط تلفن مرکزی نشان داده می‌شود.')
                ->half(),
            Field::text('postal_code', 'کد پستی')->rules(['nullable', 'regex:/^[\d۰-۹٠-٩\-\s]{5,12}$/u'])->half(),
            Field::text('region', 'استان')->rules(['nullable', 'max:60'])->half(),
            Field::text('city', 'شهر')->rules(['nullable', 'max:60'])->inList()->half(),

            Field::decimal('lat', 'عرض جغرافیایی')->rules(['nullable', 'numeric', 'between:-90,90'])
                ->hint('از گوگل‌مپ: کلیک راست روی محل، عدد اول.')
                ->section('نقشه')->half(),
            Field::decimal('lng', 'طول جغرافیایی')->rules(['nullable', 'numeric', 'between:-180,180'])
                ->hint('عدد دوم. بی مختصات، دکمه‌ی مسیریابی خودِ نشانی را جستجو می‌کند.')
                ->section('نقشه')->half(),

            Field::boolean('is_active', 'نمایش در سایت')->default(true)->inList()->half(),
        ];
    }
}
