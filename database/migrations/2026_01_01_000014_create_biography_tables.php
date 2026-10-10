<?php

use App\Models\Milestone;
use App\Models\Person;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * بیوگرافی: خطِ زمانِ شرکت، و بنیان‌گذاران و مدیران.
 *
 * داده‌ی آغازین همین‌جا نوشته می‌شود و نه در seeder: مسیرِ به‌روزرسانیِ سرور
 * فقط migrate می‌گیرد، و صفحه‌ای که روزِ اول خالی باشد، روزِ اول خراب است.
 *
 * خطِ زمان فقط از آنچه سایت همین حالا می‌گوید ساخته شده — «درباره ما»: آغاز
 * با یک کوره در ۱۳۸۰، خط اکستروژن و کوره‌ی تونلی در ۱۳۹۲، و امروز. هیچ رویداد
 * یا عددِ تازه‌ای ساخته نشده.
 *
 * برای مدیران، یک ردیفِ پنهان با الگوی متن ساخته می‌شود و نه شخصی ساختگی:
 * نام و زندگی‌نامه‌ی آدمِ واقعی را فقط خودِ شرکت می‌داند.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            /*
            | سالِ شمسی، همان‌طور که مدیر می‌نویسد. صفحه‌ی انگلیسی ۶۲۱ اضافه
            | می‌کند — همان قاعده‌ای که سالِ تأسیس در Brand::founded دارد.
            | خالی یعنی «امروز».
            */
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('title', 160);
            $table->text('text')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('role', 120)->nullable();
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        $this->seedMilestones();
        $this->seedPersonTemplate();
    }

    protected function seedMilestones(): void
    {
        $rows = [
            [
                'year' => 1380,
                'title' => 'یک کوره، پنج نفر',
                'text' => 'کار با یک کوره‌ی سنتی و پنج نفر شروع شد. بازار هنوز بلوک سفالی را جایگزین آجر نمی‌دانست و بیشتر سفارش‌ها از پیمانکارانی می‌آمد که یک‌بار امتحانش کرده بودند.',
                'en' => ['One kiln, five people', 'It began with a single traditional kiln and five people. The market did not yet see ceramic block as a replacement for brick, and most orders came from contractors who had tried it once.'],
                'ar' => ['فرن واحد وخمسة أشخاص', 'بدأ العمل بفرن تقليدي واحد وخمسة أشخاص. لم يكن السوق يرى البلوك الفخاري بديلاً عن الطوب بعد، وجاءت معظم الطلبات من مقاولين جرّبوه مرة.'],
            ],
            [
                'year' => 1392,
                'title' => 'خط اکستروژن و کوره‌ی تونلی',
                'text' => 'خط اکستروژن با کنترل خلأ و کوره‌ی تونلی جایگزین روش قبلی شد. رواداری ابعادی از چند میلی‌متر به کمتر از دو میلی‌متر رسید — و همین یک عدد در ورود به پروژه‌های بزرگ تفاوت ساخت.',
                'en' => ['Extrusion line and tunnel kiln', 'A vacuum-controlled extrusion line and a tunnel kiln replaced the old process. Dimensional tolerance fell from several millimetres to under two — and that one number opened the door to large projects.'],
                'ar' => ['خط البثق والفرن النفقي', 'حلّ خط بثق بتحكم في التفريغ وفرن نفقي محل الطريقة السابقة. انخفض التفاوت البُعدي من عدة مليمترات إلى أقل من مليمترين — وهذا الرقم وحده فتح الباب أمام المشاريع الكبيرة.'],
            ],
            [
                'year' => null,
                'title' => 'دو خط موازی',
                'text' => 'امروز دو خط موازی و ظرفیت سالانه‌ی صد و بیست هزار تن داریم. آنچه از روز اول تغییر نکرده: هر بچ تولید، پیش از بارگیری آزمون می‌شود.',
                'en' => ['Two parallel lines', 'Today we run two parallel lines with an annual capacity of 120,000 tonnes. What has not changed since day one: every production batch is tested before it is loaded.'],
                'ar' => ['خطان متوازيان', 'لدينا اليوم خطان متوازيان بطاقة سنوية تبلغ مئة وعشرين ألف طن. ما لم يتغير منذ اليوم الأول: كل دفعة إنتاج تُختبر قبل التحميل.'],
            ],
        ];

        foreach ($rows as $i => $row) {
            $id = DB::table('milestones')->insertGetId([
                'year' => $row['year'],
                'title' => $row['title'],
                'text' => $row['text'],
                'is_active' => true,
                'position' => $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach (['en', 'ar'] as $code) {
                [$title, $text] = $row[$code];
                $this->translate(Milestone::class, $id, $code, ['title' => $title, 'text' => $text]);
            }
        }
    }

    protected function seedPersonTemplate(): void
    {
        DB::table('people')->insert([
            'name' => 'نام و نام خانوادگی',
            'role' => 'بنیان‌گذار و مدیرعامل',
            'bio' => "چند جمله درباره‌ی پیشینه، تحصیلات و مسیرِ کاری.\n\nچه شد که کارخانه را راه انداخت، و امروز مسئولِ چه بخشی است.",
            // پنهان: تا شرکت نام و متنِ واقعی را ننوشته، روی سایت دیده نمی‌شود
            'is_active' => false,
            'position' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function translate(string $type, int $id, string $locale, array $values): void
    {
        foreach ($values as $field => $value) {
            DB::table('translations')->insert([
                'translatable_type' => $type,
                'translatable_id' => $id,
                'locale' => $locale,
                'field' => $field,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('translations')->whereIn('translatable_type', [Milestone::class, Person::class])->delete();
        Schema::dropIfExists('people');
        Schema::dropIfExists('milestones');
    }
};
