<?php

namespace App\Support\Admin;

use App\Admin\Resources\ArticleResource;
use App\Admin\Resources\CertificateResource;
use App\Admin\Resources\ContactMessageResource;
use App\Admin\Resources\DistributorResource;
use App\Admin\Resources\DocumentResource;
use App\Admin\Resources\FactorySectionResource;
use App\Admin\Resources\FaqResource;
use App\Admin\Resources\ProcessStepResource;
use App\Admin\Resources\ProductCategoryResource;
use App\Admin\Resources\ProductResource;
use App\Admin\Resources\ProjectCategoryResource;
use App\Admin\Resources\ProjectResource;
use App\Admin\Resources\SettingResource;
use App\Admin\Resources\SolutionResource;
use App\Admin\Resources\StatResource;
use App\Admin\Resources\UserResource;
use Illuminate\Support\Collection;

/**
 * فهرست منابع پنل. ترتیب اینجا، ترتیب منو است.
 */
class Registry
{
    /** @return array<int, class-string<resource>> */
    public static function all(): array
    {
        return [
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
            DistributorResource::class,
            ContactMessageResource::class,
            SettingResource::class,
            UserResource::class,
        ];
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
            ->reject(fn (string $resource) => $resource::$adminOnly && ! $isAdmin)
            ->groupBy(fn (string $resource) => $resource::$group);
    }

    public static function accessible(string $resource): bool
    {
        return ! $resource::$adminOnly || (auth()->user()?->isAdmin() ?? false);
    }
}
