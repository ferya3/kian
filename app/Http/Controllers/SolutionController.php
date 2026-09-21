<?php

namespace App\Http\Controllers;

use App\Models\Solution;

class SolutionController extends Controller
{
    public function index()
    {
        $this->seo()
            ->title(__('site.seo.solutions.title'))
            ->description(__('site.seo.solutions.description'))
            ->breadcrumbs([[__('site.nav.home'), route('home')], [__('site.nav.items.solutions_index'), null]]);

        return view('pages.solutions.index', [
            'solutions' => Solution::query()->with('products')->orderBy('position')->get(),
        ]);
    }

    public function show(Solution $solution)
    {
        $solution->load('products.category');

        $this->seo()
            ->title($solution->title)
            ->description($solution->summary)
            ->image($solution->image)
            ->breadcrumbs([
                [__('site.nav.home'), route('home')],
                [__('site.nav.items.solutions_index'), route('solutions.index')],
                [$solution->title, null],
            ]);

        return view('pages.solutions.show', compact('solution'));
    }
}
