<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Person;
use App\Models\Setting;

class BiographyController extends Controller
{
    public function __invoke()
    {
        $this->seo()
            ->title(__('site.seo.biography.title'))
            ->description(__('site.seo.biography.description'))
            ->breadcrumbs([
                [__('site.nav.home'), route('home')],
                [__('site.nav.items.about'), route('about')],
                [__('site.nav.items.biography'), null],
            ]);

        return view('pages.biography', [
            'lead' => Setting::text('biography_lead', __('site.biography.lead')),
            'milestones' => Milestone::query()->visible()->get(),
            'people' => Person::query()->visible()->get(),
        ]);
    }
}
