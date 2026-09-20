<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Product;
use App\Models\Project;
use App\Models\Solution;
use App\Support\Locales;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = collect();

        /*
        | هر نشانی به‌ازای هر زبان یک ردیف می‌شود، و هر ردیف alternate همه‌ی
        | زبان‌ها را با خود می‌برد — همان چیزی که گوگل برای فهمیدنِ «این‌ها یک
        | صفحه‌اند به سه زبان» می‌خواهد.
        */
        $push = function (string $name, array $params, float $priority, string $freq, $lastmod) use ($urls) {
            $alternates = collect(Locales::codes())
                ->mapWithKeys(fn (string $code) => [$code => route($name, $params + ['locale' => $code])])
                ->all();

            foreach ($alternates as $code => $loc) {
                $urls->push([
                    'loc' => $loc,
                    'priority' => $priority,
                    'changefreq' => $freq,
                    'lastmod' => $lastmod,
                    'alternates' => $alternates,
                ]);
            }
        };

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
            $push($name, [], $priority, $freq, now());
        }

        foreach (Product::query()->active()->get() as $product) {
            $push('products.show', [$product], 0.9, 'monthly', $product->updated_at);
        }

        foreach (Project::all() as $project) {
            $push('projects.show', [$project], 0.6, 'yearly', $project->updated_at);
        }

        foreach (Solution::all() as $solution) {
            $push('solutions.show', [$solution], 0.7, 'monthly', $solution->updated_at);
        }

        foreach (Article::query()->published()->get() as $article) {
            $push('articles.show', [$article], 0.6, 'monthly', $article->updated_at);
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
