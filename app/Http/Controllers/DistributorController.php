<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use Illuminate\Http\Request;

class DistributorController extends Controller
{
    public function __invoke(Request $request)
    {
        $province = $request->string('province')->toString();

        $distributors = Distributor::query()
            ->when($province, fn ($q) => $q->where('province', $province))
            ->orderBy('position')
            ->orderBy('province')
            ->get();

        $this->seo()
            ->title(__('site.seo.distributors.title'))
            ->description(__('site.seo.distributors.description'))
            ->breadcrumbs([[__('site.nav.home'), route('home')], [__('site.nav.items.distributors'), null]]);

        return view('pages.distributors', [
            'distributors' => $distributors->groupBy('province'),
            'provinces' => Distributor::query()->distinct()->orderBy('province')->pluck('province'),
            'selected' => $province,
        ]);
    }
}
