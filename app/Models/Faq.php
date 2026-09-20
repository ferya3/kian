<?php

namespace App\Models;

use App\Support\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasTranslations;

    /** فیلدهایی که در پنل برای هر زبان جداگانه پر می‌شوند. */
    public array $translatable = [
        'question',
        'answer',
    ];

    protected $guarded = [];
}
