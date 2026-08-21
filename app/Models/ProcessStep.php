<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessStep extends Model
{
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
