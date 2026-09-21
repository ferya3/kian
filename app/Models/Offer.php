<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * عرضه‌ی یک محصول توسط یک فروشنده.
 *
 * قیمت اینجاست و نه روی محصول، چون قیمت خاصیتِ رابطه است نه خاصیتِ کالا:
 * همان بلوک، نزد دو فروشنده دو قیمت دارد.
 */
class Offer extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'min_order' => 'integer',
            'stock' => 'integer',
            'lead_time_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * عرضه‌هایی که واقعاً قابل خریدند.
     *
     * سه شرط و نه یکی: خودِ عرضه فعال باشد، فروشنده‌اش فعال باشد، و محصولش
     * هم فعال باشد. غیرفعال‌کردن یک فروشنده باید همه‌ی عرضه‌هایش را از
     * ویترین بردارد، بی‌آنکه کسی تک‌تک ردیف‌ها را خاموش کند.
     */
    public function scopeSellable(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereHas('vendor', fn (Builder $q) => $q->where('is_active', true))
            ->whereHas('product', fn (Builder $q) => $q->where('is_active', true));
    }

    /** null یعنی موجودی اعلام نشده — نه اینکه ناموجود است. */
    public function inStock(int $quantity = 1): bool
    {
        return $this->stock === null || $this->stock >= $quantity;
    }
}
