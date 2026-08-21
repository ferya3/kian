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
            ->title('کارخانه — خط تولید، کوره و آزمایشگاه')
            ->description('نمای کامل کارخانه: خط تولید، کوره تونلی، خشک‌کن، آزمایشگاه کنترل کیفیت، انبار و بسته‌بندی.')
            ->breadcrumbs([['خانه', route('home')], ['کارخانه', null]]);

        return view('pages.factory', [
            'sections' => FactorySection::query()->orderBy('position')->get(),
            'stats' => Stat::query()->group('factory')->get(),
            'certificates' => Certificate::query()->orderBy('position')->get(),
        ]);
    }
}
