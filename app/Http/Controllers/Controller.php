<?php

namespace App\Http\Controllers;

use App\Support\Seo;

abstract class Controller
{
    protected function seo(): Seo
    {
        return app(Seo::class);
    }
}
