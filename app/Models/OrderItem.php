<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'unit_price' => 'integer',
            'quantity' => 'integer',
            'total' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /*
    | اطلاعات تماسِ مشتری، از دلِ سفارش.
    |
    | فروشنده باید بتواند زنگ بزند — کلِ چرخه‌ی این فروشگاه همین تماس است.
    | ولی نباید کلِ سفارش را ببیند: سفارش ممکن است ردیفِ فروشنده‌ی دیگری هم
    | داشته باشد و مبلغ کلش به او مربوط نیست. پس فقط همین چند قلم بیرون
    | می‌آید و نه خودِ رابطه.
    */
    public function getOrderNumberAttribute(): ?string
    {
        return $this->order?->number;
    }

    public function getCustomerNameAttribute(): ?string
    {
        return $this->order?->customer_name;
    }

    public function getCustomerPhoneAttribute(): ?string
    {
        return $this->order?->customer_phone;
    }

    public function getCustomerAddressAttribute(): ?string
    {
        return trim(implode('، ', array_filter([
            $this->order?->province,
            $this->order?->city,
            $this->order?->address,
        ])));
    }

    public function statusLabel(): string
    {
        return config('shop.statuses')[$this->status] ?? $this->status;
    }
}
