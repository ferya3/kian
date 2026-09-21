<?php

namespace App\Http\Controllers;

use App\Services\ProductFinder;
use App\Support\Options;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductFinderController extends Controller
{
    public function __construct(protected ProductFinder $finder) {}

    public function show(Request $request)
    {
        $this->seo()
            ->title(__('site.seo.finder.title'))
            ->description(__('site.seo.finder.description'))
            ->breadcrumbs([[__('site.nav.home'), route('home')], [__('site.actions.finder'), null]]);

        return view('pages.finder', ['criteria' => $this->criteria($request)]);
    }

    public function results(Request $request)
    {
        $criteria = $this->criteria($request);
        $matches = $this->finder->search($criteria);

        $this->seo()
            ->title(__('site.seo.finder_results.title'))
            ->description(__('site.seo.finder_results.description'))
            ->noindex()
            ->breadcrumbs([
                [__('site.nav.home'), route('home')],
                [__('site.actions.finder'), route('finder.show')],
                [__('site.seo.crumb_result'), null],
            ]);

        $view = view('pages.finder-results', [
            'criteria' => $criteria,
            'labels' => $this->finder->criteriaLabels($criteria),
            'matches' => $matches,
        ]);

        // پاسخ جزئی برای فرم زنده‌ی صفحه اصلی (بدون بارگذاری مجدد صفحه)
        if ($request->boolean('partial') || $request->header('X-Partial')) {
            return response()->view('partials.finder-results', [
                'labels' => $this->finder->criteriaLabels($criteria),
                'matches' => $matches,
                'criteria' => $criteria,
            ]);
        }

        return $view;
    }

    /** @return array<string, mixed> */
    protected function criteria(Request $request): array
    {
        $validated = $request->validate([
            'project_type' => ['nullable', Rule::in(array_keys(Options::finder('project_types')))],
            'wall_type' => ['nullable', Rule::in(array_keys(Options::finder('wall_types')))],
            'thickness' => ['nullable', Rule::in(config('kian.finder.thicknesses'))],
            'insulation' => ['nullable', Rule::in(array_keys(Options::finder('insulation_levels')))],
        ]);

        return [
            'project_type' => $validated['project_type'] ?? null,
            'wall_type' => $validated['wall_type'] ?? null,
            'thickness' => isset($validated['thickness']) ? (int) $validated['thickness'] : null,
            'insulation' => $validated['insulation'] ?? null,
        ];
    }
}
