<?php

namespace Database\Seeders;

use App\Models\Distributor;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * فروشنده‌ها و عرضه‌های اولیه‌ی فروشگاه.
 *
 * بی این، روشن‌کردنِ کلیدِ فروشگاه یک ویترینِ خالی می‌دهد: ShopController فقط
 * محصولی را نشان می‌دهد که دست‌کم یک عرضه‌ی قابل‌فروش دارد، و بی فروشنده
 * هیچ عرضه‌ای نیست. یعنی پیوندِ «فروشگاه» در منو می‌آید و صفحه‌اش سفید است.
 *
 * فروشنده‌ها از روی نمایندگی‌های موجود ساخته می‌شوند و نه از هوا: نام و شهر
 * و تلفن همان‌هاست که در سایت هست، پس ویترین با بقیه‌ی سایت یک‌دست می‌ماند.
 * یکی از آن‌ها عمداً به نمایندگی وصل نیست، چون مدل هر دو حالت را می‌پذیرد و
 * حالتی که هیچ‌وقت ساخته نشود، حالتی است که هیچ‌وقت آزموده نمی‌شود.
 *
 * ⚠ قیمت‌ها جانشین‌اند و نه واقعی. پیش از عمومی‌شدنِ فروشگاه باید در
 * «پنل ← فروشگاه ← عرضه‌ها» با قیمتِ واقعی جایگزین شوند.
 *
 * بارها قابل اجراست و ردیفِ موجود را دست نمی‌زند — firstOrCreate و نه
 * updateOrCreate. فرقش روی دیتابیسِ زنده بزرگ است: با updateOrCreate، هر
 * اجرای دوباره قیمتِ واقعی‌ای را که مدیر گذاشته بود با همین عددِ جانشین
 * برمی‌گرداند، بی‌آنکه کسی خبردار شود.
 */
class ShopSeeder extends Seeder
{
    /**
     * قیمتِ جانشین بر حسب تومان، بر پایه‌ی بزرگیِ خودِ بلوک.
     *
     * عددها از حجمِ محصول درمی‌آیند تا دست‌کم نسبتشان با هم بی‌معنا نباشد —
     * بلوکِ بزرگ‌تر گران‌تر. ولی خودِ عددها جانشین‌اند.
     *
     * پایه‌ی ثابت هم هست و نه فقط ضریبِ حجم: هزینه‌ی پخت و جابه‌جایی با
     * حجم صفر نمی‌شود، و بی آن، بلوکِ ۲٫۸ لیتری قیمتی می‌گرفت که در هیچ
     * بازاری شبیهِ قیمت نیست. دامنه‌ی حاصل روی محصولات این سایت
     * ۱۱٬۰۰۰ تا ۵۲٬۰۰۰ تومان است.
     */
    protected function placeholderPrice(Product $product): int
    {
        $litres = ($product->length_mm * $product->width_mm * $product->height_mm) / 1_000_000;

        // به نزدیک‌ترین پانصد تومان گرد می‌شود؛ قیمتِ نامگرد، جعلی به نظر می‌رسد
        return (int) (round((8_000 + max(1, $litres) * 1_100) / 500) * 500);
    }

    public function run(): void
    {
        $products = Product::query()->where('is_active', true)->orderBy('position')->get();

        if ($products->isEmpty()) {
            $this->command?->warn('محصولی نیست؛ عرضه‌ای ساخته نشد.');

            return;
        }

        $distributors = Distributor::query()->orderBy('position')->take(2)->get();

        $rows = $distributors->map(fn (Distributor $d) => [
            'name' => $d->name,
            'distributor_id' => $d->id,
            'phone' => $d->phone ?? $d->mobile,
            'province' => $d->province,
            'city' => $d->city,
            'about' => 'عرضه‌ی مستقیم محصولات کیان بهساز در '.($d->city ?: 'سراسر کشور').'.',
        ])->all();

        // فروشنده‌ی مستقل — همان حالتِ دومی که مدل اجازه می‌دهد
        $rows[] = [
            'name' => 'فروش مستقیم کارخانه',
            'distributor_id' => null,
            'phone' => config('kian.contact.phone_raw'),
            'province' => 'تهران',
            'city' => 'تهران',
            'about' => 'فروش بی‌واسطه از انبار کارخانه، با تحویل درب کارگاه.',
        ];

        foreach ($rows as $position => $row) {
            $vendor = Vendor::firstOrCreate(
                ['slug' => Str::slug($row['name'], '-', null)],
                $row + ['is_active' => true, 'position' => $position * 10],
            );

            foreach ($products as $i => $product) {
                /*
                 * هر فروشنده همه‌ی محصولات را ندارد و این عمدی است: ویترینی
                 * که همه‌ی ردیف‌هایش «۳ فروشنده» بزند، ساختگی به نظر می‌رسد.
                 * شرط قطعی است و نه تصادفی، تا اجرای دوباره همان نتیجه را
                 * بدهد.
                 */
                if (($i + $position) % 4 === 3) {
                    continue;
                }

                $base = $this->placeholderPrice($product);

                Offer::firstOrCreate(
                    ['vendor_id' => $vendor->id, 'product_id' => $product->id],
                    [
                        // اختلافِ قیمتِ فروشنده‌ها، همان چیزی است که مقایسه را معنادار می‌کند
                        'price' => (int) (round($base * (1 + ($position * 3 - 2) / 100) / 500) * 500),
                        'unit' => 'عدد',
                        'min_order' => [100, 200, 50][$position % 3],
                        'stock' => null,
                        'lead_time_days' => [2, 3, 1][$position % 3],
                        'is_active' => true,
                    ],
                );
            }
        }

        $this->command?->info(sprintf(
            'فروشگاه: %d فروشنده، %d عرضه. قیمت‌ها جانشین‌اند — در پنل جایگزین کنید.',
            Vendor::count(),
            Offer::count(),
        ));
    }
}
