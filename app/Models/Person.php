<?php

namespace App\Models;

use App\Support\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * بنیان‌گذار یا مدیر — در صفحه‌ی بیوگرافی.
 *
 * نام هم ترجمه‌پذیر است: «کیان» در انگلیسی Kian نوشته می‌شود و نه با حروفِ
 * فارسی وسطِ متنِ لاتین.
 */
class Person extends Model
{
    use HasTranslations;

    protected $table = 'people';

    public array $translatable = ['name', 'role', 'bio'];

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('position')->orderBy('id');
    }

    /** حرفِ نخستِ نام، برای وقتی که عکسی نیست. */
    public function initial(): string
    {
        return mb_substr(trim((string) $this->name), 0, 1) ?: '•';
    }

    /** @return array<int, string> هر پاراگراف یک خط */
    public function paragraphs(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R{2,}|\R/u', (string) $this->bio))));
    }
}
