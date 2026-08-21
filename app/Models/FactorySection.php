<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactorySection extends Model
{
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
