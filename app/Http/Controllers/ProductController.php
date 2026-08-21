<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\Schema;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = ProductCategory::query()->roots()->with('children')->get();

        $products = Product::query()
            ->active()
            ->with('category')
            ->when($request->string('category')->toString(), function ($query, string $slug) {
                $query->whereHas('category', fn ($q) => $q
                    ->where('slug', $slug)
                    ->orWhereHas('parent', fn ($p) => $p->where('slug', $slug)));
            })
            ->when($request->string('wall')->toString(), fn ($q, $wall) => $q->whereJsonContains('wall_types', $wall))
            ->when($request->integer('thickness'), fn ($q, $t) => $q->where('thickness_mm', $t * 10))
            ->orderBy('position')
            ->get();

        $this->seo()
            ->title('محصولات — بلوک سفالی و مصالح ساختمانی سرامیکی')
            ->description('کاتالوگ کامل بلوک‌های سفالی دیواری، تیغه‌ای، عایق و سقفی به‌همراه مشخصات فنی، ابعاد، مقاومت فشاری و ضریب هدایت حرارتی.')
            ->breadcrumbs([['خانه', route('home')], ['محصولات', null]]);

        return view('pages.products.index', compact('categories', 'products'));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load(['category.parent', 'cavities', 'documents', 'projects.category', 'solutions']);

        $related = Product::query()
            ->active()
            ->where('id', '!=', $product->id)
            ->where('product_category_id', $product->product_category_id)
            ->orderBy('position')
            ->take(3)
            ->get();

        if ($related->count() < 3) {
            $related = $related->concat(
                Product::query()->active()
                    ->whereNotIn('id', $related->pluck('id')->push($product->id))
                    ->orderBy('position')->take(3 - $related->count())->get()
            );
        }

        $this->seo()
            ->title($product->meta_title ?: $product->name.' — مشخصات فنی و دیتاشیت')
            ->description($product->meta_description ?: $product->summary)
            ->image($product->hero_image)
            ->type('product')
            ->breadcrumbs(array_values(array_filter([
                ['خانه', route('home')],
                ['محصولات', route('products.index')],
                $product->category ? [$product->category->name, route('products.index', ['category' => $product->category->slug])] : null,
                [$product->name, null],
            ])))
            ->schema(Schema::product($product));

        return view('pages.products.show', [
            'product' => $product,
            'related' => $related,
            'documentGroups' => $product->documents->groupBy('category'),
            'documentLabels' => Document::CATEGORIES,
        ]);
    }
}
