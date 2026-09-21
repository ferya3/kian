<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Support\Cart;
use App\Support\Seo;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Seo $seo)
    {
        $seo->title('سبد خرید')->noindex();

        return view('pages.shop.cart', [
            'lines' => Cart::lines(),
            'total' => Cart::total(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'offer' => ['required', 'integer', 'exists:offers,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.config('shop.max_quantity', 9999)],
        ]);

        /*
         * عرضه از مسیر sellable خوانده می‌شود و نه با find.
         *
         * exists در اعتبارسنجی فقط می‌گوید ردیف هست؛ نمی‌گوید فروشنده‌اش
         * فعال است یا محصولش هنوز منتشر. بدون این، می‌شد با یک id قدیمی
         * چیزی را به سبد افزود که ویترین اصلاً نشانش نمی‌دهد.
         */
        $offer = Offer::query()->sellable()->findOrFail($data['offer']);

        Cart::put($offer, (int) ($data['quantity'] ?? $offer->min_order));

        return back()->with('status', '«'.$offer->product->name.'» به سبد اضافه شد.');
    }

    public function update(Request $request, Offer $offer)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:'.config('shop.max_quantity', 9999)],
        ]);

        Cart::put($offer, (int) $data['quantity']);

        return back();
    }

    public function destroy(Offer $offer)
    {
        Cart::forget($offer->id);

        return back()->with('status', 'ردیف از سبد برداشته شد.');
    }
}
