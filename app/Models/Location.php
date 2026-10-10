<?php

namespace App\Models;

use App\Support\Contact;
use App\Support\HasTranslations;
use App\Support\Jalali;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * یک نشانی: کارخانه، دفتر فروش، انبار…
 *
 * نخستین نشانیِ فعال به ترتیب، نشانیِ اصلی است. نوار بالای سایت ساعت کاری‌اش
 * را نشان می‌دهد و Schema.org همان را به‌عنوان نشانیِ کسب‌وکار به گوگل
 * می‌دهد. بقیه در فوتر و صفحه‌ی تماس فهرست می‌شوند.
 */
class Location extends Model
{
    use HasTranslations;

    /** نام و نشانی و ساعت متن‌اند؛ تلفن و کد پستی و مختصات نه. */
    public array $translatable = ['title', 'address', 'working_hours'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'lat' => 'float',
            'lng' => 'float',
        ];
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('position')->orderBy('id');
    }

    /**
     * نشانی‌های فعال، یک‌بار برای هر درخواست.
     *
     * هدر و فوتر و صفحه‌ی تماس و Schema هر کدام می‌خواهندشان؛ بی این، هر
     * صفحه چهار بار همان کوئری را می‌زد.
     *
     * @return Collection<int, self>
     */
    public static function listed(): Collection
    {
        return Cache::driver('array')->rememberForever('locations.visible', fn () => static::query()->visible()->get());
    }

    public static function primary(): ?self
    {
        return static::listed()->first();
    }

    // ---------------------------------------------------- نمایش ----

    public function phoneLabel(): ?string
    {
        return filled($this->phone) ? Jalali::digits($this->phone) : null;
    }

    public function phoneTel(): ?string
    {
        return filled($this->phone) ? Contact::tel($this->phone) : null;
    }

    public function postalCodeLabel(): ?string
    {
        return filled($this->postal_code) ? Jalali::digits($this->postal_code) : null;
    }

    public function hasPoint(): bool
    {
        return $this->lat !== null && $this->lng !== null;
    }

    /**
     * پیوندِ مسیریابی.
     *
     * با مختصات، همان نقطه؛ بی مختصات، جستجوی خودِ نشانی — که از هیچ بهتر
     * است ولی گاهی به جای دیگری می‌رسد. پس مختصات پیشنهاد می‌شود و نه اجبار.
     */
    public function mapUrl(): string
    {
        $query = $this->hasPoint()
            ? $this->lat.','.$this->lng
            : $this->getRawOriginal('address');

        return 'https://www.google.com/maps/search/?api=1&query='.rawurlencode((string) $query);
    }

    protected static function booted(): void
    {
        $forget = fn () => Cache::driver('array')->forget('locations.visible');

        static::saved($forget);
        static::deleted($forget);
    }

    protected function afterTranslationsSaved(): void
    {
        Cache::driver('array')->forget('locations.visible');
    }
}
