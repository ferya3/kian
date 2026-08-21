<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['value' => 'float'];
    }

    public function scopeGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group)->orderBy('position');
    }
}
