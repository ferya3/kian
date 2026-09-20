<?php

namespace App\Models;

use App\Support\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Solution extends Model
{
    use HasTranslations;

    /** فیلدهایی که در پنل برای هر زبان جداگانه پر می‌شوند. */
    public array $translatable = [
        'title',
        'subtitle',
        'summary',
        'description',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return ['benefits' => 'array'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
