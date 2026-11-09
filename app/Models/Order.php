<?php

namespace App\Models;

use App\Support\Shop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    /*
    | فقط آنچه مشتری در فرمِ تسویه می‌نویسد. شماره، توکن، مبلغ و وضعیت را
    | سرور می‌سازد (CheckoutController) و از ورودی نوشتنی نیستند.
    */
    protected $fillable = [
        'customer_name', 'customer_phone', 'customer_email',
        'province', 'city', 'address', 'note',
    ];

    protected function casts(): array
    {
        return ['total' => 'integer'];
    }

    /** مشتری سفارشش را با token دنبال می‌کند، نه با id. */
    public function getRouteKeyName(): string
    {
        return 'token';
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * شماره‌ی خوانا برای سفارش.
     *
     * روی تاریخ سوار است تا در گفت‌وگوی تلفنی معنا داشته باشد، و دنباله‌ی
     * تصادفی دارد تا از رویش نشود حدس زد امروز چند سفارش ثبت شده — عددی که
     * هیچ فروشگاهی دوست ندارد رقیبش بداند.
     */
    public static function nextNumber(): string
    {
        return now()->format('ymd').'-'.Str::upper(Str::random(4));
    }

    public function statusLabel(): string
    {
        return Shop::status($this->status);
    }

    /** ردیف‌ها به تفکیک فروشنده — هر فروشنده بخش خودش را جدا پیگیری می‌کند. */
    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function byVendor()
    {
        return $this->items->groupBy('vendor_name');
    }
}
