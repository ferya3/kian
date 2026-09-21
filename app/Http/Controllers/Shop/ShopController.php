<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Product;
use App\Support\Seo;

/**
 * ویترین فروشگاه.
 *
 * محصول همان محصولِ کاتالوگ است — فروشگاه کاتالوگ دومی نمی‌سازد. تنها چیزی
 * که اضافه می‌کند، عرضه‌های هر محصول است: چه کسی، به چه قیمتی، با چه
 * حداقلی می‌فروشد.
 */
class ShopController extends Controller
{
    public function index(Seo $seo)
    {
        $seo->title('فروشگاه')
            ->description('خرید مستقیم بلوک سفالی از فروشندگان — مقایسه‌ی قیمت چند فروشنده برای هر محصول.');

        /*
         * فقط محصولی که دست‌کم یک عرضه‌ی قابل فروش دارد.
         *
         * محصولِ بی‌قیمت در ویترین یعنی صفحه‌ای که هیچ دکمه‌ای ندارد؛ جایش
         * در کاتالوگ فنی است و همان‌جا هم هست.
         */
        $products = Product::query()
            ->active()
            ->whereHas('offers', fn ($q) => $q->sellable())
            ->withMin(['offers as best_price' => fn ($q) => $q->sellable()], 'price')
            ->withCount(['offers as vendors_count' => fn ($q) => $q->sellable()])
            ->with('category')
            ->orderBy('position')
            ->paginate(12)
            ->withQueryString();

        return view('pages.shop.index', compact('products'));
    }

    public function show(Product $product, Seo $seo)
    {
        abort_unless($product->is_active, 404);

        $offers = Offer::query()
            ->sellable()
            ->where('product_id', $product->id)
            ->with('vendor')
            ->orderBy('price')
            ->get();

        // محصولی که هیچ فروشنده‌ای ندارد، صفحه‌ی فروشگاهی هم ندارد
        abort_if($offers->isEmpty(), 404);

        $seo->title($product->name.' — خرید')
            ->description('قیمت و شرایط فروش «'.$product->name.'» نزد '.$offers->count().' فروشنده.');

        return view('pages.shop.show', compact('product', 'offers'));
    }
}
