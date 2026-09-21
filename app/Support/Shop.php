<?php

namespace App\Support;

/**
 * کلیدِ فروشگاه.
 *
 * یک جا پرسیده می‌شود و همه‌جا از همین‌جا خوانده می‌شود: مسیرها، منو، پنل و
 * ویوها. دلیلش این است که «خاموش» باید یک معنا داشته باشد و نه پنج تا —
 * وگرنه روزی مسیر خاموش می‌ماند ولی پیوندش در فوتر می‌ماند.
 */
class Shop
{
    public static function enabled(): bool
    {
        return (bool) config('shop.enabled', false);
    }

    /**
     * واحد پول، به زبانِ صفحه.
     *
     * پیکربندی می‌گوید *کدام* واحد است (اگر روزی ریال شد، یک خط عوض می‌شود)
     * و پرونده‌ی زبان می‌گوید چطور نوشته می‌شود. پرونده‌ی فارسی کلیدش را
     * ندارد، پس همان مقدارِ پیکربندی می‌ماند.
     */
    public static function currency(): string
    {
        $key = 'site.shop.currency.'.config('shop.currency', 'تومان');
        $translated = __($key);

        return is_string($translated) && $translated !== $key
            ? $translated
            : (string) config('shop.currency', 'تومان');
    }

    /** برچسب یک وضعیت سفارش. */
    public static function status(string $status): string
    {
        $key = "site.shop.status_label.{$status}";
        $translated = __($key);

        return is_string($translated) && $translated !== $key
            ? $translated
            : (config('shop.statuses')[$status] ?? $status);
    }

    /**
     * مبلغ، با جداکننده‌ی هزارگان و ارقام فارسی.
     *
     * واحد از پیکربندی می‌آید و در ویوها تکرار نمی‌شود؛ اگر روزی ریال شد،
     * یک خط عوض می‌شود.
     */
    public static function price(?int $amount): string
    {
        if ($amount === null) {
            return '—';
        }

        return Jalali::digits(number_format($amount)).' '.static::currency();
    }
}
