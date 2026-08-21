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
        $blocks = ProductCategory::create([
            'slug' => 'ceramic-blocks',
            'name' => 'بلوک‌های سفالی',
            'name_en' => 'Ceramic Blocks',
            'tagline' => 'ستون فقرات دیوارچینی مدرن',
            'description' => 'خانواده‌ی اصلی محصولات: بلوک‌های دیواری، تیغه‌ای، عایق و سقفی که با اکستروژن خاک رس و پخت در کوره تونلی تولید می‌شوند.',
            'position' => 1,
            'is_featured' => true,
        ]);

        $special = ProductCategory::create([
            'slug' => 'special-products',
            'name' => 'محصولات ویژه',
            'name_en' => 'Engineered Systems',
            'tagline' => 'راهکارهای مهندسی‌شده برای نیازهای خاص',
            'description' => 'محصولاتی که برای پروژه‌های با الزام حرارتی، وزنی یا اجرایی خاص طراحی شده‌اند.',
            'position' => 2,
            'is_featured' => true,
        ]);

        $c = fn (array $data) => ProductCategory::create($data);

        $wall = $c(['parent_id' => $blocks->id, 'slug' => 'wall-blocks', 'name' => 'بلوک دیواری', 'name_en' => 'Wall Blocks', 'tagline' => 'دیوار باربر و میان‌قاب', 'position' => 1]);
        $partition = $c(['parent_id' => $blocks->id, 'slug' => 'partition-blocks', 'name' => 'بلوک تیغه‌ای', 'name_en' => 'Partition Blocks', 'tagline' => 'جداکننده سبک داخلی', 'position' => 2]);
        $insulating = $c(['parent_id' => $blocks->id, 'slug' => 'insulating-blocks', 'name' => 'بلوک عایق', 'name_en' => 'Insulating Blocks', 'tagline' => 'پوسته حرارتی ساختمان', 'position' => 3]);
        $accessories = $c(['parent_id' => $blocks->id, 'slug' => 'accessories', 'name' => 'متعلقات', 'name_en' => 'Accessories', 'tagline' => 'نیم‌بلوک، گوشه و نعل درگاه', 'position' => 4]);

        $light = $c(['parent_id' => $special->id, 'slug' => 'lightweight-blocks', 'name' => 'بلوک سبک', 'name_en' => 'Lightweight Blocks', 'tagline' => 'کاهش بار مرده سازه', 'position' => 1]);
        $thermal = $c(['parent_id' => $special->id, 'slug' => 'thermal-blocks', 'name' => 'بلوک حرارتی', 'name_en' => 'Thermal Blocks', 'tagline' => 'مطابق مبحث ۱۹ در اقلیم سرد', 'position' => 2]);
        $roof = $c(['parent_id' => $special->id, 'slug' => 'roof-systems', 'name' => 'سیستم‌های سقفی', 'name_en' => 'Roof Systems', 'tagline' => 'تیرچه‌بلوک سفالی', 'position' => 3]);

        $products = [
            [
                'product_category_id' => $partition->id,
                'slug' => 'partition-block-7', 'sku' => 'KP-07', 'name' => 'بلوک تیغه‌ای ۷', 'name_en' => 'Partition Block 7',
                'subtitle' => 'سبک‌ترین جداکننده داخلی',
                'summary' => 'بلوک تیغه‌ای هفت سانتی‌متری برای پارتیشن‌بندی داخلی، سرویس‌ها و فضاهایی که کمترین اشغال ضخامت و کمترین بار مرده اهمیت دارد.',
                'length_mm' => 200, 'width_mm' => 70, 'height_mm' => 200, 'thickness_mm' => 70, 'void_ratio' => 52,
                'weight_kg' => 3.20, 'compressive_strength_mpa' => 3.50, 'thermal_conductivity' => 0.360,
                'thermal_resistance' => 0.194, 'water_absorption' => 15.50, 'fire_resistance_min' => 90,
                'sound_reduction_db' => 36, 'units_per_pallet' => 240, 'units_per_sqm' => 25, 'mortar_per_sqm' => 12.5,
                'thermal_score' => 38, 'acoustic_score' => 52, 'strength_score' => 40, 'sustainability_score' => 88,
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
                'slug' => 'partition-block-10', 'sku' => 'KP-10', 'name' => 'بلوک تیغه‌ای ۱۰', 'name_en' => 'Partition Block 10',
                'subtitle' => 'پرکاربردترین بلوک داخلی',
                'summary' => 'ده سانتی‌متر ضخامت، تعادل دقیق بین وزن، صداگیری و سرعت اجرا؛ انتخاب استاندارد برای دیوارهای داخلی مسکونی.',
                'length_mm' => 250, 'width_mm' => 100, 'height_mm' => 200, 'thickness_mm' => 100, 'void_ratio' => 55,
                'weight_kg' => 4.60, 'compressive_strength_mpa' => 4.00, 'thermal_conductivity' => 0.330,
                'thermal_resistance' => 0.303, 'water_absorption' => 15.00, 'fire_resistance_min' => 120,
                'sound_reduction_db' => 40, 'units_per_pallet' => 180, 'units_per_sqm' => 20, 'mortar_per_sqm' => 14.0,
                'thermal_score' => 45, 'acoustic_score' => 60, 'strength_score' => 46, 'sustainability_score' => 88,
                'project_types' => ['residential', 'commercial', 'mass', 'industrial'],
                'wall_types' => ['partition', 'interior'],
                'insulation_level' => 'low', 'is_loadbearing' => false,
                'features' => ['سرعت اجرای بالا با ابعاد ماژولار', 'کاهش صوت ۴۰ دسی‌بل', 'سازگار با گچ و خاک و گچ‌کاری ماشینی'],
                'applications' => ['دیوار داخلی واحدهای مسکونی', 'دیوار راه‌پله', 'جداکننده فضاهای اداری'],
                'standards' => ['استاندارد ملی ایران ۷', 'مبحث ۸ مقررات ملی ساختمان'],
                'position' => 2, 'is_featured' => true,
            ],
            [
                'product_category_id' => $wall->id,
                'slug' => 'ceramic-block-15', 'sku' => 'KW-15', 'name' => 'بلوک سفالی ۱۵', 'name_en' => 'Ceramic Block 15',
                'subtitle' => 'دیوار جداکننده بین واحدها',
                'summary' => 'پانزده سانتی‌متر ضخامت با عملکرد صوتی مناسب برای دیوار مشترک بین واحدها و دیوارهای میان‌قاب سبک.',
                'length_mm' => 250, 'width_mm' => 150, 'height_mm' => 200, 'thickness_mm' => 150, 'void_ratio' => 54,
                'weight_kg' => 7.80, 'compressive_strength_mpa' => 5.00, 'thermal_conductivity' => 0.290,
                'thermal_resistance' => 0.517, 'water_absorption' => 14.20, 'fire_resistance_min' => 150,
                'sound_reduction_db' => 45, 'units_per_pallet' => 120, 'units_per_sqm' => 20, 'mortar_per_sqm' => 18.0,
                'thermal_score' => 55, 'acoustic_score' => 72, 'strength_score' => 58, 'sustainability_score' => 86,
                'project_types' => ['residential', 'commercial', 'mass'],
                'wall_types' => ['interior', 'infill'],
                'insulation_level' => 'medium', 'is_loadbearing' => false,
                'features' => ['کاهش صوت ۴۵ دسی‌بل بین واحدها', 'مقاومت آتش ۱۵۰ دقیقه', 'هندسه ماژولار سازگار با بلوک ۲۰'],
                'applications' => ['دیوار مشترک بین واحدها', 'دیوار میان‌قاب اسکلت فولادی', 'دیوار حیاط و محوطه'],
                'standards' => ['استاندارد ملی ایران ۷', 'مبحث ۱۸ مقررات ملی ساختمان'],
                'position' => 3, 'is_featured' => true,
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
                'position' => 4, 'is_featured' => true,
            ],
            [
                'product_category_id' => $insulating->id,
                'slug' => 'insulating-block-25', 'sku' => 'KI-25', 'name' => 'بلوک عایق ۲۵', 'name_en' => 'Insulating Block 25',
                'subtitle' => 'پوسته حرارتی بدون عایق افزوده',
                'summary' => 'بیست‌وپنج سانتی‌متر با آرایش حفره‌های چندردیفه؛ در بسیاری از اقلیم‌ها بدون نیاز به لایه عایق مجزا الزام مبحث ۱۹ را تأمین می‌کند.',
                'length_mm' => 400, 'width_mm' => 250, 'height_mm' => 250, 'thickness_mm' => 250, 'void_ratio' => 62,
                'weight_kg' => 14.80, 'compressive_strength_mpa' => 7.50, 'thermal_conductivity' => 0.210,
                'thermal_resistance' => 1.190, 'water_absorption' => 13.00, 'fire_resistance_min' => 240,
                'sound_reduction_db' => 52, 'units_per_pallet' => 60, 'units_per_sqm' => 10, 'mortar_per_sqm' => 24.0,
                'thermal_score' => 86, 'acoustic_score' => 88, 'strength_score' => 80, 'sustainability_score' => 92,
                'project_types' => ['residential', 'commercial', 'mass'],
                'wall_types' => ['exterior'],
                'insulation_level' => 'high', 'is_loadbearing' => true,
                'features' => ['ضریب λ برابر ۰٫۲۱ وات بر متر کلوین', 'حذف پل حرارتی با درز نر و ماده', 'مقاومت آتش چهار ساعته'],
                'applications' => ['دیوار خارجی اقلیم سرد', 'ساختمان‌های با گواهی انرژی', 'پروژه‌های مسکن ملی'],
                'standards' => ['استاندارد ملی ایران ۷', 'مبحث ۱۹ مقررات ملی ساختمان'],
                'position' => 5, 'is_featured' => true,
            ],
            [
                'product_category_id' => $insulating->id,
                'slug' => 'insulating-block-30', 'sku' => 'KI-30', 'name' => 'بلوک عایق ۳۰', 'name_en' => 'Insulating Block 30',
                'subtitle' => 'بالاترین عملکرد حرارتی خانواده',
                'summary' => 'سی سانتی‌متر ضخامت و بیشترین مقاومت حرارتی؛ برای ساختمان‌های کم‌مصرف، اقلیم‌های سرد و پروژه‌هایی که به گواهی انرژی نیاز دارند.',
                'length_mm' => 400, 'width_mm' => 300, 'height_mm' => 250, 'thickness_mm' => 300, 'void_ratio' => 64,
                'weight_kg' => 17.90, 'compressive_strength_mpa' => 8.00, 'thermal_conductivity' => 0.170,
                'thermal_resistance' => 1.765, 'water_absorption' => 12.50, 'fire_resistance_min' => 240,
                'sound_reduction_db' => 55, 'units_per_pallet' => 48, 'units_per_sqm' => 10, 'mortar_per_sqm' => 28.0,
                'thermal_score' => 96, 'acoustic_score' => 92, 'strength_score' => 84, 'sustainability_score' => 94,
                'project_types' => ['residential', 'commercial'],
                'wall_types' => ['exterior'],
                'insulation_level' => 'very_high', 'is_loadbearing' => true,
                'features' => ['مقاومت حرارتی R برابر ۱٫۷۶', 'کاهش تا ۴۰ درصد مصرف انرژی گرمایش', 'دیوار تک‌لایه بدون عایق افزوده'],
                'applications' => ['ساختمان کم‌مصرف انرژی', 'ویلا و اقامتگاه در اقلیم سرد', 'ساختمان‌های سبز'],
                'standards' => ['استاندارد ملی ایران ۷', 'مبحث ۱۹ مقررات ملی ساختمان', 'ISO 10456'],
                'position' => 6, 'is_featured' => true,
            ],
            [
                'product_category_id' => $thermal->id,
                'slug' => 'thermal-block-20-plus', 'sku' => 'KT-20P', 'name' => 'بلوک حرارتی ۲۰ پلاس', 'name_en' => 'Thermal Block 20 Plus',
                'subtitle' => 'عملکرد بلوک ۲۵ در ضخامت ۲۰',
                'summary' => 'آرایش حفره‌ی زیگزاگی مسیر انتقال حرارت را طولانی می‌کند؛ عملکرد حرارتی نزدیک به بلوک ۲۵ بدون از دست دادن فضای مفید.',
                'length_mm' => 400, 'width_mm' => 200, 'height_mm' => 250, 'thickness_mm' => 200, 'void_ratio' => 63,
                'weight_kg' => 10.40, 'compressive_strength_mpa' => 6.00, 'thermal_conductivity' => 0.190,
                'thermal_resistance' => 1.053, 'water_absorption' => 13.20, 'fire_resistance_min' => 180,
                'sound_reduction_db' => 47, 'units_per_pallet' => 90, 'units_per_sqm' => 10, 'mortar_per_sqm' => 20.0,
                'thermal_score' => 90, 'acoustic_score' => 76, 'strength_score' => 68, 'sustainability_score' => 90,
                'project_types' => ['residential', 'commercial', 'mass'],
                'wall_types' => ['exterior', 'infill'],
                'insulation_level' => 'high', 'is_loadbearing' => false,
                'features' => ['مسیر زیگزاگی انتقال حرارت', 'وزن کمتر از بلوک ۲۵ با λ نزدیک به آن', 'حفظ فضای مفید داخلی'],
                'applications' => ['بازسازی نما با محدودیت ضخامت', 'دیوار خارجی برج مسکونی', 'پروژه‌های بهینه‌سازی انرژی'],
                'standards' => ['استاندارد ملی ایران ۷', 'مبحث ۱۹ مقررات ملی ساختمان'],
                'position' => 7, 'is_featured' => false,
            ],
            [
                'product_category_id' => $light->id,
                'slug' => 'lightweight-block-20', 'sku' => 'KL-20', 'name' => 'بلوک سبک ۲۰', 'name_en' => 'Lightweight Block 20',
                'subtitle' => 'کاهش بار مرده تا ۲۸ درصد',
                'summary' => 'ترکیب خاک رس با افزودنی متخلخل‌ساز، چگالی را پایین می‌آورد؛ مناسب اسکلت‌هایی که ظرفیت باربری محدودی دارند و پروژه‌های مقاوم‌سازی.',
                'length_mm' => 400, 'width_mm' => 200, 'height_mm' => 200, 'thickness_mm' => 200, 'void_ratio' => 60,
                'weight_kg' => 8.40, 'compressive_strength_mpa' => 4.50, 'thermal_conductivity' => 0.190,
                'thermal_resistance' => 1.053, 'water_absorption' => 16.00, 'fire_resistance_min' => 180,
                'sound_reduction_db' => 44, 'units_per_pallet' => 120, 'units_per_sqm' => 12.5, 'mortar_per_sqm' => 21.0,
                'thermal_score' => 82, 'acoustic_score' => 68, 'strength_score' => 52, 'sustainability_score' => 90,
                'project_types' => ['residential', 'commercial', 'mass'],
                'wall_types' => ['interior', 'infill', 'exterior'],
                'insulation_level' => 'high', 'is_loadbearing' => false,
                'features' => ['۲۸ درصد سبک‌تر از بلوک هم‌ابعاد', 'کاهش بار زلزله‌ی وارد بر اسکلت', 'حمل و چیدمان آسان‌تر برای مجری'],
                'applications' => ['مقاوم‌سازی و اضافه طبقه', 'ساختمان‌های بلندمرتبه', 'سازه‌های LSF و اسکلت سبک'],
                'standards' => ['استاندارد ملی ایران ۷', 'آیین‌نامه ۲۸۰۰ ویرایش چهارم'],
                'position' => 8, 'is_featured' => false,
            ],
            [
                'product_category_id' => $roof->id,
                'slug' => 'roof-block-20', 'sku' => 'KR-20', 'name' => 'بلوک سقفی ۲۰', 'name_en' => 'Roof Block 20',
                'subtitle' => 'سیستم تیرچه‌بلوک سفالی',
                'summary' => 'بلوک سقفی سفالی برای سیستم تیرچه‌بلوک؛ سبک‌تر از بلوک بتنی، با عملکرد حرارتی و صوتی بهتر و بتن‌ریزی تمیزتر.',
                'length_mm' => 400, 'width_mm' => 250, 'height_mm' => 200, 'thickness_mm' => 200, 'void_ratio' => 56,
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
                'position' => 9, 'is_featured' => false,
            ],
            [
                'product_category_id' => $accessories->id,
                'slug' => 'lintel-block', 'sku' => 'KA-LNT', 'name' => 'نعل درگاه سفالی', 'name_en' => 'Ceramic Lintel',
                'subtitle' => 'حذف پل حرارتی بالای بازشو',
                'summary' => 'قطعه‌ی U شکل سفالی که به‌عنوان قالب ماندگار نعل درگاه عمل می‌کند و پیوستگی حرارتی دیوار را در بالای در و پنجره حفظ می‌کند.',
                'length_mm' => 1000, 'width_mm' => 200, 'height_mm' => 200, 'thickness_mm' => 200, 'void_ratio' => 30,
                'weight_kg' => 16.00, 'compressive_strength_mpa' => 9.00, 'thermal_conductivity' => 0.320,
                'thermal_resistance' => 0.625, 'water_absorption' => 13.00, 'fire_resistance_min' => 180,
                'sound_reduction_db' => 46, 'units_per_pallet' => 40, 'units_per_sqm' => 0, 'mortar_per_sqm' => 0,
                'thermal_score' => 70, 'acoustic_score' => 66, 'strength_score' => 88, 'sustainability_score' => 84,
                'project_types' => ['residential', 'commercial', 'mass'],
                'wall_types' => ['exterior', 'interior', 'infill'],
                'insulation_level' => 'medium', 'is_loadbearing' => true,
                'features' => ['قالب ماندگار — بدون قالب‌بندی چوبی', 'یکنواختی نما و رنگ با بدنه دیوار', 'حذف پل حرارتی بالای بازشو'],
                'applications' => ['بالای در و پنجره', 'بازشوهای تا دهانه ۱٫۸ متر'],
                'standards' => ['مبحث ۸ مقررات ملی ساختمان'],
                'position' => 10, 'is_featured' => false,
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
                'products' => ['insulating-block-25', 'insulating-block-30', 'thermal-block-20-plus'],
                'position' => 1,
            ],
            [
                'slug' => 'acoustic-separation',
                'title' => 'جداسازی صوتی بین واحدها',
                'title_en' => 'Acoustic Separation',
                'subtitle' => 'دیوار مشترک با کاهش صوت بالای ۵۰ دسی‌بل',
                'summary' => 'جرم حجمی سفال به‌همراه حفره‌های هوا، ترکیبی است که هم صدای هوابرد را کاهش می‌دهد و هم بدون افزودن وزن زیاد به سازه اجرا می‌شود.',
                'benefits' => ['کاهش صوت تا ۵۵ دسی‌بل', 'تأمین الزام مبحث ۱۸', 'بدون نیاز به لایه صوتی افزوده'],
                'products' => ['ceramic-block-15', 'ceramic-block-20', 'insulating-block-25'],
                'position' => 2,
            ],
            [
                'slug' => 'mass-housing',
                'title' => 'انبوه‌سازی و مسکن ملی',
                'title_en' => 'Mass Housing',
                'subtitle' => 'تحویل زمان‌بندی‌شده در مقیاس صدها هزار متر مربع',
                'summary' => 'برای پروژه‌های بزرگ، مسئله فقط قیمت بلوک نیست؛ مسئله تضمین تأمین، یکنواختی کیفیت میان بچ‌های تولید و برنامه‌ی تحویل هماهنگ با پیشرفت کار است.',
                'benefits' => ['قرارداد تأمین با برنامه تحویل مرحله‌ای', 'یکنواختی ابعاد میان بچ‌های تولید', 'پشتیبانی فنی در کارگاه', 'بارگیری پالت‌شده و شرینک‌پیچ'],
                'products' => ['ceramic-block-20', 'partition-block-10', 'roof-block-20'],
                'position' => 3,
            ],
            [
                'slug' => 'seismic-retrofit',
                'title' => 'مقاوم‌سازی و اضافه طبقه',
                'title_en' => 'Retrofit & Vertical Extension',
                'subtitle' => 'کاهش بار مرده، کاهش نیروی زلزله',
                'summary' => 'نیروی زلزله متناسب با جرم سازه است. با جایگزینی دیوارهای سنگین، بار مرده و به‌تبع آن برش پایه کاهش می‌یابد — اغلب ارزان‌تر از تقویت اسکلت.',
                'benefits' => ['کاهش بار مرده تا ۲۸ درصد', 'کاهش برش پایه ناشی از زلزله', 'اجرای خشک‌تر و سریع‌تر در ساختمان در حال بهره‌برداری'],
                'products' => ['lightweight-block-20', 'partition-block-7', 'partition-block-10'],
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
