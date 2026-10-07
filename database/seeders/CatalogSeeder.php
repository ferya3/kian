<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Solution;
use App\Support\Jalali;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        /*
        | کارخانه پنج سایز تولید می‌کند: ۸، ۱۳، ۲۰، ۲۵ و ۴۰.
        |
        | پیش‌تر اینجا ده محصول در هفت دسته بود — عایق، سبک، حرارتی،
        | متعلقات — که هیچ‌کدام تولید نمی‌شوند. دسته‌ای که محصول ندارد در
        | منو می‌آید و به صفحه‌ی خالی می‌رسد، پس فقط سه دسته‌ای می‌ماند که
        | واقعاً محصول دارند.
        */
        $blocks = ProductCategory::create([
            'slug' => 'ceramic-blocks',
            'name' => 'بلوک‌های سفالی',
            'name_en' => 'Ceramic Blocks',
            'tagline' => 'ستون فقرات دیوارچینی مدرن',
            'description' => 'خانواده‌ی محصولات کارخانه: بلوک‌های تیغه‌ای، دیواری و سقفی که با اکستروژن خاک رس و پخت در کوره تونلی تولید می‌شوند.',
            'position' => 1,
            'is_featured' => true,
        ]);

        $c = fn (array $data) => ProductCategory::create($data);

        $partition = $c(['parent_id' => $blocks->id, 'slug' => 'partition-blocks', 'name' => 'بلوک تیغه‌ای', 'name_en' => 'Partition Blocks', 'tagline' => 'جداکننده سبک داخلی — ۸ و ۱۳', 'position' => 1]);
        $wall = $c(['parent_id' => $blocks->id, 'slug' => 'wall-blocks', 'name' => 'بلوک دیواری', 'name_en' => 'Wall Blocks', 'tagline' => 'دیوار باربر و میان‌قاب — ۲۰ و ۲۵', 'position' => 2]);
        $roof = $c(['parent_id' => $blocks->id, 'slug' => 'roof-systems', 'name' => 'سیستم‌های سقفی', 'name_en' => 'Roof Systems', 'tagline' => 'تیرچه‌بلوک سفالی — ۴۰', 'position' => 3]);

        /*
        | ⚠ عددهای فنی تخمینی‌اند و نه اندازه‌گیری‌شده.
        |
        | از روی ابعاد هر بلوک و نسبت‌های خانواده‌ی محصول حساب شده‌اند تا
        | صفحه‌ی محصول خالی نماند، ولی هیچ‌کدام از آزمایشگاه نیامده. پیش از
        | اینکه سایت به مشتری یا ناظر نشان داده شود، باید در «پنل ←
        | محصولات» با عددهای واقعی جایگزین شوند.
        */
        $products = [
            [
                'product_category_id' => $partition->id,
                'slug' => 'partition-block-8', 'sku' => 'KP-08', 'name' => 'بلوک تیغه‌ای ۸', 'name_en' => 'Partition Block 8',
                'subtitle' => 'سبک‌ترین جداکننده داخلی',
                'summary' => 'بلوک تیغه‌ای هشت سانتی‌متری برای پارتیشن‌بندی داخلی، سرویس‌ها و فضاهایی که کمترین اشغال ضخامت و کمترین بار مرده اهمیت دارد.',
                'length_mm' => 200, 'width_mm' => 80, 'height_mm' => 200, 'thickness_mm' => 80, 'void_ratio' => 52,
                'weight_kg' => 3.60, 'compressive_strength_mpa' => 3.50, 'thermal_conductivity' => 0.355,
                'thermal_resistance' => 0.225, 'water_absorption' => 15.50, 'fire_resistance_min' => 90,
                'sound_reduction_db' => 37, 'units_per_pallet' => 230, 'units_per_sqm' => 25, 'mortar_per_sqm' => 13.0,
                'thermal_score' => 40, 'acoustic_score' => 54, 'strength_score' => 42, 'sustainability_score' => 88,
                'project_types' => ['residential', 'commercial', 'mass'],
                'wall_types' => ['partition', 'interior'],
                'insulation_level' => 'low', 'is_loadbearing' => false,
                'features' => ['کمترین وزن در خانواده محصولات', 'برش و شیارزنی آسان برای تأسیسات', 'سطح شیاردار برای چسبندگی بهتر گچ'],
                'applications' => ['پارتیشن اتاق‌ها', 'دیوار سرویس بهداشتی', 'کمد و فضای دفن تأسیسات'],
                'standards' => ['استاندارد ملی ایران ۷', 'مبحث ۸ مقررات ملی ساختمان'],
                'position' => 1, 'is_featured' => false,
            ],
            [
                'product_category_id' => $partition->id,
                'slug' => 'partition-block-13', 'sku' => 'KP-13', 'name' => 'بلوک تیغه‌ای ۱۳', 'name_en' => 'Partition Block 13',
                'subtitle' => 'پرکاربردترین بلوک داخلی',
                'summary' => 'سیزده سانتی‌متر ضخامت، تعادل میان وزن، صداگیری و سرعت اجرا؛ انتخاب متداول برای دیوارهای داخلی و جداکننده‌ی واحدها.',
                'length_mm' => 250, 'width_mm' => 130, 'height_mm' => 200, 'thickness_mm' => 130, 'void_ratio' => 55,
                'weight_kg' => 6.00, 'compressive_strength_mpa' => 4.50, 'thermal_conductivity' => 0.310,
                'thermal_resistance' => 0.419, 'water_absorption' => 14.80, 'fire_resistance_min' => 135,
                'sound_reduction_db' => 42, 'units_per_pallet' => 150, 'units_per_sqm' => 20, 'mortar_per_sqm' => 16.0,
                'thermal_score' => 50, 'acoustic_score' => 66, 'strength_score' => 52, 'sustainability_score' => 87,
                'project_types' => ['residential', 'commercial', 'mass', 'industrial'],
                'wall_types' => ['partition', 'interior', 'infill'],
                'insulation_level' => 'low', 'is_loadbearing' => false,
                'features' => ['سرعت اجرای بالا با ابعاد ماژولار', 'کاهش صوت ۴۲ دسی‌بل', 'سازگار با گچ و خاک و گچ‌کاری ماشینی'],
                'applications' => ['دیوار داخلی واحدهای مسکونی', 'دیوار راه‌پله', 'جداکننده فضاهای اداری'],
                'standards' => ['استاندارد ملی ایران ۷', 'مبحث ۸ مقررات ملی ساختمان'],
                'position' => 2, 'is_featured' => true,
            ],
            [
                'product_category_id' => $wall->id,
                'slug' => 'ceramic-block-20', 'sku' => 'KW-20', 'name' => 'بلوک سفالی ۲۰', 'name_en' => 'Ceramic Block 20',
                'subtitle' => 'استاندارد صنعت ساختمان ایران',
                'summary' => 'بلوک بیست سانتی‌متری با ابعاد ۲۰×۲۰×۴۰، متداول‌ترین انتخاب برای دیوار میان‌قاب و دیوار خارجی ساختمان‌های اسکلت‌دار.',
                'length_mm' => 400, 'width_mm' => 200, 'height_mm' => 200, 'thickness_mm' => 200, 'void_ratio' => 58,
                'weight_kg' => 11.50, 'compressive_strength_mpa' => 6.50, 'thermal_conductivity' => 0.260,
                'thermal_resistance' => 0.769, 'water_absorption' => 13.80, 'fire_resistance_min' => 180,
                'sound_reduction_db' => 48, 'units_per_pallet' => 96, 'units_per_sqm' => 12.5, 'mortar_per_sqm' => 21.0,
                'thermal_score' => 68, 'acoustic_score' => 80, 'strength_score' => 72, 'sustainability_score' => 85,
                'project_types' => ['residential', 'commercial', 'industrial', 'mass'],
                'wall_types' => ['exterior', 'interior', 'infill'],
                'insulation_level' => 'medium', 'is_loadbearing' => true,
                'features' => ['یک بلوک معادل ۸ آجر — سرعت اجرای چند برابر', 'مقاومت فشاری ۶٫۵ مگاپاسکال', 'مقاومت آتش سه ساعته'],
                'applications' => ['دیوار خارجی ساختمان مسکونی', 'دیوار میان‌قاب اسکلت بتنی و فولادی', 'دیوار محوطه صنعتی'],
                'standards' => ['استاندارد ملی ایران ۷', 'مبحث ۱۹ مقررات ملی ساختمان', 'مبحث ۲۱ — پدافند غیرعامل'],
                'meta_title' => 'بلوک سفالی ۲۰ — ابعاد ۲۰×۲۰×۴۰، مقاومت ۶٫۵ مگاپاسکال',
                'meta_description' => 'مشخصات فنی کامل بلوک سفالی ۲۰: ابعاد ۲۰×۲۰×۴۰ سانتی‌متر، وزن ۱۱٫۵ کیلوگرم، ضریب هدایت حرارتی ۰٫۲۶ و مقاومت فشاری ۶٫۵ مگاپاسکال. دانلود دیتاشیت، فایل DWG و آبجکت Revit.',
                'position' => 3, 'is_featured' => true,
            ],
            [
                'product_category_id' => $wall->id,
                'slug' => 'ceramic-block-25', 'sku' => 'KW-25', 'name' => 'بلوک سفالی ۲۵', 'name_en' => 'Ceramic Block 25',
                'subtitle' => 'پوسته حرارتی بدون عایق افزوده',
                'summary' => 'بیست‌وپنج سانتی‌متر با آرایش حفره‌های چندردیفه؛ در بسیاری از اقلیم‌ها بدون نیاز به لایه عایق مجزا الزام مبحث ۱۹ را تأمین می‌کند.',
                'length_mm' => 400, 'width_mm' => 250, 'height_mm' => 250, 'thickness_mm' => 250, 'void_ratio' => 62,
                'weight_kg' => 14.80, 'compressive_strength_mpa' => 7.50, 'thermal_conductivity' => 0.210,
                'thermal_resistance' => 1.190, 'water_absorption' => 13.00, 'fire_resistance_min' => 240,
                'sound_reduction_db' => 52, 'units_per_pallet' => 60, 'units_per_sqm' => 10, 'mortar_per_sqm' => 24.0,
                'thermal_score' => 86, 'acoustic_score' => 88, 'strength_score' => 80, 'sustainability_score' => 92,
                'project_types' => ['residential', 'commercial', 'mass'],
                'wall_types' => ['exterior', 'infill'],
                'insulation_level' => 'high', 'is_loadbearing' => true,
                'features' => ['ضریب λ برابر ۰٫۲۱ وات بر متر کلوین', 'حذف پل حرارتی با درز نر و ماده', 'مقاومت آتش چهار ساعته'],
                'applications' => ['دیوار خارجی اقلیم سرد', 'ساختمان‌های با گواهی انرژی', 'پروژه‌های مسکن ملی'],
                'standards' => ['استاندارد ملی ایران ۷', 'مبحث ۱۹ مقررات ملی ساختمان'],
                'position' => 4, 'is_featured' => true,
            ],
            [
                'product_category_id' => $roof->id,
                'slug' => 'roof-block-40', 'sku' => 'KR-40', 'name' => 'بلوک سقفی ۴۰', 'name_en' => 'Roof Block 40',
                'subtitle' => 'سیستم تیرچه‌بلوک سفالی',
                'summary' => 'بلوک سقفی سفالی به طول چهل سانتی‌متر برای سیستم تیرچه‌بلوک؛ سبک‌تر از بلوک بتنی، با عملکرد حرارتی و صوتی بهتر و بتن‌ریزی تمیزتر.',
                'length_mm' => 400, 'width_mm' => 250, 'height_mm' => 200, 'thickness_mm' => 250, 'void_ratio' => 56,
                'weight_kg' => 10.20, 'compressive_strength_mpa' => 5.50, 'thermal_conductivity' => 0.300,
                'thermal_resistance' => 0.667, 'water_absorption' => 14.50, 'fire_resistance_min' => 120,
                'sound_reduction_db' => 42, 'units_per_pallet' => 80, 'units_per_sqm' => 8.5, 'mortar_per_sqm' => 0,
                'thermal_score' => 62, 'acoustic_score' => 64, 'strength_score' => 60, 'sustainability_score' => 86,
                'project_types' => ['residential', 'mass', 'commercial'],
                'wall_types' => ['roof'],
                'insulation_level' => 'medium', 'is_loadbearing' => false,
                'features' => ['۳۵ درصد سبک‌تر از بلوک بتنی سقفی', 'کاهش مصرف بتن پرکننده', 'سطح زیرین صاف برای گچ‌کاری'],
                'applications' => ['سقف تیرچه‌بلوک مسکونی', 'سقف پارکینگ و انبار', 'بازسازی سقف‌های قدیمی'],
                'standards' => ['نشریه ۵۴۳ سازمان برنامه و بودجه', 'مبحث ۹ مقررات ملی ساختمان'],
                'position' => 5, 'is_featured' => false,
            ],
        ];

        foreach ($products as $data) {
            $product = Product::create($data);
            $this->seedCavities($product);
        }

        $this->seedSolutions();
    }

    /** نقاط تعاملی روی مقطع بلوک — کاربر روی هر حفره کلیک می‌کند. */
    protected function seedCavities(Product $product): void
    {
        $rows = [
            [
                'label' => 'حفره‌های عمودی چندردیفه',
                'description' => 'هوای ساکن محبوس در حفره‌ها بهترین عایق طبیعی است. هرچه تعداد ردیف‌ها بیشتر باشد، مسیر انتقال حرارت طولانی‌تر و ضریب λ کمتر می‌شود.',
                'x' => 28, 'y' => 30,
                'metric_label' => 'درصد تخلخل', 'metric_value' => Jalali::digits($product->void_ratio).'٪',
            ],
            [
                'label' => 'دیواره‌های داخلی زیگزاگ',
                'description' => 'آرایش غیرخطی تیغه‌های داخلی، جریان حرارت را مجبور به طی مسیر طولانی‌تر می‌کند و هم‌زمان مقاومت فشاری بلوک را بالا نگه می‌دارد.',
                'x' => 55, 'y' => 62,
                'metric_label' => 'مقاومت فشاری', 'metric_value' => Jalali::digits($this->trim($product->compressive_strength_mpa)).' MPa',
            ],
            [
                'label' => 'درز نر و ماده',
                'description' => 'قفل‌شدن بلوک‌ها در امتداد افقی، ملات درز قائم را حذف می‌کند: سرعت اجرا بالاتر، مصرف ملات کمتر و پل حرارتی کمتر.',
                'x' => 91, 'y' => 50,
                'metric_label' => 'ملات مصرفی', 'metric_value' => Jalali::digits($this->trim($product->mortar_per_sqm ?: 0)).' لیتر بر متر مربع',
            ],
            [
                'label' => 'بدنه‌ی سفال پخته',
                'description' => 'خاک رس پخته‌شده در ۹۰۰ درجه، ماده‌ای معدنی و بی‌اثر است: نمی‌سوزد، پوسیده نمی‌شود و ترکیب شیمیایی‌اش در طول عمر ساختمان تغییر نمی‌کند.',
                'x' => 13, 'y' => 74,
                'metric_label' => 'جذب آب', 'metric_value' => Jalali::digits($this->trim($product->water_absorption)).'٪',
            ],
        ];

        foreach ($rows as $i => $row) {
            $product->cavities()->create([...$row, 'position' => $i + 1]);
        }
    }

    /** حذف صفرهای اضافه‌ی اعشار: 6.50 → 6.5 و 21.00 → 21 */
    protected function trim(float|int|string $value): string
    {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }

    protected function seedSolutions(): void
    {
        $map = [
            [
                'slug' => 'exterior-envelope',
                'title' => 'پوسته‌ی حرارتی ساختمان',
                'title_en' => 'Building Envelope',
                'subtitle' => 'دیوار خارجی تک‌لایه بدون عایق افزوده',
                'summary' => 'به‌جای ترکیب دیوار نازک و لایه‌ی عایق، از یک بلوک با λ پایین استفاده کنید: اجرای ساده‌تر، پل حرارتی کمتر و عمر مفید برابر با عمر ساختمان.',
                'benefits' => ['حذف لایه عایق مجزا', 'کاهش پل حرارتی در اتصالات', 'تأمین الزام مبحث ۱۹ در اقلیم سرد', 'کاهش زمان اجرا تا ۳۰ درصد'],
                'products' => ['ceramic-block-25', 'ceramic-block-20'],
                'position' => 1,
            ],
            [
                'slug' => 'acoustic-separation',
                'title' => 'جداسازی صوتی بین واحدها',
                'title_en' => 'Acoustic Separation',
                'subtitle' => 'دیوار مشترک با کاهش صوت بالای ۵۰ دسی‌بل',
                'summary' => 'جرم حجمی سفال به‌همراه حفره‌های هوا، ترکیبی است که هم صدای هوابرد را کاهش می‌دهد و هم بدون افزودن وزن زیاد به سازه اجرا می‌شود.',
                'benefits' => ['کاهش صوت تا ۵۵ دسی‌بل', 'تأمین الزام مبحث ۱۸', 'بدون نیاز به لایه صوتی افزوده'],
                'products' => ['ceramic-block-25', 'ceramic-block-20', 'partition-block-13'],
                'position' => 2,
            ],
            [
                'slug' => 'mass-housing',
                'title' => 'انبوه‌سازی و مسکن ملی',
                'title_en' => 'Mass Housing',
                'subtitle' => 'تحویل زمان‌بندی‌شده در مقیاس صدها هزار متر مربع',
                'summary' => 'برای پروژه‌های بزرگ، مسئله فقط قیمت بلوک نیست؛ مسئله تضمین تأمین، یکنواختی کیفیت میان بچ‌های تولید و برنامه‌ی تحویل هماهنگ با پیشرفت کار است.',
                'benefits' => ['قرارداد تأمین با برنامه تحویل مرحله‌ای', 'یکنواختی ابعاد میان بچ‌های تولید', 'پشتیبانی فنی در کارگاه', 'بارگیری پالت‌شده و شرینک‌پیچ'],
                'products' => ['ceramic-block-20', 'partition-block-13', 'roof-block-40'],
                'position' => 3,
            ],
            [
                'slug' => 'seismic-retrofit',
                'title' => 'مقاوم‌سازی و اضافه طبقه',
                'title_en' => 'Retrofit & Vertical Extension',
                'subtitle' => 'کاهش بار مرده، کاهش نیروی زلزله',
                'summary' => 'نیروی زلزله متناسب با جرم سازه است. با جایگزینی دیوارهای سنگین، بار مرده و به‌تبع آن برش پایه کاهش می‌یابد — اغلب ارزان‌تر از تقویت اسکلت.',
                'benefits' => ['کاهش بار مرده تا ۲۸ درصد', 'کاهش برش پایه ناشی از زلزله', 'اجرای خشک‌تر و سریع‌تر در ساختمان در حال بهره‌برداری'],
                'products' => ['partition-block-8', 'partition-block-13', 'ceramic-block-20'],
                'position' => 4,
            ],
        ];

        foreach ($map as $data) {
            $slugs = $data['products'];
            unset($data['products']);

            $solution = Solution::create($data);
            $solution->products()->sync(Product::whereIn('slug', $slugs)->pluck('id'));
        }
    }
}
