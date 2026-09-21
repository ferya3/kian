<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Distributor extends Model
{
    protected $guarded = [];

    /**
     * فروشگاهِ همین نمایندگی — اگر داشته باشد.
     *
     * نمایندگی و فروشنده یکی نیستند: هر نمایندگی لزوماً آنلاین نمی‌فروشد و
     * هر فروشنده هم لزوماً نمایندگی نیست. پس رابطه اختیاری است.
     */
    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }
}
