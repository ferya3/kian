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
            ->title('درباره ما')
            ->description('بیش از دو دهه تولید بلوک سفالی مهندسی‌شده؛ از یک کوره‌ی سنتی تا خط تولید تمام‌اتوماتیک با ظرفیت سالانه صد و بیست هزار تن.')
            ->breadcrumbs([['خانه', route('home')], ['درباره ما', null]]);

        return view('pages.about', [
            'stats' => Stat::query()->group('factory')->get(),
            'certificates' => Certificate::query()->orderBy('position')->get(),
            'steps' => ProcessStep::query()->orderBy('step_no')->take(4)->get(),
        ]);
    }
}
