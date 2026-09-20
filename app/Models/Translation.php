<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * یک فیلد از یک رکورد، به یک زبان.
 *
 * مستقیم با آن کار نمی‌شود؛ HasTranslations واسطه است.
 */
class Translation extends Model
{
    protected $guarded = [];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
