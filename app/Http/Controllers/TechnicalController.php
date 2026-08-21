<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Document;
use App\Models\Faq;
use App\Models\Product;
use App\Support\Schema;
use Illuminate\Http\Request;

class TechnicalController extends Controller
{
    public function index()
    {
        $this->seo()
            ->title('مرکز فنی — دیتاشیت، CAD، BIM و ضوابط اجرا')
            ->description('همه‌ی آنچه مهندس، معمار و پیمانکار لازم دارد: دیتاشیت محصولات، کاتالوگ فنی، فایل‌های DWG و IFC، آبجکت‌های Revit، راهنمای اجرا و گواهی‌نامه‌ها.')
            ->breadcrumbs([['خانه', route('home')], ['مرکز فنی', null]]);

        return view('pages.technical.index', [
            'groups' => Document::query()->orderBy('position')->get()->groupBy('category'),
            'labels' => Document::CATEGORIES,
            'products' => Product::query()->active()->orderBy('position')->get(),
            'certificates' => Certificate::query()->orderBy('position')->take(4)->get(),
        ]);
    }

    public function downloads(Request $request)
    {
        $documents = Document::query()
            ->with('product')
            ->when($request->string('category')->toString(), fn ($q, $c) => $q->where('category', $c))
            ->when($request->string('audience')->toString(), fn ($q, $a) => $q->where('audience', $a))
            ->when($request->string('format')->toString(), fn ($q, $f) => $q->where('format', $f))
            ->when($request->string('q')->toString(), fn ($q, $term) => $q->where('title', 'like', "%{$term}%"))
            ->orderBy('category')
            ->orderBy('position')
            ->get();

        $this->seo()
            ->title('مرکز دانلود فایل‌های فنی')
            ->description('دانلود دیتاشیت، کاتالوگ، فایل‌های CAD و BIM، راهنمای اجرا و گواهی‌نامه‌های محصولات سفالی.')
            ->breadcrumbs([
                ['خانه', route('home')],
                ['مرکز فنی', route('technical.index')],
                ['دانلودها', null],
            ]);

        return view('pages.technical.downloads', [
            'documents' => $documents,
            'labels' => Document::CATEGORIES,
            'audiences' => Document::AUDIENCES,
            'formats' => Document::query()->distinct()->orderBy('format')->pluck('format'),
        ]);
    }

    public function installation()
    {
        $this->seo()
            ->title('راهنمای اجرا — روش صحیح چیدمان بلوک سفالی')
            ->description('گام‌به‌گام اجرای دیوار سفالی: آماده‌سازی بستر، ملات، رگ‌چینی، نعل درگاه، اتصال به قاب و نکات کنترلی پیمانکار.')
            ->breadcrumbs([
                ['خانه', route('home')],
                ['مرکز فنی', route('technical.index')],
                ['راهنمای اجرا', null],
            ]);

        return view('pages.technical.installation', [
            'guides' => Document::query()->category('installation')->orderBy('position')->get(),
            'faqs' => Faq::query()->where('group', 'installation')->orderBy('position')->get(),
        ]);
    }

    public function certificates()
    {
        $this->seo()
            ->title('گواهی‌نامه‌ها و استانداردها')
            ->description('استاندارد ملی ایران، مبحث ۱۹ مقررات ملی ساختمان، ISO 9001 و گزارش‌های آزمون مرکز تحقیقات راه، مسکن و شهرسازی.')
            ->breadcrumbs([
                ['خانه', route('home')],
                ['مرکز فنی', route('technical.index')],
                ['گواهی‌نامه‌ها', null],
            ]);

        return view('pages.technical.certificates', [
            'certificates' => Certificate::query()->orderBy('position')->get(),
            'standards' => Document::query()->category('standard')->orderBy('position')->get(),
        ]);
    }

    /** ترتیب نمایش دسته‌ها: از پرتکرارترین پرسش‌های فنی به عمومی‌ترین. */
    protected const FAQ_GROUP_ORDER = ['technical', 'installation', 'order', 'general'];

    public function faq()
    {
        $faqs = Faq::query()
            ->orderBy('position')
            ->get()
            ->sortBy([
                fn (Faq $a, Faq $b) => array_search($a->group, self::FAQ_GROUP_ORDER, true)
                    <=> array_search($b->group, self::FAQ_GROUP_ORDER, true),
                fn (Faq $a, Faq $b) => $a->position <=> $b->position,
            ])
            ->values();

        $this->seo()
            ->title('پرسش‌های متداول فنی')
            ->description('پاسخ کارشناسان به پرسش‌های رایج درباره بلوک سفالی، عایق‌کاری، اجرا و سفارش.')
            ->breadcrumbs([
                ['خانه', route('home')],
                ['مرکز فنی', route('technical.index')],
                ['پرسش‌های متداول', null],
            ])
            ->schema(Schema::faq($faqs));

        return view('pages.technical.faq', ['groups' => $faqs->groupBy('group')]);
    }
}
