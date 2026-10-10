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
            // مدیرعامل بخشِ خودش را دارد و در کارت‌ها تکرار نمی‌شود
            'ceo' => $ceo = Person::query()->visible()->where('is_featured', true)->first(),
            'people' => Person::query()->visible()->when($ceo, fn ($q) => $q->whereKeyNot($ceo->id))->get(),
        ]);
    }
}
