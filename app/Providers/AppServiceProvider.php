<?php

namespace App\Providers;

use App\Models\ProductCategory;
use App\Support\Navigation;
use App\Support\Seo;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(Seo::class, fn () => new Seo);
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Paginator::defaultView('vendor.pagination.kian');

        // مِگا منوی «محصولات» روی همه‌ی صفحات رندر می‌شود.
        View::composer(['components.layouts.app', 'partials.header', 'partials.mega-menu', 'partials.mobile-nav', 'partials.footer'], function ($view) {
            $view->with('navigation', Navigation::items());
            $view->with('megaMenu', $this->megaMenu());
        });

        View::share('seo', $this->app->make(Seo::class));
    }

    /** @return Collection<int, ProductCategory> */
    protected function megaMenu()
    {
        if (! Schema::hasTable('product_categories')) {
            return collect();
        }

        return once(fn () => ProductCategory::query()
            ->roots()
            ->with(['children' => fn ($q) => $q->withCount('products')])
            ->get());
    }
}
