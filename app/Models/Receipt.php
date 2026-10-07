<?php

namespace App\Models;

use App\Support\Media;
use App\Support\Shop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * رسیدِ حواله‌ی یک فروشنده از یک سفارش.
 *
 * چرخه‌اش سه حالت دارد و نه بیشتر: فرستاده شده، تأیید شد، رد شد. حالتِ
 * «نفرستاده» ردیف ندارد — نبودِ ردیف خودش همان معنا را می‌دهد و یک حالتِ
 * چهارم که باید همه‌جا بررسی شود، کم می‌کند.
 */
class Receipt extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'reviewed_at' => 'datetime',
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

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * رسیدِ تأییدشده دیگر عوض نمی‌شود.
     *
     * وگرنه مشتری می‌توانست پس از تأیید، عکس را با چیز دیگری جایگزین کند و
     * سندِ فروشنده زیر پایش خالی شود.
     */
    public function isLocked(): bool
    {
        return $this->isConfirmed();
    }

    public function statusLabel(): string
    {
        return Shop::receiptStatus($this->status);
    }

    // ---------------------------------------------- برای فهرستِ پنل ----

    public function getOrderNumberAttribute(): ?string
    {
        return $this->order?->number;
    }

    public function getVendorNameAttribute(): ?string
    {
        return $this->vendor?->name;
    }

    public function getAmountLabelAttribute(): string
    {
        return Shop::price($this->amount);
    }

    public function getImageLinkAttribute(): ?string
    {
        return $this->image ? Media::url($this->image) : null;
    }

    protected static function booted(): void
    {
        /*
        | مهرِ زمانِ داوری خودکار زده می‌شود.
        |
        | در پنل گذاشتنش یعنی یک فیلدِ دیگر که داورْ باید یادش بماند پرش
        | کند — و روزی که یادش نرود هم، تاریخی می‌نویسد که با واقعیت یکی
        | نیست.
        */
        static::saving(function (self $receipt) {
            if ($receipt->isDirty('status')) {
                $receipt->reviewed_at = $receipt->isPending() ? null : now();
            }
        });
    }
}
