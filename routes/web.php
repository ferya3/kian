<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductFinderController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SolutionController;
use App\Http\Controllers\SustainabilityController;
use App\Http\Controllers\TechnicalController;
use App\Http\Controllers\TechnologyController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

/*
| محصولات — هر محصول URL مستقل و پایدار دارد: /products/ceramic-block-20
*/
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

/* موتور انتخاب محصول */
Route::get('/product-finder', [ProductFinderController::class, 'show'])->name('finder.show');
Route::get('/product-finder/results', [ProductFinderController::class, 'results'])->name('finder.results');

/* راهکارها */
Route::get('/solutions', [SolutionController::class, 'index'])->name('solutions.index');
Route::get('/solutions/{solution}', [SolutionController::class, 'show'])->name('solutions.show');

/* پروژه‌ها */
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

/* فناوری و کارخانه */
Route::get('/technology', TechnologyController::class)->name('technology');
Route::get('/factory', FactoryController::class)->name('factory');
Route::get('/sustainability', SustainabilityController::class)->name('sustainability');

/* مرکز فنی — مهندس، معمار، پیمانکار */
Route::get('/technical', [TechnicalController::class, 'index'])->name('technical.index');
Route::get('/technical/downloads', [TechnicalController::class, 'downloads'])->name('technical.downloads');
Route::get('/technical/installation', [TechnicalController::class, 'installation'])->name('technical.installation');
Route::get('/technical/certificates', [TechnicalController::class, 'certificates'])->name('technical.certificates');
Route::get('/technical/faq', [TechnicalController::class, 'faq'])->name('technical.faq');
Route::get('/downloads/{document}', [DocumentController::class, 'download'])->name('documents.download');

/* دانش فنی */
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

/* شرکت */
Route::get('/about', AboutController::class)->name('about');
Route::get('/distributors', DistributorController::class)->name('distributors');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('contact.store');

Route::get('/search', SearchController::class)->name('search');

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
        Route::get('media', MediaController::class)->name('media');

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
