<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['slug' => 'residential', 'name' => 'مسکونی', 'name_en' => 'Residential', 'position' => 1],
            ['slug' => 'commercial', 'name' => 'تجاری و اداری', 'name_en' => 'Commercial', 'position' => 2],
            ['slug' => 'industrial', 'name' => 'صنعتی', 'name_en' => 'Industrial', 'position' => 3],
            ['slug' => 'mass-housing', 'name' => 'انبوه‌سازی', 'name_en' => 'Mass Housing', 'position' => 4],
            ['slug' => 'public', 'name' => 'عمومی و فرهنگی', 'name_en' => 'Public', 'position' => 5],
        ])->mapWithKeys(fn ($data) => [$data['slug'] => ProjectCategory::create($data)]);

        $projects = [
            [
                'category' => 'residential',
                'slug' => 'niavaran-residential-complex',
                'title' => 'مجتمع مسکونی نیاوران',
                'title_en' => 'Niavaran Residential Complex',
                'client' => 'گروه ساختمانی آرمان',
                'architect' => 'دفتر معماری شار',
                'city' => 'تهران', 'province' => 'تهران', 'year' => 1403,
                'area_sqm' => 18500, 'blocks_used' => 142000,
                'summary' => 'برج مسکونی هجده طبقه با دیوار خارجی تک‌لایه‌ی بلوک عایق ۲۵؛ حذف کامل لایه‌ی عایق افزوده در پوسته.',
                'description' => 'الزام کارفرما، دریافت گواهی انرژی کلاس B بود. با انتخاب بلوک عایق ۲۵ برای پوسته و بلوک ۱۵ برای دیوار مشترک بین واحدها، هم الزام حرارتی مبحث ۱۹ و هم الزام صوتی مبحث ۱۸ بدون افزودن لایه‌ی جداگانه تأمین شد. حذف مرحله‌ی عایق‌کاری، حدود شش هفته از برنامه‌ی زمان‌بندی نما کاست.',
                'products' => ['insulating-block-25', 'ceramic-block-15', 'partition-block-10'],
                'is_featured' => true, 'position' => 1,
            ],
            [
                'category' => 'commercial',
                'slug' => 'isfahan-office-tower',
                'title' => 'برج اداری سپاهان',
                'title_en' => 'Sepahan Office Tower',
                'client' => 'شرکت سرمایه‌گذاری سپاهان',
                'architect' => 'مهندسین مشاور نقش جهان',
                'city' => 'اصفهان', 'province' => 'اصفهان', 'year' => 1402,
                'area_sqm' => 24000, 'blocks_used' => 186000,
                'summary' => 'بیست‌ودو طبقه اداری با اسکلت فولادی و دیوار میان‌قاب بلوک ۲۰؛ سرعت اجرا معیار اصلی انتخاب بود.',
                'description' => 'در سازه‌های فولادی بلندمرتبه، سرعت بسته شدن پوسته مسیر بحرانی پروژه است. ابعاد ۲۰×۲۰×۴۰ و درز نر و ماده، نرخ دیوارچینی هر اکیپ را نسبت به آجر سنتی بیش از دو برابر کرد. کاهش بار مرده نیز در طراحی اتصالات میان‌قاب لحاظ شد.',
                'products' => ['ceramic-block-20', 'partition-block-10', 'lintel-block'],
                'is_featured' => true, 'position' => 2,
            ],
            [
                'category' => 'mass-housing',
                'slug' => 'national-housing-parand',
                'title' => 'مسکن ملی پرند — فاز ۷',
                'title_en' => 'Parand National Housing — Phase 7',
                'client' => 'شرکت عمران شهرهای جدید',
                'architect' => 'مهندسین مشاور طرح و ساخت',
                'city' => 'پرند', 'province' => 'تهران', 'year' => 1403,
                'area_sqm' => 96000, 'blocks_used' => 720000,
                'summary' => 'هزار و دویست واحد مسکونی با برنامه‌ی تحویل مرحله‌ای هماهنگ با پیشرفت هر بلوک ساختمانی.',
                'description' => 'چالش اصلی، تأمین پیوسته در مقیاس بزرگ بدون انباشت مصالح در کارگاه بود. قرارداد تأمین با برنامه‌ی تحویل هفتگی بسته شد و هر پالت با شناسه‌ی بچ تولید ارسال شد تا یکنواختی ابعاد میان بلوک‌های مختلف پروژه تضمین شود.',
                'products' => ['ceramic-block-20', 'partition-block-10', 'roof-block-20'],
                'is_featured' => true, 'position' => 3,
            ],
            [
                'category' => 'industrial',
                'slug' => 'kaveh-industrial-warehouse',
                'title' => 'انبار صنعتی شهرک کاوه',
                'title_en' => 'Kaveh Industrial Warehouse',
                'client' => 'گروه صنعتی کاوه',
                'architect' => 'واحد فنی کارفرما',
                'city' => 'ساوه', 'province' => 'مرکزی', 'year' => 1401,
                'area_sqm' => 12000, 'blocks_used' => 68000,
                'summary' => 'دیوار پیرامونی سوله‌ی انبار با بلوک ۲۰؛ مقاومت آتش سه ساعته الزام بیمه‌ی کارفرما بود.',
                'description' => 'برای انبار مواد اولیه، شرکت بیمه مقاومت حرارتی و آتش دیوارهای جداکننده را شرط پوشش قرار داده بود. سفال به‌عنوان ماده‌ای غیرقابل اشتعال با مقاومت آتش ۱۸۰ دقیقه، بدون نیاز به پوشش ضدحریق افزوده، این شرط را تأمین کرد.',
                'products' => ['ceramic-block-20', 'lintel-block'],
                'is_featured' => true, 'position' => 4,
            ],
            [
                'category' => 'public',
                'slug' => 'shiraz-cultural-center',
                'title' => 'مرکز فرهنگی شیراز',
                'title_en' => 'Shiraz Cultural Center',
                'client' => 'شهرداری شیراز',
                'architect' => 'دفتر معماری هفت‌اقلیم',
                'city' => 'شیراز', 'province' => 'فارس', 'year' => 1402,
                'area_sqm' => 6800, 'blocks_used' => 41000,
                'summary' => 'سفال نمایان به‌عنوان زبان معماری پروژه؛ بافت و رنگ طبیعی خاک، بدون هیچ پوشش نهایی.',
                'description' => 'معمار پروژه، بلوک سفالی را نه به‌عنوان زیرسازی بلکه به‌عنوان سطح نهایی نما انتخاب کرد. یکنواختی رنگ میان بچ‌های تولید بحرانی بود؛ کل حجم مورد نیاز از یک دوره‌ی پخت پیوسته تأمین و پیش از ارسال، شاهد رنگ تأیید شد.',
                'products' => ['ceramic-block-20', 'ceramic-block-15', 'lintel-block'],
                'is_featured' => true, 'position' => 5,
            ],
            [
                'category' => 'residential',
                'slug' => 'lavasan-villa',
                'title' => 'ویلای لواسان',
                'title_en' => 'Lavasan Villa',
                'client' => 'کارفرمای خصوصی',
                'architect' => 'استودیو معماری کاو',
                'city' => 'لواسان', 'province' => 'تهران', 'year' => 1403,
                'area_sqm' => 720, 'blocks_used' => 5400,
                'summary' => 'ویلای دو طبقه در اقلیم سرد کوهستانی با بلوک عایق ۳۰ و هدف کمینه‌سازی مصرف انرژی گرمایش.',
                'description' => 'در ارتفاع ۱۸۰۰ متری، دوره‌ی گرمایش طولانی است. با دیوار تک‌لایه‌ی بلوک ۳۰ و مقاومت حرارتی ۱٫۷۶، محاسبات مبحث ۱۹ بدون هیچ عایق افزوده‌ای تأمین شد و بار گرمایشی محاسباتی نسبت به گزینه‌ی مرجع حدود چهل درصد کاهش یافت.',
                'products' => ['insulating-block-30', 'partition-block-10'],
                'is_featured' => true, 'position' => 6,
            ],
            [
                'category' => 'commercial',
                'slug' => 'mashhad-retail-complex',
                'title' => 'مجتمع تجاری رضوان مشهد',
                'title_en' => 'Rezvan Retail Complex',
                'client' => 'هلدینگ رضوان',
                'architect' => 'مهندسین مشاور آرمه',
                'city' => 'مشهد', 'province' => 'خراسان رضوی', 'year' => 1401,
                'area_sqm' => 31000, 'blocks_used' => 210000,
                'summary' => 'پنج طبقه تجاری با دیوارهای جداکننده‌ی واحدها و الزام صوتی و آتش بالا.',
                'description' => 'در مجتمع تجاری، دیوار بین واحدها هم‌زمان باید صوت را کنترل کند و مانع گسترش آتش شود. ترکیب بلوک ۱۵ برای جداکننده‌ها و بلوک ۲۰ برای پوسته، هر دو الزام را بدون سیستم ترکیبی پیچیده پوشش داد.',
                'products' => ['ceramic-block-15', 'ceramic-block-20', 'partition-block-10'],
                'is_featured' => false, 'position' => 7,
            ],
            [
                'category' => 'mass-housing',
                'slug' => 'tabriz-cooperative-housing',
                'title' => 'تعاونی مسکن فرهنگیان تبریز',
                'title_en' => 'Tabriz Teachers Housing',
                'client' => 'تعاونی مسکن فرهنگیان',
                'architect' => 'مهندسین مشاور ارس',
                'city' => 'تبریز', 'province' => 'آذربایجان شرقی', 'year' => 1402,
                'area_sqm' => 42000, 'blocks_used' => 305000,
                'summary' => 'چهارصد و بیست واحد در اقلیم سرد؛ بلوک حرارتی ۲۰ پلاس برای حفظ فضای مفید داخلی.',
                'description' => 'محدودیت سطح زیربنای مصوب اجازه‌ی ضخیم‌تر کردن دیوار خارجی را نمی‌داد. بلوک حرارتی ۲۰ پلاس با λ برابر ۰٫۱۹ در همان ضخامت بیست سانتی‌متر، الزام اقلیم سرد را بدون از دست دادن متراژ مفید هر واحد تأمین کرد.',
                'products' => ['thermal-block-20-plus', 'partition-block-10', 'roof-block-20'],
                'is_featured' => false, 'position' => 8,
            ],
            [
                'category' => 'residential',
                'slug' => 'karaj-retrofit-tower',
                'title' => 'مقاوم‌سازی و اضافه طبقه کرج',
                'title_en' => 'Karaj Retrofit & Extension',
                'client' => 'شرکت بازآفرینی شهری',
                'architect' => 'مهندسین مشاور پایا',
                'city' => 'کرج', 'province' => 'البرز', 'year' => 1403,
                'area_sqm' => 9400, 'blocks_used' => 47000,
                'summary' => 'افزودن دو طبقه به ساختمان موجود با بلوک سبک ۲۰؛ کاهش بار مرده به‌جای تقویت اسکلت.',
                'description' => 'ظرفیت باربری اسکلت موجود اجازه‌ی اضافه طبقه با دیوار معمولی را نمی‌داد. با جایگزینی دیوارهای موجود طبقات بالا با بلوک سبک، بخشی از ظرفیت آزاد شد و نیاز به تقویت گسترده‌ی ستون‌ها حذف گردید.',
                'products' => ['lightweight-block-20', 'partition-block-7'],
                'is_featured' => false, 'position' => 9,
            ],
        ];

        foreach ($projects as $data) {
            $slugs = $data['products'];
            $categorySlug = $data['category'];
            unset($data['products'], $data['category']);

            $project = Project::create([...$data, 'project_category_id' => $categories[$categorySlug]->id]);
            $project->products()->sync(Product::whereIn('slug', $slugs)->pluck('id'));
        }
    }
}
