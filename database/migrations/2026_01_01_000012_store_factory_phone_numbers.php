<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * شماره‌های واقعیِ کارخانه، به‌عنوان مقدارِ پنل.
 *
 * چرا مهاجرت و نه فقط پیش‌فرضِ config: پیش‌فرضِ config را .env می‌پوشاند،
 * و .env ِ هر سروری که تا امروز نصب شده از روی .env.example ِ قدیم ساخته
 * شده — با KIAN_PHONE="۰۲۱-۹۱۰۰۲۲۳۳". یعنی عوض‌کردنِ پیش‌فرض در کد هیچ‌وقت
 * به سایتِ زنده نمی‌رسید و همان شماره‌ی نمونه روی هدر می‌ماند.
 *
 * ردیفِ پنل هر دو را می‌پوشاند. فقط اگر ردیفی نباشد نوشته می‌شود: مدیری که
 * از پنل شماره‌ی دیگری گذاشته، همان را نگه می‌دارد.
 */
return new class extends Migration
{
    protected array $values = [
        'contact.phone' => '045-3182',
        'contact.sales_phone' => '045-33338748',
        'contact.sales_extension' => '106',
    ];

    public function up(): void
    {
        foreach ($this->values as $key => $value) {
            if (DB::table('settings')->where('key', $key)->exists()) {
                continue;
            }

            DB::table('settings')->insert([
                'key' => $key,
                'group' => 'contact',
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->forgetSettingsCache();
    }

    /**
     * فقط ردیفی برداشته می‌شود که هنوز همان مقدارِ این مهاجرت را دارد.
     * شماره‌ای که مدیر بعداً عوض کرده، مالِ اوست و نه این مهاجرت.
     */
    public function down(): void
    {
        foreach ($this->values as $key => $value) {
            DB::table('settings')->where('key', $key)->where('value', $value)->delete();
        }

        $this->forgetSettingsCache();
    }

    /** نقشه‌ی تنظیمات برای همیشه کش می‌شود؛ بی این، تا پاک‌شدنِ کش شماره‌ی قدیم می‌ماند. */
    protected function forgetSettingsCache(): void
    {
        foreach (array_keys(config('locales.available', [])) as $code) {
            Cache::forget("settings.map.{$code}");
        }
    }
};
