<?php

namespace App\Models;

use App\Support\Locales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

/**
 * ریدایرکتِ یک نشانیِ قدیم.
 *
 * سایتِ قبلی روی همین دامنه وردپرس بود و گوگل نشانی‌هایش را ایندکس کرده؛
 * بی ریدایرکت، همه‌شان ۴۰۴ می‌شوند و اعتبارشان در جستجو از دست می‌رود.
 */
class Redirect extends Model
{
    protected $guarded = [];

    public const CACHE_KEY = 'redirects.map';

    /** بیشترین تعدادِ ۴۰۴ ِ بی‌مقصدی که ثبت می‌شود — جلوی پرشدنِ جدول با اسکنِ ربات‌ها. */
    public const MAX_LOGGED = 3000;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'status_code' => 'integer',
            'hits' => 'integer',
            'last_hit_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /**
     * شکلِ یکسانِ یک نشانی برای مقایسه.
     *
     * وردپرس نامک‌های فارسی را درصدی‌رمز می‌کند و گوگل گاهی با اسلشِ پایانی
     * و گاهی بی آن ایندکس می‌کند؛ همه باید به یک کلید برسند. پارامترها
     * (utm و…) فقط روی ریشه نگه داشته می‌شوند، چون آنجا خودِ نشانی‌اند:
     * ‎/?p=123 و ‎/?page_id=5 صفحه‌های جدای وردپرس بودند.
     */
    public static function normalize(string $url): string
    {
        $url = trim($url);

        if (preg_match('~^https?://~i', $url) || str_starts_with($url, '//')) {
            $parts = parse_url(str_starts_with($url, '//') ? 'http:'.$url : $url) ?: [];
            $path = $parts['path'] ?? '/';
            $query = $parts['query'] ?? '';
        } else {
            [$path, $query] = array_pad(explode('?', $url, 2), 2, '');
        }

        $path = rawurldecode($path);
        $path = '/'.trim(preg_replace('~/+~', '/', $path), '/');
        $path = Str::lower($path);

        if ($path === '/' && $query !== '') {
            parse_str($query, $params);
            $params = array_filter($params, fn ($k) => ! str_starts_with((string) $k, 'utm_') && $k !== 'fbclid' && $k !== 'gclid', ARRAY_FILTER_USE_KEY);
            ksort($params);

            return $params ? '/?'.urldecode(http_build_query($params)) : '/';
        }

        return Str::limit($path, 690, '');
    }

    /**
     * نقشه‌ی ریدایرکت‌های فعال: from_path => [id, to, status].
     *
     * روی هر درخواست خوانده می‌شود، پس کش می‌شود و با هر ذخیره پاک. پیش از
     * migrate (نصبِ تازه) جدول نیست و نقشه خالی است، نه خطا.
     *
     * @return array<string, array{0:int, 1:?string, 2:int}>
     */
    public static function map(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()
                ->where('is_active', true)
                ->where(fn ($q) => $q->whereNotNull('to_url')->orWhere('status_code', 410))
                ->get(['id', 'from_path', 'to_url', 'status_code'])
                ->mapWithKeys(fn (self $r) => [$r->from_path => [$r->id, $r->to_url, $r->status_code]])
                ->all());
        } catch (Throwable) {
            return [];
        }
    }

    /** یک ۴۰۴ را می‌شمرد، تا مدیر ببیند کدام نشانی‌های قدیم هنوز بازدید دارند. */
    public static function record404(string $key): void
    {
        try {
            $updated = static::query()->where('from_path', $key)
                ->update(['hits' => \DB::raw('hits + 1'), 'last_hit_at' => now()]);

            if (! $updated && static::query()->whereNull('to_url')->count() < self::MAX_LOGGED) {
                static::query()->insertOrIgnore([
                    'from_path' => $key, 'source' => '404', 'status_code' => 301,
                    'hits' => 1, 'last_hit_at' => now(), 'is_active' => true,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        } catch (Throwable) {
            // شمارش نباید هرگز صفحه را بشکند
        }
    }

    /**
     * حدسِ مقصد از روی خودِ نشانیِ قدیم.
     *
     * فقط پیشنهاد است و در یادداشت علامت می‌خورد؛ مدیر در پنل اصلاحش می‌کند.
     * نشانیِ نسبی برمی‌گردد تا با عوض‌شدنِ دامنه کهنه نشود.
     */
    public static function guess(string $key): string
    {
        $k = Str::lower(rawurldecode($key));
        $route = fn (string $name, array $p = []) => route($name, ['locale' => Locales::default()] + $p, false);
        $has = fn (string $pattern) => (bool) preg_match('~'.$pattern.'~u', $k);

        // محصولِ مشخص: «بلوک ۲۰» ⇐ همان سایز، اگر در کاتالوگ باشد
        if ($has('block|blok|بلوک|سفال|product|محصول|tile')) {
            $digits = strtr($k, ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9']);
            if (preg_match_all('~(?<!\d)(\d{1,2})(?!\d)~', $digits, $m)) {
                foreach (Product::query()->where('is_active', true)->pluck('slug') as $slug) {
                    if (preg_match('~-(\d+)$~', $slug, $n) && in_array($n[1], $m[1], true)) {
                        return $route('products.show', ['product' => $slug]);
                    }
                }
            }

            return $route('products.index');
        }

        return match (true) {
            $has('contact|تماس|ارتباط') => $route('contact'),
            $has('about|درباره|history|تاریخچه|biograph|بیوگرافی') => $route('about'),
            $has('project|پروژه|portfolio|نمونه[-\s]?کار|gallery|گالری') => $route('projects.index'),
            $has('catalog|کاتالوگ|download|دانلود|brochure') => $route('technical.downloads'),
            $has('certif|گواهی|استاندارد|standard') => $route('technical.certificates'),
            $has('faq|سوال|سؤال|پرسش') => $route('technical.faq'),
            $has('factory|کارخانه|production|تولید') => $route('factory'),
            $has('agent|نمایندگی|distribut|dealer') => $route('distributors'),
            $has('shop|فروشگاه|cart|سبد|price|قیمت|order|سفارش') => $route('products.index'),
            $has('blog|news|article|مقاله|اخبار|خبر|^/category/|^/tag/|^/\d{4}/\d{2}|\?p=|\?cat=') => $route('articles.index'),
            default => $route('home'),
        };
    }
}
