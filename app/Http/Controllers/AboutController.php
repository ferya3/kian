<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\ProcessStep;
use App\Models\Stat;

class AboutController extends Controller
{
    public function __invoke()
    {
        $this->seo()
            ->title(__('site.seo.about.title'))
            ->description(__('site.seo.about.description'))
            ->breadcrumbs([[__('site.nav.home'), route('home')], [__('site.nav.items.about'), null]]);

        return view('pages.about', [
            'stats' => Stat::query()->group('factory')->get(),
            'certificates' => Certificate::query()->orderBy('position')->get(),
            'steps' => ProcessStep::query()->orderBy('step_no')->take(4)->get(),
        ]);
    }
}
