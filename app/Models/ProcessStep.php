<?php

namespace App\Models;

use App\Support\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class ProcessStep extends Model
{
    use HasTranslations;

    /** فیلدهایی که در پنل برای هر زبان جداگانه پر می‌شوند. */
    public array $translatable = [
        'title',
        'summary',
        'description',
        'metric_label',
        'metric_value',
        'duration',
    ];

    protected $guarded = [];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function paddedNumber(): string
    {
        return str_pad((string) $this->step_no, 2, '0', STR_PAD_LEFT);
    }
}
