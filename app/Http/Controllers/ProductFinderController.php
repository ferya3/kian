<?php

namespace App\Http\Controllers;

use App\Services\ProductFinder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductFinderController extends Controller
{
    public function __construct(protected ProductFinder $finder) {}

    public function show(Request $request)
    {
        $this->seo()
            ->title('انتخاب محصول مناسب پروژه')
            ->description('در چهار گام ساده، بلوک سفالی مناسب نوع پروژه، نوع دیوار، ضخامت و سطح عایق‌کاری مورد نیاز خود را پیدا کنید.')
            ->breadcrumbs([['خانه', route('home')], ['انتخاب محصول', null]]);

        return view('pages.finder', ['criteria' => $this->criteria($request)]);
    }

    public function results(Request $request)
    {
        $criteria = $this->criteria($request);
        $matches = $this->finder->search($criteria);

        $this->seo()
            ->title('نتیجه انتخاب محصول')
            ->description('محصولات پیشنهادی بر اساس مشخصات پروژه شما.')
            ->noindex()
            ->breadcrumbs([
                ['خانه', route('home')],
                ['انتخاب محصول', route('finder.show')],
                ['نتیجه', null],
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
            'project_type' => ['nullable', Rule::in(array_keys(config('kian.finder.project_types')))],
            'wall_type' => ['nullable', Rule::in(array_keys(config('kian.finder.wall_types')))],
            'thickness' => ['nullable', Rule::in(config('kian.finder.thicknesses'))],
            'insulation' => ['nullable', Rule::in(array_keys(config('kian.finder.insulation_levels')))],
        ]);

        return [
            'project_type' => $validated['project_type'] ?? null,
            'wall_type' => $validated['wall_type'] ?? null,
            'thickness' => isset($validated['thickness']) ? (int) $validated['thickness'] : null,
            'insulation' => $validated['insulation'] ?? null,
        ];
    }
}
