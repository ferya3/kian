<?php

namespace App\Support;

use App\Models\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * زبان‌های سایت.
 *
 * دو منبع دارد و این کلاس تنها جایی است که هر دو را می‌شناسد:
 *
 *   • config/locales.php — چه زبان‌هایی *وجود دارند* و مشخصاتشان چیست. کد
 *     است، چون هر زبان پوشه‌ی lang و ترجمه‌ی محتوا می‌خواهد.
 *   • جدول locales — کدام‌ها *همین حالا روشن‌اند*. تصمیم مدیر است و از پنل
 *     عوض می‌شود، بی‌آنکه کسی به پرونده‌ای دست بزند.
 *
 * پس دو فهرست داریم و خلط‌شدنشان گران تمام می‌شود:
 *   declared() = هرچه اعلام شده → قیدِ مسیرها. ثابت است و به دیتابیس کار
 *     ندارد، پس route:cache سالم می‌ماند و خاموش‌کردن زبان مسیرها را کهنه
 *     نمی‌کند.
 *   all() = هرچه روشن است → هر چیزی که کاربر می‌بیند: سوئیچر، hreflang،
 *     نقشه‌ی سایت، فرم ترجمه‌ی پنل.
 *
 * فاصله‌ی میان این دو، همان زبانِ خاموش است: مسیرش هنوز می‌خورد تا SetLocale
 * بتواند با ۳۰۱ به زبان پیش‌فرض بفرستدش، و نه ۴۰۴ بدهد یا — بدتر — همان صفحه
 * را زیر نشانیِ دیگری دوباره منتشر کند.
 *
 * نکته‌ی مسیرها: همه‌ی صفحه‌های عمومی پیشوند زبان دارند، حتی زبان پیش‌فرض.
 * تقارنِ /fa/products و /en/products ارزشش را دارد: با پیشوندِ اختیاری،
 * هر ۱۳۲ فراخوانیِ route() در ویوها باید عوض می‌شد.
 */
class Locales
{
    public const COOKIE = 'locale';

    /**
     * هر زبانی که اعلام شده — روشن یا خاموش.
     *
     * قیدِ پیشوند مسیرها از همین‌جا می‌آید و نه از all(): مسیرها یک‌بار هنگام
     * بوت ساخته می‌شوند و کش می‌شوند، پس قیدشان نباید به حالتی وابسته باشد
     * که مدیر وسط کار عوضش می‌کند.
     *
     * @return Collection<string, array>
     */
    public static function declared(): Collection
    {
        return collect(config('locales.available', []));
    }

    /** @return array<int, string> */
    public static function declaredCodes(): array
    {
        return static::declared()->keys()->all();
    }

    /**
     * فقط زبان‌های روشن، به ترتیبی که مدیر خواسته.
     *
     * حالت از جدول می‌آید و مشخصات از تنظیمات. اگر جدول هنوز نباشد — نصبِ
     * تازه، پیش از اجرای مهاجرت‌ها — به کلید enabledِ خودِ تنظیمات برمی‌گردیم
     * تا سایت بالا بیاید.
     *
     * @return Collection<string, array>
     */
    public static function all(): Collection
    {
        $state = Locale::mapOrNull();

        if ($state === null) {
            return static::declared()->filter(fn (array $meta) => $meta['enabled'] ?? true);
        }

        $declared = static::declared();

        $on = collect($state)
            // زبانی که ردیف دارد ولی دیگر در تنظیمات نیست، زبان نیست
            ->filter(fn (array $row, string $code) => $declared->has($code) && $row['is_active'])
            ->map(fn (array $row, string $code) => $declared->get($code));

        /*
         * زبان پیش‌فرض همیشه هست.
         *
         * مدل هم جلوی خاموش‌کردنش را می‌گیرد، ولی یک UPDATE مستقیم روی
         * دیتابیس از مدل رد می‌شود و از این خط نه. بی این، یک دستور SQL
         * می‌توانست سایت را بدون هیچ زبانی بگذارد.
         */
        $default = static::default();

        if (! $on->has($default) && $declared->has($default)) {
            $on = $on->prepend($declared->get($default), $default);
        }

        return $on;
    }

    /** @return array<int, string> */
    public static function codes(): array
    {
        return static::all()->keys()->all();
    }

    /** زبانی که اعلام شده ولی مدیر خاموشش کرده. */
    public static function isDisabled(?string $code): bool
    {
        return $code !== null && static::declared()->has($code) && ! static::supports($code);
    }

    public static function default(): string
    {
        return config('locales.default', 'fa');
    }

    public static function current(): string
    {
        return static::supports(app()->getLocale()) ? app()->getLocale() : static::default();
    }

    public static function supports(?string $code): bool
    {
        return $code !== null && static::all()->has($code);
    }

    /** @return array<string, mixed> */
    public static function meta(?string $code = null): array
    {
        $code ??= static::current();

        return static::all()->get($code) ?? static::all()->get(static::default()) ?? [];
    }

    public static function dir(?string $code = null): string
    {
        return static::meta($code)['dir'] ?? 'rtl';
    }

    public static function isRtl(?string $code = null): bool
    {
        return static::dir($code) === 'rtl';
    }

    public static function html(?string $code = null): string
    {
        return static::meta($code)['html'] ?? $code ?? static::default();
    }

    public static function name(?string $code = null): string
    {
        return static::meta($code)['name'] ?? (string) $code;
    }

    public static function digits(?string $code = null): string
    {
        return static::meta($code)['digits'] ?? 'latn';
    }

    public static function calendar(?string $code = null): string
    {
        return static::meta($code)['calendar'] ?? 'gregorian';
    }

    public static function isDefault(?string $code = null): bool
    {
        return ($code ?? static::current()) === static::default();
    }

    /**
     * همین صفحه، به زبانی دیگر.
     *
     * از مسیر جاری ساخته می‌شود و نه از رشته‌ی URL، تا پارامترها — شناسه‌ی
     * محصول، فیلتر دسته، شماره‌ی صفحه — سرِ جایشان بمانند.
     */
    public static function urlFor(string $code, ?Request $request = null): string
    {
        $request ??= request();
        $route = $request->route();

        if ($route === null || ! $route->getName()) {
            return url($code);
        }

        return route(
            $route->getName(),
            array_merge($route->parameters(), $request->query(), ['locale' => $code]),
        );
    }

    /**
     * زبانی که بازدیدکننده احتمالاً می‌خواهد.
     *
     * اول انتخاب قبلی خودش، بعد Accept-Language مرورگر، و در نهایت پیش‌فرض.
     */
    public static function preferred(?Request $request = null): string
    {
        $request ??= request();

        $chosen = $request->cookie(static::COOKIE);

        if (static::supports($chosen)) {
            return $chosen;
        }

        return $request->getPreferredLanguage(static::codes()) ?? static::default();
    }
}
