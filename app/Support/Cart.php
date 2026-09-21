<?php

namespace App\Support;

use App\Models\Offer;
use Illuminate\Support\Collection;

/**
 * سبد خرید، روی نشست.
 *
 * مشتری حساب کاربری ندارد — سایت B2B است و خریدار یک‌بار سفارش می‌دهد و
 * فروشنده تماس می‌گیرد. پس سبد در session می‌ماند و نه در دیتابیس.
 *
 * در نشست فقط «شناسه‌ی عرضه ← تعداد» ذخیره می‌شود و نه قیمت. قیمتِ ذخیره‌شده
 * در نشست یعنی مشتری می‌تواند سبدِ دیروز را با قیمتِ دیروز ثبت کند، یا بدتر،
 * با دستکاری کوکی هر قیمتی بسازد. قیمت هر بار از دیتابیس خوانده می‌شود و
 * فقط در لحظه‌ی ثبتِ سفارش روی ردیف‌ها کپی می‌شود.
 */
class Cart
{
    protected const KEY = 'shop.cart';

    /** @return array<int, int> شناسه‌ی عرضه ← تعداد */
    public static function raw(): array
    {
        return session()->get(static::KEY, []);
    }

    /**
     * ردیف‌های سبد، همراه عرضه‌ی زنده‌ی هرکدام.
     *
     * عرضه‌هایی که دیگر قابل فروش نیستند — فروشنده خاموشش کرده، محصول
     * برداشته شده — بی‌صدا کنار گذاشته می‌شوند. نمایش ردیفی که ثبتش شکست
     * می‌خورد، بدتر از نبودنش است.
     *
     * @return Collection<int, array{offer: Offer, quantity: int, total: int}>
     */
    public static function lines(): Collection
    {
        $quantities = static::raw();

        if ($quantities === []) {
            return collect();
        }

        return Offer::query()
            ->sellable()
            ->with(['vendor', 'product'])
            ->whereIn('id', array_keys($quantities))
            ->get()
            ->map(fn (Offer $offer) => [
                'offer' => $offer,
                'quantity' => $quantity = (int) $quantities[$offer->id],
                'total' => $offer->price * $quantity,
            ])
            ->values();
    }

    public static function total(): int
    {
        return (int) static::lines()->sum('total');
    }

    public static function count(): int
    {
        return (int) collect(static::raw())->sum();
    }

    /**
     * افزودن یا تغییر تعداد.
     *
     * تعداد به حداقل سفارشِ همان عرضه و به موجودی‌اش محدود می‌شود — نه به
     * عددی که کاربر فرستاده. اعتبارسنجیِ فرم جای خودش، ولی حقیقتِ موجودی
     * فقط اینجاست.
     */
    public static function put(Offer $offer, int $quantity): void
    {
        $quantity = max($offer->min_order, $quantity);
        $quantity = min($quantity, (int) config('shop.max_quantity', 9999));

        if ($offer->stock !== null) {
            $quantity = min($quantity, $offer->stock);
        }

        if ($quantity < 1) {
            static::forget($offer->id);

            return;
        }

        session()->put(static::KEY.'.'.$offer->id, $quantity);
    }

    public static function forget(int $offerId): void
    {
        session()->forget(static::KEY.'.'.$offerId);
    }

    public static function clear(): void
    {
        session()->forget(static::KEY);
    }
}
