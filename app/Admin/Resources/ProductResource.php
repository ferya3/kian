<?php

namespace App\Admin\Resources;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\Admin\Field;
use App\Support\Admin\Resource;
use Illuminate\Database\Eloquent\Model;

class ProductResource extends Resource
{
    public static string $model = Product::class;

    public static string $slug = 'products';

    public static string $label = 'محصولات';

    public static string $singular = 'محصول';

    public static string $icon = 'layers';

    public static string $group = 'کاتالوگ';

    public static string $orderBy = 'position';

    public static string $orderDir = 'asc';

    public static function searchable(): array
    {
        return ['name', 'name_en', 'sku', 'summary'];
    }

    public static function with(): array
    {
        return ['category'];
    }

    public static function publicUrl(Model $record): ?string
    {
        return route('products.show', $record);
    }

    public static function fields(): array
    {
        // بخش‌ها به ترتیب اعلام ساخته می‌شوند؛ فرم ۳۵ فیلدی محصول این‌طور به
        // چند پنل کوتاه می‌شکند و روی گوشی یک ستون بی‌پایان نمی‌ماند.
        return [
            Field::text('name', 'نام محصول')->rules(['required'])->inList(true)->half(),
            Field::text('name_en', 'نام انگلیسی')->rules(['nullable'])->half(),
            Field::text('sku', 'کد محصول')->rules(['required', 'max:40'])->inList()->third(),
            Field::slug('slug')->rules(['required'])->third(),
            Field::relation('product_category_id', 'دسته‌بندی', ProductCategory::class)
                ->relationName('category')
                ->rules(['required', 'exists:product_categories,id'])->inList()->third(),

            Field::image('hero_image', 'تصویر اصلی', 'products')
                ->rules(['nullable'])->half()->section('تصاویر')
                ->hint('اگر خالی بماند، بلوک سه‌بعدی وکتوری نمایش داده می‌شود.'),
            Field::image('section_image', 'تصویر مقطع', 'products')
                ->rules(['nullable'])->half()->section('تصاویر'),
            Field::gallery('gallery', 'گالری تصاویر', 'products')
                ->rules(['nullable'])->section('تصاویر'),

            Field::text('subtitle', 'زیرعنوان')->rules(['nullable'])->section('معرفی'),
            Field::textarea('summary', 'خلاصه')->rules(['nullable', 'max:600'])->section('معرفی'),
            Field::longtext('description', 'توضیح کامل')->rules(['nullable', 'max:6000'])->section('معرفی'),

            Field::number('length_mm', 'طول')->rules(['required', 'min:1', 'max:5000'])->suffix('mm')->third()->section('ابعاد'),
            Field::number('width_mm', 'عرض')->rules(['required', 'min:1', 'max:5000'])->suffix('mm')->third()->section('ابعاد'),
            Field::number('height_mm', 'ارتفاع')->rules(['required', 'min:1', 'max:5000'])->suffix('mm')->third()->section('ابعاد'),
            Field::number('thickness_mm', 'ضخامت دیوار')->rules(['required', 'min:1', 'max:5000'])->suffix('mm')->third()->section('ابعاد'),
            Field::number('void_ratio', 'درصد تخلخل')->rules(['nullable', 'min:0', 'max:100'])->suffix('%')->third()->section('ابعاد'),
            Field::number('units_per_pallet', 'تعداد در پالت')->rules(['nullable', 'min:0', 'max:10000'])->third()->section('ابعاد'),

            Field::decimal('weight_kg', 'وزن')->rules(['required', 'min:0', 'max:999'])->suffix('kg')->inList()->third()->section('مشخصات فنی'),
            Field::decimal('compressive_strength_mpa', 'مقاومت فشاری')->rules(['required', 'min:0', 'max:99'])->suffix('MPa')->inList()->third()->section('مشخصات فنی'),
            Field::decimal('thermal_conductivity', 'ضریب هدایت حرارتی (λ)')->rules(['required', 'min:0', 'max:9'])->suffix('W/m·K')->inList()->third()->section('مشخصات فنی'),
            Field::decimal('thermal_resistance', 'مقاومت حرارتی (R)')->rules(['nullable', 'min:0', 'max:99'])->suffix('m²·K/W')->third()->section('مشخصات فنی'),
            Field::decimal('water_absorption', 'جذب آب')->rules(['required', 'min:0', 'max:100'])->suffix('%')->third()->section('مشخصات فنی'),
            Field::number('fire_resistance_min', 'مقاومت آتش')->rules(['nullable', 'min:0', 'max:600'])->suffix('دقیقه')->third()->section('مشخصات فنی'),
            Field::number('sound_reduction_db', 'کاهش صوت')->rules(['nullable', 'min:0', 'max:120'])->suffix('dB')->third()->section('مشخصات فنی'),
            Field::decimal('units_per_sqm', 'تعداد در متر مربع')->rules(['nullable', 'min:0', 'max:999'])->third()->section('مشخصات فنی'),
            Field::decimal('mortar_per_sqm', 'ملات مصرفی')->rules(['nullable', 'min:0', 'max:999'])->suffix('لیتر/m²')->third()->section('مشخصات فنی'),

            // امتیازها نمودار میله‌ای صفحه‌ی محصول را می‌سازند.
            Field::number('thermal_score', 'امتیاز حرارتی')->rules(['required', 'min:0', 'max:100'])->suffix('۰ تا ۱۰۰')->third()->section('امتیازهای عملکرد'),
            Field::number('acoustic_score', 'امتیاز صوتی')->rules(['required', 'min:0', 'max:100'])->suffix('۰ تا ۱۰۰')->third()->section('امتیازهای عملکرد'),
            Field::number('strength_score', 'امتیاز سازه‌ای')->rules(['required', 'min:0', 'max:100'])->suffix('۰ تا ۱۰۰')->third()->section('امتیازهای عملکرد'),
            Field::number('sustainability_score', 'امتیاز پایداری')->rules(['nullable', 'min:0', 'max:100'])->suffix('۰ تا ۱۰۰')->third()->section('امتیازهای عملکرد'),

            Field::checkboxes('project_types', 'نوع پروژه‌های مناسب',
                collect(config('kian.finder.project_types'))->map(fn ($t) => $t['label'])->all())
                ->rules(['nullable'])->half()->section('موتور انتخاب محصول'),
            Field::checkboxes('wall_types', 'کاربرد در دیوار',
                collect(config('kian.finder.wall_types'))->map(fn ($t) => $t['label'])->all())
                ->rules(['nullable'])->half()->section('موتور انتخاب محصول'),
            Field::select('insulation_level', 'سطح عایق حرارتی',
                collect(config('kian.finder.insulation_levels'))->map(fn ($t) => $t['label'])->all())
                ->rules(['required'])->half()->section('موتور انتخاب محصول'),
            Field::boolean('is_loadbearing', 'باربر است')->half()->section('موتور انتخاب محصول'),

            Field::lines('features', 'ویژگی‌ها')->rules(['nullable'])->section('فهرست‌های صفحه‌ی محصول'),
            Field::lines('applications', 'کاربردها')->rules(['nullable'])->section('فهرست‌های صفحه‌ی محصول'),
            Field::lines('standards', 'استانداردها')->rules(['nullable'])->section('فهرست‌های صفحه‌ی محصول'),

            Field::text('meta_title', 'عنوان سئو')->rules(['nullable', 'max:70'])
                ->hint('اگر خالی بماند از نام محصول ساخته می‌شود. حداکثر ۷۰ نویسه.')->section('سئو'),
            Field::textarea('meta_description', 'توضیح سئو')->rules(['nullable', 'max:320'])
                ->hint('۱۵۰ تا ۱۶۰ نویسه بهترین نتیجه را در گوگل می‌دهد.')->section('سئو'),

            Field::number('position', 'ترتیب نمایش')->rules(['nullable', 'min:0', 'max:999'])->third()->section('انتشار'),
            Field::boolean('is_active', 'فعال')->default(true)->inList()->third()->section('انتشار'),
            Field::boolean('is_featured', 'نمایش در صفحه اصلی')->third()->section('انتشار'),
        ];
    }
}
