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
            ->title(__('site.seo.technical.title'))
            ->description(__('site.seo.technical.description'))
            ->breadcrumbs([[__('site.nav.home'), route('home')], [__('site.nav.sub.technical_index'), null]]);

        return view('pages.technical.index', [
            'groups' => Document::query()->orderBy('position')->get()->groupBy('category'),
            'labels' => Document::categories(),
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
            ->title(__('site.seo.downloads.title'))
            ->description(__('site.seo.downloads.description'))
            ->breadcrumbs([
                [__('site.nav.home'), route('home')],
                [__('site.nav.sub.technical_index'), route('technical.index')],
                [__('site.seo.crumb_downloads'), null],
            ]);

        return view('pages.technical.downloads', [
            'documents' => $documents,
            'labels' => Document::categories(),
            'audiences' => Document::audiences(),
            'formats' => Document::query()->distinct()->orderBy('format')->pluck('format'),
        ]);
    }

    public function installation()
    {
        $this->seo()
            ->title(__('site.seo.installation.title'))
            ->description(__('site.seo.installation.description'))
            ->breadcrumbs([
                [__('site.nav.home'), route('home')],
                [__('site.nav.sub.technical_index'), route('technical.index')],
                [__('site.nav.items.technical_installation'), null],
            ]);

        return view('pages.technical.installation', [
            'guides' => Document::query()->category('installation')->orderBy('position')->get(),
            'faqs' => Faq::query()->where('group', 'installation')->orderBy('position')->get(),
        ]);
    }

    public function certificates()
    {
        $this->seo()
            ->title(__('site.seo.certificates.title'))
            ->description(__('site.seo.certificates.description'))
            ->breadcrumbs([
                [__('site.nav.home'), route('home')],
                [__('site.nav.sub.technical_index'), route('technical.index')],
                [__('site.footer.certificates'), null],
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
            ->title(__('site.seo.faq.title'))
            ->description(__('site.seo.faq.description'))
            ->breadcrumbs([
                [__('site.nav.home'), route('home')],
                [__('site.nav.sub.technical_index'), route('technical.index')],
                [__('site.nav.items.technical_faq'), null],
            ])
            ->schema(Schema::faq($faqs));

        return view('pages.technical.faq', ['groups' => $faqs->groupBy('group')]);
    }
}
