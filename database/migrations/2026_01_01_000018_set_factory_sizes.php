<?php

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Redirect;
use Database\Seeders\CatalogSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
| هفت سایزِ واقعیِ کارخانه، و فقط همان‌ها.
|
|   تیغه‌ای:  ۴۰×۱۸٫۵×۸   ۲۰×۱۸٫۵×۸   ۴۰×۱۸٫۵×۱۳   ۲۰×۱۸٫۵×۱۳
|   دیواری:   ۲۰×۲۰×۴۰
|   سقفی:     ۲۰×۲۵×۴۰   ۲۰×۲۵×۵۰
|
| size_label ابعاد را همان‌طور که کارخانه می‌نویسد نگه می‌دارد؛ ترتیبش
| با طول × عرض × ارتفاع یکی نیست، پس از سه ستونِ عددی ساخته نمی‌شود.
|
| روی سرورِ موجود:
|   - محصولی که هست: فقط ابعاد و نام و ترتیب عوض می‌شود. متن‌ها، عکس‌ها و
|     مشخصاتی که مدیر در پنل نوشته دست نمی‌خورند.
|   - محصولی که نیست: کامل ساخته می‌شود.
|   - هر محصولِ فعالِ دیگری پنهان می‌شود و نه حذف — برگشت‌پذیر است — و
|     نشانی‌اش به دسته‌ی خودش ریدایرکت می‌شود تا ۴۰۴ ندهد.
*/
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'size_label')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('size_label', 60)->nullable()->after('thickness_mm');
            });
        }

        $categories = ProductCategory::query()->pluck('id', 'slug');

        // نصبِ تازه: دسته‌ها هنوز نیستند و CatalogSeeder همه‌چیز را می‌سازد
        if (! isset($categories['partition-blocks'], $categories['wall-blocks'], $categories['roof-systems'])) {
            return;
        }

        $definitions = CatalogSeeder::definitions();
        $keep = array_column($definitions, 'slug');

        DB::transaction(function () use ($definitions, $keep, $categories) {
            foreach ($definitions as $data) {
                $data['product_category_id'] = $categories[$data['category']];
                unset($data['category']);

                $product = Product::query()->where('slug', $data['slug'])->first();

                if ($product) {
                    $product->forceFill(array_intersect_key($data, array_flip([
                        'size_label', 'length_mm', 'width_mm', 'height_mm', 'thickness_mm',
                        'name', 'name_en', 'position', 'product_category_id',
                    ])) + ['is_active' => true])->save();

                    continue;
                }

                $product = Product::create($data + ['is_active' => true]);
(new CatalogSeeder)->seedCavities($product);
            }

            /*
            | بلوکِ ۸ از ۲۰ به ۴۰ سانت طول رسید؛ وزن و تعدادش از روی ابعاد تخمین
            | زده شده بود و با آن عوض می‌شود — فقط اگر هنوز همان تخمینِ
            | دست‌نخورده است. عددی که مدیر در پنل گذاشته می‌ماند.
            */
            $eight = collect($definitions)->firstWhere('slug', 'partition-block-8');
            Product::query()->where('slug', 'partition-block-8')
                ->where('weight_kg', 3.60)->where('units_per_pallet', 230)->where('units_per_sqm', 25)
                ->update(array_intersect_key($eight, array_flip(['weight_kg', 'units_per_pallet', 'units_per_sqm'])));

            // آنچه دیگر تولید نمی‌شود: پنهان، با ریدایرکت به دسته‌اش
            $retired = Product::query()->whereNotIn('slug', $keep)->where('is_active', true)->with('category')->get();

            foreach ($retired as $product) {
                $product->update(['is_active' => false]);

                if (! Schema::hasTable('redirects')) {
                    continue;
                }

                foreach (['fa', 'en', 'ar'] as $locale) {
                    $from = "/{$locale}/products/{$product->slug}";
                    $to = "/{$locale}/products".($product->category ? '?category='.$product->category->slug : '');

                    Redirect::query()->firstOrCreate(
                        ['from_path' => $from],
                        ['to_url' => $to, 'status_code' => 301, 'source' => 'manual', 'note' => 'محصولی که دیگر تولید نمی‌شود']
                    );
                }
            }

            ProductCategory::query()->where('slug', 'wall-blocks')->update(['tagline' => 'دیوار باربر و میان‌قاب — ۲۰']);
            ProductCategory::query()->where('slug', 'roof-systems')->update(['tagline' => 'تیرچه‌بلوک سفالی — ۴۰ و ۵۰']);
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'size_label')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('size_label');
            });
        }
    }
};
