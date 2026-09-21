<?php

namespace App\Http\Controllers;

use App\Models\Stat;

class SustainabilityController extends Controller
{
    public function __invoke()
    {
        $this->seo()
            ->title(__('site.seo.sustainability.title'))
            ->description(__('site.seo.sustainability.description'))
            ->breadcrumbs([[__('site.nav.home'), route('home')], [__('site.nav.items.sustainability'), null]]);

        return view('pages.sustainability', [
            'stats' => Stat::query()->group('sustainability')->get(),
        ]);
    }
}
