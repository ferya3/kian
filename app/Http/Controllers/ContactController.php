<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use App\Models\Distributor;
use App\Models\Product;
use App\Support\Schema;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show(Request $request)
    {
        $this->seo()
            ->title('تماس با ما و درخواست قیمت')
            ->description('درخواست پیش‌فاکتور، مشاوره فنی، همکاری به‌عنوان نماینده فروش یا بازدید از کارخانه.')
            ->breadcrumbs([['خانه', route('home')], ['تماس با ما', null]])
            ->schema(Schema::localBusiness());

        return view('pages.contact', [
            'products' => Product::query()->active()->orderBy('position')->get(['id', 'name']),
            'provinces' => Distributor::query()->distinct()->orderBy('province')->pluck('province'),
            'presetType' => $request->string('type')->toString() ?: 'quote',
            'presetProduct' => $request->integer('product') ?: null,
        ]);
    }

    public function store(ContactRequest $request)
    {
        $message = ContactMessage::create([
            ...$request->safe()->except('website'),
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('contact')
            ->with('success', "پیام شما ثبت شد. شماره پیگیری: {$message->id} — کارشناسان ما حداکثر تا یک روز کاری تماس می‌گیرند.");
    }
}
