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

    public static function currency(): string
    {
        return (string) config('shop.currency', 'تومان');
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
