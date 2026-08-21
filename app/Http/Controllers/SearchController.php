<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Document;
use App\Models\Product;
use App\Models\Project;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $term = trim($request->string('q')->toString());

        $results = ['products' => collect(), 'projects' => collect(), 'documents' => collect(), 'articles' => collect()];

        if (mb_strlen($term) >= 2) {
            $like = "%{$term}%";

            $results['products'] = Product::query()->active()
                ->where(fn ($q) => $q->where('name', 'like', $like)
                    ->orWhere('name_en', 'like', $like)
                    ->orWhere('sku', 'like', $like)
                    ->orWhere('summary', 'like', $like))
                ->take(8)->get();

            $results['projects'] = Project::query()->with('category')
                ->where(fn ($q) => $q->where('title', 'like', $like)->orWhere('city', 'like', $like))
                ->take(6)->get();

            $results['documents'] = Document::query()
                ->where('title', 'like', $like)->take(6)->get();

            $results['articles'] = Article::query()->published()
                ->where(fn ($q) => $q->where('title', 'like', $like)->orWhere('excerpt', 'like', $like))
                ->take(6)->get();
        }

        $this->seo()
            ->title($term ? "جستجو: {$term}" : 'جستجو')
            ->noindex()
            ->breadcrumbs([['خانه', route('home')], ['جستجو', null]]);

        return view('pages.search', compact('term', 'results'));
    }
}
