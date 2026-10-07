<?php

namespace App\Providers;

use App\Models\ProductCategory;
use App\Support\Locales;
use App\Support\Navigation;
use App\Support\Seo;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
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
        $this->trustProxies();

        // فقط وقتی سایت واقعاً روی https سرو می‌شود؛ وگرنه نصب بدون SSL
        // لینک‌های https تولید می‌کند که باز نمی‌شوند.
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        /*
        | پارامتر زبان برای هر route() پر می‌شود، حتی بیرون از گروهِ زبان.
        |
        | SetLocale همین را برای صفحه‌های عمومی با زبان درخواست بازنویسی
        | می‌کند؛ این مقدار برای بقیه است: پنل مدیریت، نقشه‌ی سایت، صفحه‌ی
        | خطا. بدون آن، هر route('home') بیرون از گروه استثنا می‌داد.
        */
        URL::defaults(['locale' => Locales::default()]);

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

    /**
     * اعلامِ اعتماد به پراکسی — دلیلش در config/proxies.php نوشته شده.
     *
     * اینجا و نه در bootstrap/app.php: آن پرونده پیش از بارگذاریِ پیکربندی
     * اجرا می‌شود، پس config() آنجا خالی است و با config:cache هم env()
     * دیگر خوانده نمی‌شود. boot دیر اجرا می‌شود و هر دو را دارد.
     *
     * فهرستِ خالی یعنی هیچ تغییری — سروری که مستقیم روی آی‌پی سرو می‌شود
     * نباید به سربرگی اعتماد کند که هر کسی می‌تواند جعلش کند.
     */
    protected function trustProxies(): void
    {
        $proxies = config('proxies.trusted');

        if ($proxies === [] || $proxies === null) {
            return;
        }

        TrustProxies::at($proxies);

        /*
        | Forwarded (RFC 7239) عمداً نیست: کلودفلر آن را نمی‌فرستد و
        | پذیرفتنش فقط یک سربرگِ دیگر است که باید به آن اعتماد کرد.
        */
        TrustProxies::withHeaders(
            Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO
        );
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
