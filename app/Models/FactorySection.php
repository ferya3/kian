<?php

namespace App\Models;

use App\Support\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class FactorySection extends Model
{
    use HasTranslations;

    /** فیلدهایی که در پنل برای هر زبان جداگانه پر می‌شوند. */
    public array $translatable = [
        'title',
        'description',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return ['stats' => 'array', 'hotspot_x' => 'float', 'hotspot_y' => 'float'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
