<?php

namespace App\Http\Controllers;

use App\Models\Stat;

class SustainabilityController extends Controller
{
    public function __invoke()
    {
        $this->seo()
            ->title('پایداری — از خاک به ساختمان و بازگشت به خاک')
            ->description('سفال یک ماده‌ی معدنی، بی‌اثر و بازیافت‌پذیر است. عملکرد چرخه‌ی عمر، بازیابی حرارت کوره و مدیریت ضایعات در کارخانه.')
            ->breadcrumbs([['خانه', route('home')], ['پایداری', null]]);

        return view('pages.sustainability', [
            'stats' => Stat::query()->group('sustainability')->get(),
        ]);
    }
}
