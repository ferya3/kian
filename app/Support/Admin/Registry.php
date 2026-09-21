<?php

namespace App\Support\Admin;

use App\Admin\Resources\ArticleResource;
use App\Admin\Resources\CertificateResource;
use App\Admin\Resources\ContactMessageResource;
use App\Admin\Resources\DistributorResource;
use App\Admin\Resources\DocumentResource;
use App\Admin\Resources\FactorySectionResource;
use App\Admin\Resources\FaqResource;
use App\Admin\Resources\OfferResource;
use App\Admin\Resources\OrderResource;
use App\Admin\Resources\ProcessStepResource;
use App\Admin\Resources\ProductCategoryResource;
use App\Admin\Resources\ProductResource;
use App\Admin\Resources\ProjectCategoryResource;
use App\Admin\Resources\ProjectResource;
use App\Admin\Resources\SettingResource;
use App\Admin\Resources\SiteMediaResource;
use App\Admin\Resources\SolutionResource;
use App\Admin\Resources\StatResource;
use App\Admin\Resources\UserResource;
use App\Admin\Resources\VendorOrderResource;
use App\Admin\Resources\VendorResource;
use App\Support\Shop;
use Illuminate\Support\Collection;

/**
 * فهرست منابع پنل. ترتیب اینجا، ترتیب منو است.
 */
class Registry
{
    /** @return array<int, class-string<resource>> */
    public static function all(): array
    {
        $resources = [
            ProductResource::class,
            ProductCategoryResource::class,
            SolutionResource::class,
            ProjectResource::class,
            ProjectCategoryResource::class,
            DocumentResource::class,
            ArticleResource::class,
            FaqResource::class,
            ProcessStepResource::class,
            FactorySectionResource::class,
            StatResource::class,
            CertificateResource::class,
            SiteMediaResource::class,
            DistributorResource::class,
            ContactMessageResource::class,
            SettingResource::class,
            UserResource::class,
        ];

        /*
         * منابع فروشگاه فقط با کلیدِ روشن وجود دارند.
         *
         * اینجا از فهرست بیرون می‌مانند و نه در منو — یعنی با کلیدِ خاموش،
         * /admin/offers هم ۴۰۴ است و نه صفحه‌ای خالی. find و navigation هر
         * دو از همین فهرست می‌خوانند، پس یک شرط برای هر دو کافی است.
         */
        if (Shop::enabled()) {
            array_splice($resources, 13, 0, [
                VendorResource::class,
                OfferResource::class,
                OrderResource::class,
                VendorOrderResource::class,
            ]);
        }

        return $resources;
    }

    /** @return class-string<resource>|null */
    public static function find(string $slug): ?string
    {
        foreach (static::all() as $resource) {
            if ($resource::$slug === $slug) {
                return $resource;
            }
        }

        return null;
    }

    /** منابعِ در دسترس کاربر جاری، گروه‌بندی‌شده برای منو. */
    public static function navigation(): Collection
    {
        $isAdmin = auth()->user()?->isAdmin() ?? false;

        return collect(static::all())
            ->filter(fn (string $resource) => static::accessible($resource))
            ->reject(fn (string $resource) => $resource::$adminOnly && ! $isAdmin)
            ->reject(fn (string $resource) => ! $resource::$inNavigation)
            ->groupBy(fn (string $resource) => $resource::$group);
    }

    /**
     * آیا کاربر جاری این منبع را می‌بیند؟
     *
     * دو قاعده‌ی مستقل: منبعِ «فقط مدیر کل»، و منبعی که نقش‌های مجازش را
     * صریح اعلام کرده. فروشنده هیچ منبعی را نمی‌بیند مگر آنکه نامِ نقشش در
     * همان منبع آمده باشد — پیش‌فرض، بستن است و نه باز گذاشتن.
     */
    public static function accessible(string $resource): bool
    {
        $user = auth()->user();

        if ($resource::$adminOnly && ! ($user?->isAdmin() ?? false)) {
            return false;
        }

        if ($resource::$roles !== []) {
            return in_array($user?->role, $resource::$roles, true);
        }

        return ! ($user?->isVendor() ?? false);
    }
}
