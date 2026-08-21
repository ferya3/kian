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
        // فقط وقتی سایت واقعاً روی https سرو می‌شود؛ وگرنه نصب بدون SSL
        // لینک‌های https تولید می‌کند که باز نمی‌شوند.
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Paginator::defaultView('vendor.pagination.kian');

        // مِگا منوی «محصولات» روی همه‌ی صفحات رندر می‌شود.
        View::composer(['partials.header', 'partials.mega-menu', 'partials.mobile-nav', 'partials.footer'], function ($view) {
            $view->with('navigation', Navigation::items());
            $view->with('megaMenu', $this->megaMenu());
        });

        // به‌جای View::share در boot — تا زیر Octane نمونه‌ی درخواست قبلی باقی نماند.
        View::composer(['components.layouts.app', 'components.breadcrumbs'], function ($view) {
            $view->with('seo', $this->app->make(Seo::class));
        });
    }

    /** @return Collection<int, ProductCategory> */
    protected function megaMenu()
    {
        return once(function () {
            // پیش از اجرای مهاجرت‌ها (مثلاً هنگام migrate:fresh) جدول وجود ندارد.
            if (! Schema::hasTable('product_categories')) {
                return collect();
            }

            return ProductCategory::query()
                ->roots()
                ->with(['children' => fn ($q) => $q->withCount('products')])
                ->get();
        });
    }
}
