<?php

namespace App\Http\Controllers;

use App\Models\ProcessStep;
use App\Models\Stat;

class TechnologyController extends Controller
{
    public function __invoke()
    {
        $this->seo()
            ->title(__('site.seo.technology.title'))
            ->description(__('site.seo.technology.description'))
            ->breadcrumbs([[__('site.nav.home'), route('home')], [__('site.nav.items.technology'), null]]);

        return view('pages.technology', [
            'steps' => ProcessStep::query()->orderBy('step_no')->get(),
            'stats' => Stat::query()->group('factory')->get(),
        ]);
    }
}
