<?php

namespace App\Admin\Resources;

use App\Models\User;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;

class UserResource extends Resource
{
    public static string $model = User::class;

    public static string $slug = 'users';

    public static string $label = 'کاربران پنل';

    public static string $singular = 'کاربر';

    public static string $icon = 'shield';

    public static string $group = 'سیستم';

    public static bool $adminOnly = true;

    public static string $orderBy = 'id';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['name', 'email'];
    }

    public static function fields(): array
    {
        return [
            Field::text('name', 'نام و نام خانوادگی')->rules(['required', 'max:120'])->inList(true)->half(),
            Field::email('email', 'ایمیل')->rules(['required', 'unique:users,email'])->inList()->half(),
            Field::select('role', 'نقش', User::ROLES)->rules(['required'])->inList()->half(),
            Field::boolean('is_active', 'فعال')->default(true)->inList()->half(),
            Field::password('password', 'گذرواژه')->rules(['required', 'confirmed'])
                ->hint('حداقل ۱۰ نویسه. ترکیب حروف، رقم و نماد.')
                ->onlyOnCreate(),
            Field::password('password', 'گذرواژه جدید')->rules(['nullable', 'confirmed'])
                ->hint('برای بدون‌تغییر ماندن، خالی بگذارید.')
                ->onlyOnEdit(),
            Field::readonly('last_login_at', 'آخرین ورود')->inList(),
        ];
    }
}
