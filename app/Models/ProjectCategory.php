<?php

namespace App\Models;

use App\Support\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectCategory extends Model
{
    use HasTranslations;

    /** فیلدهایی که در پنل برای هر زبان جداگانه پر می‌شوند. */
    public array $translatable = [
        'name',
    ];

    protected $guarded = [];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
