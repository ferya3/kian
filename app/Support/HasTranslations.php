<?php

namespace App\Support;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * محتوای چندزبانه، بدون دست‌زدن به ویوها.
 *
 * مدل فهرستی از فیلدهای قابل ترجمه اعلام می‌کند؛ از آن پس خواندن همان
 * فیلد — $product->name — در زبان غیرپیش‌فرض خودش ترجمه را برمی‌گرداند و
 * اگر ترجمه‌ای نبود، به متنِ زبان پیش‌فرض برمی‌گردد.
 *
 * نوشتن دست‌نخورده می‌ماند: $product->name = '...' همچنان ستونِ خود جدول را
 * می‌نویسد، یعنی زبان پیش‌فرض. ترجمه‌ها از راه putTranslations وارد می‌شوند.
 *
 * برگشت به زبان پیش‌فرض عمدی است و نه ضعف: سایتِ نیمه‌ترجمه باید کار کند،
 * نه اینکه جای متن خالی بماند.
 */
trait HasTranslations
{
    /**
     * حافظه‌ی نقشه‌ی ترجمه‌ها.
     *
     * صریح اعلام شده و نه پویا: Model متد __get دارد، و خاصیتِ اعلام‌نشده
     * به‌جای خودش سر از getAttribute درمی‌آورد و کش هیچ‌وقت کار نمی‌کند.
     */
    protected ?array $translationCache = null;

    /**
     * فقط وقتی زبان جاری پیش‌فرض نیست، ترجمه‌ها هم بار می‌شوند.
     *
     * در فارسی هیچ کوئری اضافه‌ای اجرا نمی‌شود؛ در بقیه‌ی زبان‌ها یک کوئری
     * برای کل مجموعه، نه یکی به‌ازای هر رکورد.
     */
    public static function bootHasTranslations(): void
    {
        static::addGlobalScope('translations', function (Builder $query) {
            if (! Locales::isDefault()) {
                $query->with('translations');
            }
        });
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /** @return array<int, string> */
    public function translatableFields(): array
    {
        return property_exists($this, 'translatable') ? $this->translatable : [];
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (! is_string($key) || ! in_array($key, $this->translatableFields(), true)) {
            return $value;
        }

        if (Locales::isDefault()) {
            return $value;
        }

        $translated = $this->translation($key);

        return $translated === null || $translated === '' ? $value : $translated;
    }

    /** ترجمه‌ی خام یک فیلد؛ بدون برگشت به زبان پیش‌فرض. برای پنل. */
    public function translation(string $field, ?string $locale = null): ?string
    {
        $locale ??= Locales::current();

        return $this->translationMap()[$locale][$field] ?? null;
    }

    /**
     * ذخیره‌ی ترجمه‌های یک زبان.
     *
     * مقدار خالی یعنی «ترجمه‌ای ندارم» و ردیفش پاک می‌شود، تا برگشت به زبان
     * پیش‌فرض دوباره فعال شود — ردیفِ خالی و نبودِ ردیف نباید فرق کنند.
     *
     * @param  array<string, ?string>  $values
     */
    public function putTranslations(string $locale, array $values): void
    {
        foreach ($values as $field => $value) {
            if (! in_array($field, $this->translatableFields(), true)) {
                continue;
            }

            $where = ['locale' => $locale, 'field' => $field];

            if (blank($value)) {
                $this->translations()->where($where)->delete();

                continue;
            }

            $this->translations()->updateOrCreate($where, ['value' => $value]);
        }

        $this->translationCache = null;
        $this->unsetRelation('translations');

        $this->afterTranslationsSaved();
    }

    /**
     * قلابی برای مدلی که بیرون از خودش هم چیزی برای به‌روز کردن دارد.
     *
     * Setting نقشه‌اش را برای هر زبان کش می‌کند و آن کش با نوشتنِ ترجمه کهنه
     * می‌شود. رویدادهای خودِ Translation کافی نبودند: پاک‌کردنِ ترجمه‌ی خالی
     * از راه Query Builder انجام می‌شود و رویدادِ مدل ندارد.
     */
    protected function afterTranslationsSaved(): void
    {
        //
    }

    /** @return array<string, array<string, string>> locale => field => value */
    protected function translationMap(): array
    {
        if ($this->translationCache !== null) {
            return $this->translationCache;
        }

        $rows = $this->relationLoaded('translations')
            ? $this->getRelation('translations')
            : $this->translations()->get();

        $map = [];

        foreach ($rows as $row) {
            $map[$row->locale][$row->field] = $row->value;
        }

        return $this->translationCache = $map;
    }
}
