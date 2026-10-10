<?php

use App\Models\Location;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * نشانی‌ها — هر کدام با ساعت کاری، تلفن و نقطه‌ی نقشه‌ی خودش.
 *
 * تا اینجا نشانی یک میدان بود در «اطلاعات تماس». کارخانه و دفتر فروش و
 * انبار هر کدام نشانی و ساعت کاری خودشان را دارند، و یک میدان یعنی یا
 * همه را در یک خط چپاندن یا یکی را نگفتن.
 *
 * نخستین ردیف به ترتیب، نشانیِ اصلی است: نوار بالای سایت ساعت کاری‌اش را
 * نشان می‌دهد و گوگل همان را به‌عنوان نشانیِ کسب‌وکار می‌گیرد.
 */
return new class extends Migration
{
    /** کلیدهایی که از «اطلاعات تماس» به اینجا کوچ می‌کنند. */
    protected array $moved = [
        'address', 'address_locality', 'address_region', 'postal_code', 'working_hours', 'lat', 'lng',
    ];

    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->text('address');
            $table->string('working_hours', 200)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('region', 60)->nullable();
            $table->string('city', 60)->nullable();
            $table->string('postal_code', 12)->nullable();
            $table->decimal('lat', 9, 6)->nullable();
            $table->decimal('lng', 9, 6)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        $this->moveCurrentAddressIn();
    }

    /**
     * نشانیِ فعلی، نخستین ردیف می‌شود.
     *
     * از همان جایی خوانده می‌شود که سایت تا امروز می‌خواند: ردیفِ پنل اگر
     * بود، وگرنه پیکربندی. ترجمه‌ها هم همین‌طور — ترجمه‌ی پنل، وگرنه پرونده‌ی
     * زبان — تا صفحه‌ی انگلیسی پس از این مهاجرت ناگهان نشانیِ فارسی نشان
     * ندهد.
     */
    protected function moveCurrentAddressIn(): void
    {
        $stored = DB::table('settings')->where('key', 'like', 'contact.%')->pluck('value', 'key');
        $get = fn (string $key) => filled($stored["contact.{$key}"] ?? null)
            ? trim($stored["contact.{$key}"])
            : config("kian.contact.{$key}");

        $address = $get('address');

        if (blank($address)) {
            return;
        }

        $id = DB::table('locations')->insertGetId([
            'title' => 'کارخانه',
            'address' => $address,
            'working_hours' => $get('working_hours'),
            'region' => $get('address_region'),
            'city' => $get('address_locality'),
            'postal_code' => $get('postal_code'),
            'lat' => is_numeric($get('lat')) ? $get('lat') : null,
            'lng' => is_numeric($get('lng')) ? $get('lng') : null,
            'is_active' => true,
            'position' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (array_keys(config('locales.available', [])) as $code) {
            if ($code === config('locales.default', 'fa')) {
                continue;
            }

            $values = ['title' => trans('site.contact.plant', [], $code)];

            foreach (['address', 'working_hours'] as $field) {
                $line = "site.contact.{$field}";
                $fromFile = trans($line, [], $code);

                $values[$field] = filled($stored["contact.{$field}_{$code}"] ?? null)
                    ? trim($stored["contact.{$field}_{$code}"])
                    : ($fromFile !== $line ? $fromFile : null);
            }

            foreach ($values as $field => $value) {
                // «site.contact.plant» یعنی ترجمه نبود
                if (blank($value) || str_starts_with((string) $value, 'site.')) {
                    continue;
                }

                DB::table('translations')->insert([
                    'translatable_type' => Location::class,
                    'translatable_id' => $id,
                    'locale' => $code,
                    'field' => $field,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // یک منبع: ردیف‌های کهنه‌ی تماس پاک می‌شوند تا کسی دنبالشان نگردد
        //
        // کلیدها صریح‌اند و نه LIKE با «_»: SQLite برای LIKE نویسه‌ی گریز
        // پیش‌فرض ندارد، پس «\_» آنجا یعنی خودِ بک‌اسلش و ردیف‌های ترجمه
        // جا می‌ماندند.
        $keys = [];

        foreach ($this->moved as $key) {
            $keys[] = "contact.{$key}";

            foreach (array_keys(config('locales.available', [])) as $code) {
                $keys[] = "contact.{$key}_{$code}";
            }
        }

        DB::table('settings')->whereIn('key', $keys)->delete();

        foreach (array_keys(config('locales.available', [])) as $code) {
            Cache::forget("settings.map.{$code}");
        }
    }

    public function down(): void
    {
        DB::table('translations')->where('translatable_type', Location::class)->delete();
        Schema::dropIfExists('locations');
    }
};
