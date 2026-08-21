<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Product;

/**
 * تولید داده‌های ساخت‌یافته‌ی Schema.org.
 * هر متد یک node مستقل برمی‌گرداند تا در @graph صفحه قرار بگیرد.
 */
class Schema
{
    public static function organization(): array
    {
        return [
            '@type' => 'Organization',
            '@id' => url('/#organization'),
            'name' => config('kian.brand.legal_name'),
            'alternateName' => config('kian.brand.name_en'),
            'url' => url('/'),
            'logo' => url('/logo.svg'),
            'foundingDate' => (string) config('kian.brand.founded'),
            'description' => config('kian.seo.default_description'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => config('kian.contact.address'),
                'addressLocality' => config('kian.contact.address_locality'),
                'addressRegion' => config('kian.contact.address_region'),
                'postalCode' => config('kian.contact.postal_code'),
                'addressCountry' => 'IR',
            ],
            'contactPoint' => [
                [
                    '@type' => 'ContactPoint',
                    'telephone' => config('kian.contact.phone_raw'),
                    'contactType' => 'sales',
                    'areaServed' => 'IR',
                    'availableLanguage' => ['fa', 'en'],
                ],
                [
                    '@type' => 'ContactPoint',
                    'email' => config('kian.contact.technical_email'),
                    'contactType' => 'technical support',
                ],
            ],
            'sameAs' => array_values(array_filter(config('kian.social'))),
        ];
    }

    public static function localBusiness(): array
    {
        return [
            '@type' => ['LocalBusiness', 'Manufacturer'],
            '@id' => url('/#factory'),
            'name' => config('kian.brand.legal_name'),
            'image' => url('/og-image.svg'),
            'telephone' => config('kian.contact.phone_raw'),
            'email' => config('kian.contact.email'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => config('kian.contact.address'),
                'addressLocality' => config('kian.contact.address_locality'),
                'addressCountry' => 'IR',
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => config('kian.contact.lat'),
                'longitude' => config('kian.contact.lng'),
            ],
            'openingHours' => 'Sa-We 08:00-17:00',
        ];
    }

    public static function product(Product $product): array
    {
        return [
            '@type' => 'Product',
            '@id' => route('products.show', $product).'#product',
            'name' => $product->name,
            'sku' => $product->sku,
            'category' => $product->category?->name,
            'description' => $product->summary,
            'image' => url($product->hero_image ?: '/og-image.svg'),
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
        ];
    }

    public static function article(Article $article): array
    {
        return [
            '@type' => 'Article',
            '@id' => route('articles.show', $article).'#article',
            'headline' => $article->title,
            'description' => $article->excerpt,
            'image' => url($article->cover_image ?: '/og-image.svg'),
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
