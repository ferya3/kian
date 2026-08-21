<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Product;
use App\Models\Project;
use App\Models\Solution;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = collect();

        foreach ([
            ['home', 1.0, 'daily'],
            ['products.index', 0.9, 'weekly'],
            ['solutions.index', 0.7, 'monthly'],
            ['projects.index', 0.8, 'weekly'],
            ['technology', 0.7, 'monthly'],
            ['factory', 0.7, 'monthly'],
            ['sustainability', 0.6, 'monthly'],
            ['technical.index', 0.9, 'weekly'],
            ['technical.downloads', 0.8, 'weekly'],
            ['technical.installation', 0.7, 'monthly'],
            ['technical.certificates', 0.6, 'monthly'],
            ['technical.faq', 0.6, 'monthly'],
            ['articles.index', 0.7, 'weekly'],
            ['about', 0.6, 'yearly'],
            ['distributors', 0.6, 'monthly'],
            ['contact', 0.6, 'yearly'],
        ] as [$name, $priority, $freq]) {
            $urls->push(['loc' => route($name), 'priority' => $priority, 'changefreq' => $freq, 'lastmod' => now()]);
        }

        foreach (Product::query()->active()->get() as $product) {
            $urls->push(['loc' => route('products.show', $product), 'priority' => 0.9, 'changefreq' => 'monthly', 'lastmod' => $product->updated_at]);
        }

        foreach (Project::all() as $project) {
            $urls->push(['loc' => route('projects.show', $project), 'priority' => 0.6, 'changefreq' => 'yearly', 'lastmod' => $project->updated_at]);
        }

        foreach (Solution::all() as $solution) {
            $urls->push(['loc' => route('solutions.show', $solution), 'priority' => 0.7, 'changefreq' => 'monthly', 'lastmod' => $solution->updated_at]);
        }

        foreach (Article::query()->published()->get() as $article) {
            $urls->push(['loc' => route('articles.show', $article), 'priority' => 0.6, 'changefreq' => 'monthly', 'lastmod' => $article->updated_at]);
        }

        return response()
            ->view('sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /product-finder/results',
            'Disallow: /search',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode(PHP_EOL, $lines))->header('Content-Type', 'text/plain');
    }
}
