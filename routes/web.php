<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\SetLocale;
use App\Support\Locales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| صفحه‌های عمومی — زیر پیشوند زبان
|--------------------------------------------------------------------------
*/
Route::prefix('{locale}')
    ->where(['locale' => implode('|', Locales::codes())])
    ->middleware(SetLocale::class)
    ->group(base_path('routes/public.php'));

/*
| ریشه‌ی سایت زبانی ندارد، پس بازدیدکننده را به زبان خودش می‌فرستد: انتخاب
| قبلی‌اش، وگرنه Accept-Language مرورگر، وگرنه پیش‌فرض. ۳۰۲ و نه ۳۰۱، چون
| مقصدش به بازدیدکننده بستگی دارد و نباید کش شود.
*/
Route::get('/', fn () => redirect()->route('home', ['locale' => Locales::preferred()], 302))
    ->name('root');

/* سئو */
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| پنل مدیریت
|--------------------------------------------------------------------------
| همه‌ی مسیرها پشت احراز هویت‌اند و هدر noindex می‌گیرند. مسیرهای «فقط مدیر
| کل» جداگانه با پارامتر admin محافظت می‌شوند.
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuth::class, 'show'])->name('login');
    Route::post('login', [AdminAuth::class, 'login'])->middleware('throttle:20,1');

    Route::middleware('admin')->group(function () {
        Route::post('logout', [AdminAuth::class, 'logout'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');

        // پیش از مسیر عمومی {resource} تعریف می‌شود، وگرنه media یک منبع تلقی می‌شود
        Route::get('media', MediaController::class)
            ->middleware('admin:staff')
            ->name('media');

        Route::get('activity', ActivityLogController::class)
            ->middleware('admin:admin')
            ->name('activity');

        Route::get('{resource}', [ResourceController::class, 'index'])->name('resource.index');
        Route::get('{resource}/create', [ResourceController::class, 'create'])->name('resource.create');
        Route::post('{resource}', [ResourceController::class, 'store'])->name('resource.store');
        Route::get('{resource}/{id}/edit', [ResourceController::class, 'edit'])->name('resource.edit');
        Route::put('{resource}/{id}', [ResourceController::class, 'update'])->name('resource.update');
        Route::delete('{resource}/{id}', [ResourceController::class, 'destroy'])->name('resource.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| نشانی‌های بی‌پیشوند
|--------------------------------------------------------------------------
| پیش از چندزبانه‌شدن، صفحه‌ها بدون پیشوند بودند. هر نشانی قدیمی که هنوز
| جایی ذخیره شده باشد، با ۳۰۱ به همان صفحه در زبان پیش‌فرض می‌رود. اگر
| چنین صفحه‌ای هم نباشد، ۴۰۴ عادی.
*/
Route::fallback(function (Request $request) {
    $path = trim($request->path(), '/');
    $target = '/'.Locales::default().'/'.$path;

    /*
    | match() خودش به مسیرِ fallback می‌رسد و همه‌چیز را «پیدا» می‌کند، پس
    | باید صریح رد شود؛ وگرنه هر نشانی ناموجودی ۳۰۱ می‌گرفت به‌جای ۴۰۴.
    */
    try {
        $match = app('router')->getRoutes()->match(Request::create($target, 'GET'));
    } catch (Throwable) {
        abort(404);
    }

    if ($match->isFallback) {
        abort(404);
    }

    return redirect($target.($request->getQueryString() ? '?'.$request->getQueryString() : ''), 301);
});
