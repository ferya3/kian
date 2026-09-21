<?php

namespace App\Models;

use App\Support\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasTranslations;

    /** فیلدهایی که در پنل برای هر زبان جداگانه پر می‌شوند. */
    public array $translatable = [
        'title',
        'description',
    ];

    protected $guarded = [];

    /**
     * کلیدهای دسته و مخاطب.
     *
     * خودِ کلیدها در دیتابیس ذخیره می‌شوند و قواعد اعتبارسنجی از رویشان
     * ساخته می‌شود، پس کدند. برچسبشان در lang/<code>/site.php است و با
     * categories() و audiences() خوانده می‌شود.
     */
    public const CATEGORIES = [
        'datasheet', 'catalog', 'cad', 'bim', 'installation', 'certificate', 'standard',
    ];

    public const AUDIENCES = ['customer', 'engineer', 'contractor'];

    /** @return array<string, string> کلید => برچسب، به زبان صفحه */
    public static function categories(): array
    {
        return collect(self::CATEGORIES)
            ->mapWithKeys(fn (string $key) => [$key => __("site.document.category.{$key}")])
            ->all();
    }

    /** @return array<string, string> */
    public static function audiences(): array
    {
        return collect(self::AUDIENCES)
            ->mapWithKeys(fn (string $key) => [$key => __("site.document.audience.{$key}")])
            ->all();
    }

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
        return static::categories()[$this->category] ?? $this->category;
    }

    public function sizeLabel(): string
    {
        if ($this->file_size_kb >= 1024) {
            return number_format($this->file_size_kb / 1024, 1).' MB';
        }

        return $this->file_size_kb.' KB';
    }
}
