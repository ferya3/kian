<?php

namespace App\Http\Controllers;

use App\Models\FactorySection;
use App\Models\Product;
use App\Models\ProcessStep;
use App\Models\Project;
use App\Models\Solution;
use App\Models\Stat;
use App\Support\Schema;

class HomeController extends Controller
{
    public function __invoke()
    {
        $this->seo()
            ->title(config('kian.brand.name').' — '.config('kian.seo.default_title'))
            ->description(config('kian.seo.default_description'))
            ->schema(Schema::localBusiness());

        return view('pages.home', [
            'featuredProducts' => Product::query()->active()->featured()
                ->with('category')->orderBy('position')->take(4)->get(),
            'processSteps' => ProcessStep::query()->orderBy('step_no')->get(),
            'factorySections' => FactorySection::query()->orderBy('position')->get(),
            'stats' => Stat::query()->group('factory')->get(),
            'projects' => Project::query()->with('category')->featured()
                ->orderBy('position')->take(6)->get(),
            'solutions' => Solution::query()->orderBy('position')->take(4)->get(),
            'interactiveProduct' => Product::query()->active()->with('cavities')
                ->orderByDesc('thermal_score')->first(),
        ]);
    }
}
