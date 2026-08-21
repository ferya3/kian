<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * جمع‌کننده‌ی متادیتای سئو برای هر درخواست.
 * کنترلرها آن را پر می‌کنند و layout آن را رندر می‌کند.
 */
class Seo
{
    public ?string $title = null;

    public ?string $description = null;

    public ?string $canonical = null;

    public ?string $image = null;

    public string $type = 'website';

    public bool $noindex = false;

    /** @var array<int, array{label: string, url: ?string}> */
    public array $breadcrumbs = [];

    /** @var array<int, array<string, mixed>> */
    public array $schemas = [];

    public function title(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function description(?string $description): static
    {
        $this->description = $description ? Str::limit(strip_tags($description), 300, '') : null;

        return $this;
    }

    public function canonical(string $url): static
    {
        $this->canonical = $url;

        return $this;
    }

    public function image(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function noindex(bool $value = true): static
    {
        $this->noindex = $value;

        return $this;
    }

    /** @param array<int, array{0: string, 1?: ?string}> $items */
    public function breadcrumbs(array $items): static
    {
        $this->breadcrumbs = array_map(
            fn ($item) => ['label' => $item[0], 'url' => $item[1] ?? null],
            $items
        );

        return $this;
    }

    public function schema(array $schema): static
    {
        $this->schemas[] = $schema;

        return $this;
    }

    public function fullTitle(): string
    {
        $brand = config('kian.brand.name');

        if (! $this->title) {
            return $brand.' — '.config('kian.seo.default_title');
        }

        return Str::contains($this->title, $brand) ? $this->title : $this->title.' | '.$brand;
    }

    public function metaDescription(): string
    {
        return $this->description ?: config('kian.seo.default_description');
    }

    public function ogImage(): string
    {
        return url($this->image ?: config('kian.seo.og_image'));
    }

    public function canonicalUrl(): string
    {
        return $this->canonical ?: url()->current();
    }

    /** تمام بلوک‌های JSON-LD این صفحه، شامل Organization و BreadcrumbList. */
    public function jsonLd(): array
    {
        $graph = [Schema::organization()];

        if ($this->breadcrumbs) {
            $graph[] = Schema::breadcrumbs($this->breadcrumbs);
        }

        return array_merge($graph, $this->schemas);
    }
}
