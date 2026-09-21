<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\FactorySection;
use App\Models\Stat;

class FactoryController extends Controller
{
    public function __invoke()
    {
        $this->seo()
            ->title(__('site.seo.factory.title'))
            ->description(__('site.seo.factory.description'))
            ->breadcrumbs([[__('site.nav.home'), route('home')], [__('site.nav.items.factory'), null]]);

        return view('pages.factory', [
            'sections' => FactorySection::query()->orderBy('position')->get(),
            'stats' => Stat::query()->group('factory')->get(),
            'certificates' => Certificate::query()->orderBy('position')->get(),
        ]);
    }
}
