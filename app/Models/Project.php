<?php

namespace App\Models;

use App\Support\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasTranslations;

    /** فیلدهایی که در پنل برای هر زبان جداگانه پر می‌شوند. */
    public array $translatable = [
        'title',
        'summary',
        'description',
        'client',
        'architect',
        'city',
        'province',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return ['gallery' => 'array', 'is_featured' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}
