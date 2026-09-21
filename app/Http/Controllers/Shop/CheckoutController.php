<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\Cart;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * تسویه — بدون درگاه پرداخت.
 *
 * سفارش ثبت می‌شود و فروشنده تماس می‌گیرد؛ تسویه بیرون از سایت است. این
 * تصمیمِ کسب‌وکار است و نه کمبودِ فنی: در مصالح ساختمانی مقدار، کرایه‌ی حمل
 * و زمان تحویل معمولاً پیش از پرداخت گفت‌وگو می‌شوند.
 */
class CheckoutController extends Controller
{
    public function show(Seo $seo)
    {
        $lines = Cart::lines();

        if ($lines->isEmpty()) {
            return redirect()->route('cart.show');
        }

        $seo->title('تکمیل سفارش')->noindex();

        return view('pages.shop.checkout', [
            'lines' => $lines,
            'total' => Cart::total(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_email' => ['nullable', 'email', 'max:160'],
            'province' => ['nullable', 'string', 'max:60'],
            'city' => ['nullable', 'string', 'max:60'],
            'address' => ['nullable', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        /*
         * سبد دوباره از دیتابیس خوانده می‌شود، نه از فرم.
         *
         * قیمت و موجودی ممکن است از لحظه‌ی دیدنِ سبد تا فشردنِ دکمه عوض شده
         * باشند. هرچه در سفارش می‌نشیند از همین خواندنِ تازه می‌آید.
         */
        $lines = Cart::lines();

        if ($lines->isEmpty()) {
            return redirect()->route('cart.show')
                ->withErrors(['cart' => 'سبد خرید خالی است.']);
        }

        /*
         * تراکنش، چون سفارش و ردیف‌هایش یک چیزند.
         *
         * سفارشِ بی‌ردیف بدتر از سفارشِ ثبت‌نشده است: در پنل دیده می‌شود،
         * کسی پیگیری‌اش می‌کند و چیزی در آن نیست.
         */
        $order = DB::transaction(function () use ($data, $lines) {
            $order = Order::create($data + [
                'number' => Order::nextNumber(),
                'token' => Str::random(48),
                'total' => $lines->sum('total'),
                'status' => 'new',
            ]);

            foreach ($lines as $line) {
                $offer = $line['offer'];

                $order->items()->create([
                    'vendor_id' => $offer->vendor_id,
                    'product_id' => $offer->product_id,
                    // اسنپ‌شات: فاکتور باید همان چیزی بماند که روز خرید بود
                    'product_name' => $offer->product->name,
                    'vendor_name' => $offer->vendor->name,
                    'unit_price' => $offer->price,
                    'quantity' => $line['quantity'],
                    'total' => $line['total'],
                    'status' => 'new',
                ]);
            }

            return $order;
        });

        Cart::clear();

        return redirect()->route('orders.show', $order);
    }

    public function done(Order $order, Seo $seo)
    {
        $seo->title('سفارش '.$order->number)->noindex();

        $order->load('items');

        return view('pages.shop.order', compact('order'));
    }
}
