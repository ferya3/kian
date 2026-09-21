<?php

namespace App\Models;

use App\Support\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    use HasTranslations;

    /** عدد و پیشوند و پسوند کدند؛ برچسب و شرحش متن‌اند. */
    public array $translatable = ['label', 'description'];

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
