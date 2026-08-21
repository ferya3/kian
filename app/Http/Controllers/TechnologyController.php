<?php

namespace App\Http\Controllers;

use App\Models\ProcessStep;
use App\Models\Stat;

class TechnologyController extends Controller
{
    public function __invoke()
    {
        $this->seo()
            ->title('از خاک تا سازه — فناوری تولید')
            ->description('نُه مرحله تولید بلوک سفالی، از استخراج خاک رس تا کنترل کیفیت و بسته‌بندی؛ با دمای پخت، زمان خشک‌کن و شاخص‌های هر مرحله.')
            ->breadcrumbs([['خانه', route('home')], ['فناوری', null]]);

        return view('pages.technology', [
            'steps' => ProcessStep::query()->orderBy('step_no')->get(),
            'stats' => Stat::query()->group('factory')->get(),
        ]);
    }
}
