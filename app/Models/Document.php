<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $guarded = [];

    public const CATEGORIES = [
        'datasheet' => 'دیتاشیت محصول',
        'catalog' => 'کاتالوگ فنی',
        'cad' => 'فایل CAD',
        'bim' => 'آبجکت BIM',
        'installation' => 'راهنمای اجرا',
        'certificate' => 'گواهی‌نامه',
        'standard' => 'استاندارد و ضوابط',
    ];

    public const AUDIENCES = [
        'customer' => 'کارفرما و مشتری',
        'engineer' => 'مهندس و معمار',
        'contractor' => 'پیمانکار و مجری',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function sizeLabel(): string
    {
        if ($this->file_size_kb >= 1024) {
            return number_format($this->file_size_kb / 1024, 1).' MB';
        }

        return $this->file_size_kb.' KB';
    }
}
