<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * تغییر نام برند در محتوای ذخیره‌شده.
 *
 * نام برند از «سفال کیان» به «کیان بهساز» رفت. عوض‌کردن config و seeder
 * برای نصبِ تازه کافی است، ولی نه برای سایتی که از قبل بالا آمده: آن‌جا
 * متن‌ها در دیتابیس‌اند و seeder دوباره اجرا نمی‌شود. مثالِ عینی‌اش
 * hero_eyebrow است که روی صفحه‌ی اصلی، درشت، نام قدیم را نشان می‌داد.
 *
 * جایگزینیِ رشته است و نه بازنویسیِ مقدار: هر متنی که نام قدیم را ندارد
 * اصلاً لمس نمی‌شود، و متنی که دارد فقط همان چند حرفش عوض می‌شود. پس
 * ویرایش‌هایی که مدیر سایت در پنل انجام داده از بین نمی‌رود.
 *
 * بازگشت‌پذیر است، ولی برگرداندنش هم جایگزینیِ ساده است: اگر جایی از قبل
 * «کیان بهساز» نوشته شده بود، down آن را هم به نام قدیم برمی‌گرداند.
 */
return new class extends Migration
{
    /**
     * ستون‌هایی که متنِ رو به کاربر دارند.
     *
     * ستون‌های فنی — slug، کلید، مسیر فایل — عمداً بیرون‌اند: نام برند در
     * slug تغییر نشانی است و نه اصلاحِ متن، و پیوندهای قدیمی را می‌شکند.
     *
     * @var array<string, list<string>>
     */
    protected array $columns = [
        'settings' => ['value'],
        'translations' => ['value'],
        'articles' => ['title', 'excerpt', 'body'],
        'faqs' => ['question', 'answer'],
        'projects' => ['title', 'summary', 'description', 'client'],
        'products' => ['name', 'description'],
        'solutions' => ['title', 'summary', 'description'],
        'process_steps' => ['title', 'description'],
        'factory_sections' => ['title', 'description'],
        'certificates' => ['title', 'issuer'],
        'documents' => ['title', 'description'],
    ];

    public function up(): void
    {
        $this->swap('سفال کیان', 'کیان بهساز');
    }

    public function down(): void
    {
        $this->swap('کیان بهساز', 'سفال کیان');
    }

    protected function swap(string $from, string $to): void
    {
        foreach ($this->columns as $table => $fields) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($fields as $field) {
                if (! Schema::hasColumn($table, $field)) {
                    continue;
                }

                DB::table($table)
                    ->where($field, 'like', '%'.$from.'%')
                    ->update([$field => DB::raw(
                        // REPLACE در sqlite، mysql و postgres یکسان است
                        'REPLACE('.$field.', '.DB::getPdo()->quote($from).', '.DB::getPdo()->quote($to).')'
                    )]);
            }
        }
    }
};
