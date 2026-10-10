<?php

namespace App\Models;

use App\Support\HasTranslations;
use App\Support\Jalali;
use App\Support\Locales;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * یک رویداد در خطِ زمانِ بیوگرافی.
 *
 * سال شمسی ذخیره می‌شود، همان‌طور که مدیر می‌نویسد؛ تقویمِ میلادی ۶۲۱ اضافه
 * می‌کند — همان قاعده‌ی Brand::founded، تا «۱۳۸۰» ِ این صفحه و «۱۳۸۰» ِ
 * «درباره ما» در انگلیسی هر دو ۲۰۰۱ بخوانند. سالِ خالی یعنی «امروز».
 */
class Milestone extends Model
{
    use HasTranslations;

    public array $translatable = ['title', 'text'];

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'year' => 'integer'];
    }

    /**
     * به ترتیبِ سال — خطِ زمان است. «امروز» همیشه آخر، و «ترتیب»ِ پنل فقط
     * دو رویدادِ یک سال را از هم جدا می‌کند. مدیر لازم نیست برای جاانداختنِ
     * رویدادی در وسط، بقیه را از نو شماره بزند.
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->orderByRaw('year is null')
            ->orderBy('year')
            ->orderBy('position')
            ->orderBy('id');
    }

    public function yearLabel(): string
    {
        if ($this->year === null) {
            return __('site.biography.today');
        }

        return Jalali::digits(Locales::calendar() === 'jalali' ? $this->year : $this->year + 621);
    }
}
