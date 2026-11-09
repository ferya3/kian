<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    /** فقط فیلدهای فرمِ تماس (اعتبارسنجی‌شده در ContactRequest) و IP. وضعیت و یادداشت از پنل. */
    protected $fillable = ['type', 'name', 'company', 'email', 'phone', 'city', 'subject', 'product_id', 'message', 'ip'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
