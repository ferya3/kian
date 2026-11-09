<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

/**
 * تولید داده‌های ساخت‌یافته‌ی Schema.org.
 * هر متد یک node مستقل برمی‌گرداند تا در @graph صفحه قرار بگیرد.
 */
class Schema
{
    public static function organization(): array
    {
        return static::compact([
            '@type' => 'Organization',
            '@id' => url('/#organization'),
            'name' => config('kian.brand.legal_name'),
            'alternateName' => config('kian.brand.name_en'),
            'url' => url('/'),
            'logo' => url('/logo.svg'),
            'foundingDate' => (string) config('kian.brand.founded'),
            'description' => config('kian.seo.default_description'),
            'address' => static::postalAddress(\App\Models\Location::primary()),

            /*
            | بقیه‌ی نشانی‌ها — دفتر فروش، انبار — هر کدام یک Place. نشانیِ
            | اصلی همان address است و اینجا تکرار نمی‌شود.
            */
            'location' => \App\Models\Location::listed()->slice(1)->map(fn ($l) => array_filter([
                '@type' => 'Place',
                'name' => $l->title,
                'address' => static::postalAddress($l),
                'telephone' => $l->phoneTel(),
            ]))->values()->all(),
            'contactPoint' => [
                [
                    '@type' => 'ContactPoint',
                    'telephone' => \App\Support\Contact::phoneTel(),
                    'contactType' => 'sales',
                    'areaServed' => 'IR',
                    'availableLanguage' => ['fa', 'en'],
                ],
                [
                    '@type' => 'ContactPoint',
                    'email' => \App\Support\Contact::get('technical_email'),
                    'contactType' => 'technical support',
                ],
            ],
            'sameAs' => array_values(\App\Support\Contact::social()),
        ]);
    }

    public static function localBusiness(): array
    {
        return static::compact([
            '@type' => ['LocalBusiness', 'Manufacturer'],
            '@id' => url('/#factory'),
            'name' => config('kian.brand.legal_name'),
            'image' => url('/og-image.svg'),
            'telephone' => \App\Support\Contact::phoneTel(),
            'email' => \App\Support\Contact::get('email'),
            'address' => static::postalAddress($primary = \App\Models\Location::primary()),
            'geo' => $primary?->hasPoint() ? [
                '@type' => 'GeoCoordinates',
                'latitude' => $primary->lat,
                'longitude' => $primary->lng,
            ] : null,
            'openingHours' => 'Sa-We 08:00-17:00',
        ]);
    }

    /** میدانِ خالی نوشته نمی‌شود: «"geo": null» برای گوگل یعنی داده‌ی خراب. */
    protected static function compact(array $data): array
    {
        return array_filter($data, fn ($v) => $v !== null && $v !== []);
    }

    /** نشانیِ پستیِ یک مکان؛ میدان‌های خالی اصلاً فرستاده نمی‌شوند. */
    protected static function postalAddress(?\App\Models\Location $location): ?array
    {
        if (! $location) {
            return null;
        }

        return array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $location->address,
            'addressLocality' => $location->city,
            'addressRegion' => $location->region,
            'postalCode' => $location->postal_code,
            'addressCountry' => 'IR',
        ]);
    }

    /**
     * محصول برای گوگل — فقط وقتی قیمت دارد.
     *
     * گوگل برای Product یکی از offers، review یا aggregateRating را الزامی
     * می‌داند و بی آن کلِ آیتم را «نامعتبر» گزارش می‌کند (Search Console:
     * «Either offers, review, or aggregateRating should be specified»). قیمت
     * از عرضه‌های فروشگاه می‌آید؛ محصولی که هنوز فروشنده‌ای رویش قیمت نگذاشته،
     * به‌جای آیتمِ نامعتبر، اصلاً به‌عنوان Product اعلام نمی‌شود — تا اولین
     * عرضه‌اش. نان‌ریزه‌ی صفحه سرِ جایش می‌ماند.
     */
    public static function product(Product $product): ?array
    {
        $offers = Shop::enabled()
            ? $product->offers()->sellable()->with('vendor')->orderBy('price')->get()
            : collect();

        if ($offers->isEmpty()) {
            return null;
        }

        return [
            '@type' => 'Product',
            '@id' => route('products.show', $product).'#product',
            'name' => $product->name,
            'sku' => $product->sku,
            'category' => $product->category?->name,
            'description' => $product->summary,
            // مسیرِ دیسک نه نشانی: url('admin/…') به ‎/admin/… می‌رسید، که وجود ندارد
            'image' => url(Media::original($product->hero_image) ?: '/og-image.svg'),
            'brand' => ['@type' => 'Brand', 'name' => config('kian.brand.name')],
            'manufacturer' => ['@id' => url('/#organization')],
            'material' => 'Fired clay / Terracotta',
            'width' => self::quantity($product->width_mm, 'MMT'),
            'height' => self::quantity($product->height_mm, 'MMT'),
            'depth' => self::quantity($product->length_mm, 'MMT'),
            'weight' => self::quantity($product->weight_kg, 'KGM'),
            'additionalProperty' => array_map(fn ($spec) => [
                '@type' => 'PropertyValue',
                'name' => $spec['label'],
                'value' => $spec['value'],
                'unitText' => $spec['unit'],
            ], $product->specSheet()),
            'offers' => self::offers($product, $offers),
        ];
    }

    /**
     * قیمت‌ها: یک عرضه ⇐ Offer، چند عرضه ⇐ AggregateOffer با کمترین و بیشترین.
     *
     * واحدِ پولِ schema.org باید کدِ ISO باشد و «تومان» کد ندارد؛ پس قیمت به
     * ریال (IRR) گزارش می‌شود — تومان × ۱۰.
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\Offer>  $offers
     */
    protected static function offers(Product $product, \Illuminate\Support\Collection $offers): array
    {
        $rial = fn (int $price) => $price * (config('shop.currency', 'تومان') === 'ریال' ? 1 : 10);
        $url = Route::has('shop.show') ? route('shop.show', $product) : route('products.show', $product);

        $single = fn ($offer) => array_filter([
            '@type' => 'Offer',
            'price' => $rial($offer->price),
            'priceCurrency' => 'IRR',
            'availability' => $offer->inStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
            'url' => $url,
            'seller' => $offer->vendor ? ['@type' => 'Organization', 'name' => $offer->vendor->name] : null,
            'eligibleQuantity' => $offer->min_order > 1
                ? ['@type' => 'QuantitativeValue', 'minValue' => $offer->min_order, 'unitText' => $offer->unit]
                : null,
        ], fn ($v) => $v !== null);

        if ($offers->count() === 1) {
            return $single($offers->first());
        }

        $prices = $offers->pluck('price');

        return [
            '@type' => 'AggregateOffer',
            'priceCurrency' => 'IRR',
            'lowPrice' => $rial($prices->min()),
            'highPrice' => $rial($prices->max()),
            'offerCount' => $offers->count(),
            'availability' => $offers->contains(fn ($o) => $o->inStock())
                ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => $url,
            'offers' => $offers->map($single)->values()->all(),
        ];
    }

    public static function article(Article $article): array
    {
        return [
            '@type' => 'Article',
            '@id' => route('articles.show', $article).'#article',
            'headline' => $article->title,
            'description' => $article->excerpt,
            'image' => url(Media::original($article->cover_image) ?: '/og-image.svg'),
            'datePublished' => $article->published_at?->toIso8601String(),
            'dateModified' => $article->updated_at?->toIso8601String(),
            'author' => ['@type' => 'Person', 'name' => $article->author ?: config('kian.brand.name')],
            'publisher' => ['@id' => url('/#organization')],
        ];
    }

    /** @param iterable<object{question: string, answer: string}> $faqs */
    public static function faq(iterable $faqs): array
    {
        return [
            '@type' => 'FAQPage',
            'mainEntity' => collect($faqs)->map(fn ($faq) => [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($faq->answer)],
            ])->values()->all(),
        ];
    }

    /** @param array<int, array{label: string, url: ?string}> $items */
    public static function breadcrumbs(array $items): array
    {
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn ($item, $i) => array_filter([
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['label'],
                'item' => $item['url'],
            ]))->all(),
        ];
    }

    protected static function quantity(float|int $value, string $unitCode): array
    {
        return ['@type' => 'QuantitativeValue', 'value' => $value, 'unitCode' => $unitCode];
    }
}
