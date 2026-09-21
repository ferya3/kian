<?php

namespace App\Admin\Resources;

use App\Models\Locale;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;
use App\Support\Locales;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * زبان‌های سایت — روشن، خاموش، و ترتیبشان در سوئیچر.
 *
 * ساخت و حذف بسته است و این محدودیت عمدی است: زبان با یک ردیف در دیتابیس به
 * وجود نمی‌آید، با پوشه‌ی lang و ترجمه‌ی محتوا به وجود می‌آید. ردیفی که پشتش
 * پرونده‌ی رشته‌ها نباشد، صفحه‌ای تولید می‌کند که همه‌اش به فارسی برگشته.
 *
 * پس اینجا فقط یک تصمیم گرفته می‌شود: کدام زبانِ *آماده* روی سایت باشد.
 */
class LocaleResource extends Resource
{
    public static string $model = Locale::class;

    public static string $slug = 'locales';

    public static string $label = 'زبان‌های سایت';

    public static string $singular = 'زبان';

    public static string $icon = 'globe';

    public static string $group = 'سیستم';

    public static bool $adminOnly = true;

    public static bool $creatable = false;

    public static bool $deletable = false;

    public static string $orderBy = 'order';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['code'];
    }

    /**
     * پیش از نمایش فهرست، زبان‌های تازه‌ی تنظیمات ردیف می‌گیرند.
     *
     * زبان با به‌روزرسانیِ کد می‌آید، ولی مسیر استقرار فقط مهاجرت‌ها را اجرا
     * می‌کند؛ بدون این، مدیر زبانِ تازه را در پنل نمی‌دید. خاموش ساخته می‌شود
     * تا لحظه‌ی انتشارش با مدیر باشد.
     */
    public static function query(): Builder
    {
        Locale::sync();

        return parent::query();
    }

    public static function titleFor(Model $record): string
    {
        return $record->name;
    }

    public static function fields(): array
    {
        return [
            Field::readonly('name', 'زبان')->inList(),
            Field::readonly('code', 'کد')->inList(),
            Field::readonly('direction', 'جهت نوشتار')->inList(),

            /*
             * روی ردیفِ زبان پیش‌فرض، تیک جای خود را به یک جمله می‌دهد.
             *
             * برداشتنِ آن تیک بی‌اثر است — هم مدل و هم Locales::all جلویش را
             * می‌گیرند — و کنترلی که کار نکند بدتر از کنترلی است که نباشد.
             * فیلد readonly ذخیره هم نمی‌شود، پس یک مسیرِ نوشتن کمتر.
             *
             * شرطِ id فقط در فرم برقرار است؛ در فهرست، ستونِ «روشن روی سایت»
             * باید برای همه‌ی ردیف‌ها باشد.
             */
            self::editingDefault()
                ? Field::readonly('active_state', 'روشن روی سایت')->inList()
                : Field::boolean('is_active', 'روشن روی سایت')->hint(self::activeHint())->inList(),

            Field::number('order', 'ترتیب در سوئیچر')
                ->rules(['required', 'integer', 'min:0', 'max:9999'])
                ->hint('کوچک‌تر، جلوتر.')
                ->half()
                ->inList(true),
        ];
    }

    /**
     * زبان پیش‌فرض خاموش نمی‌شود.
     *
     * سه سد برای یک قاعده، چون هرکدام جای دیگری را می‌پوشانند: این یکی فرمِ
     * پنل را، هوکِ saving مدل هر نوشتنِ دیگری در کد را، و Locales::all حتی
     * دستور SQL مستقیم را. بی‌آخری، یک UPDATE می‌توانست سایت را بی‌زبان کند.
     */
    public static function beforeSave(array $data, ?Model $record): array
    {
        if ($record && $record->code === Locales::default()) {
            $data['is_active'] = true;
        }

        return $data;
    }

    /** آیا همین حالا فرمِ ویرایشِ زبان پیش‌فرض باز است؟ */
    protected static function editingDefault(): bool
    {
        $id = request()->route('id');

        return $id !== null
            && Locale::query()->whereKey($id)->value('code') === Locales::default();
    }

    protected static function activeHint(): string
    {
        return 'زبان خاموش از سوئیچر، hreflang و نقشه‌ی سایت حذف می‌شود و '
            .'نشانی‌هایش با ۳۰۱ به «'.Locales::name(Locales::default()).'» می‌روند. '
            .'ترجمه‌های واردشده پاک نمی‌شوند و با روشن‌کردن دوباره برمی‌گردند. '
            .'زبان پیش‌فرض سایت خاموش نمی‌شود.';
    }
}
