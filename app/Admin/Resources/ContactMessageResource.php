<?php

namespace App\Admin\Resources;

use App\Models\ContactMessage;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;
use Illuminate\Database\Eloquent\Model;

class ContactMessageResource extends Resource
{
    public static string $model = ContactMessage::class;

    public static string $slug = 'messages';

    public static string $label = 'درخواست‌ها';

    public static string $singular = 'درخواست';

    public static string $icon = 'mail';

    public static string $group = 'فروش';

    public static string $orderBy = 'created_at';

    // پیام‌ها فقط از فرم سایت می‌آیند؛ ساختن دستی معنا ندارد.
    public static bool $creatable = false;

    public static function searchable(): array
    {
        return ['name', 'company', 'phone', 'email', 'message'];
    }

    public static function with(): array
    {
        return ['product'];
    }

    public static function titleFor(Model $record): string
    {
        return $record->name.' — '.$record->phone;
    }

    public static function fields(): array
    {
        return [
            // محتوای پیام از سمت کاربر آمده و ویرایش‌شدنی نیست؛
            // فقط وضعیت پیگیری قابل تغییر است.
            Field::readonly('name', 'نام')->inList(),
            Field::readonly('phone', 'تلفن')->inList(),
            Field::readonly('company', 'شرکت / پروژه'),
            Field::readonly('email', 'ایمیل'),
            Field::readonly('city', 'شهر'),
            Field::readonly('type', 'موضوع')->inList(),
            Field::readonly('message', 'متن پیام'),
            Field::readonly('created_at', 'زمان دریافت')->inList(true),
            Field::readonly('ip', 'IP فرستنده'),
            Field::select('status', 'وضعیت پیگیری', [
                'new' => 'جدید',
                'in_progress' => 'در حال پیگیری',
                'done' => 'انجام شد',
                'spam' => 'اسپم',
            ])->rules(['required'])->inList(),
        ];
    }
}
