<?php

namespace App\Http\Controllers;

use App\Models\Solution;

class SolutionController extends Controller
{
    public function index()
    {
        $this->seo()
            ->title('راهکارهای ساختمانی')
            ->description('راهکارهای دیوار خارجی، جداکننده داخلی، سقف و عایق‌کاری حرارتی با سیستم‌های بلوک سفالی.')
            ->breadcrumbs([['خانه', route('home')], ['راهکارها', null]]);

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
                ['خانه', route('home')],
                ['راهکارها', route('solutions.index')],
                [$solution->title, null],
            ]);

        return view('pages.solutions.show', compact('solution'));
    }
}
