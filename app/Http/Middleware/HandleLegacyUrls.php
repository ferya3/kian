<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * نشانی‌های سایتِ قدیم (وردپرس) — پیش از مسیریابی.
 *
 * ۱. ریدایرکتی که مدیر در پنل گذاشته، اول از همه.
 * ۲. نشانی‌های فنیِ وردپرس (wp-admin، feed، wp-content…) که در سایتِ تازه
 *    هیچ‌وقت وجود نخواهند داشت: ۴۱۰. به گوگل می‌گوید «برای همیشه رفته» و
 *    زودتر از ۴۰۴ از نتایج بیرونشان می‌برد.
 * ۳. هر ۴۰۴ ِ دیگر شمرده می‌شود و در پنل، با تعدادِ بازدید، منتظرِ مقصد می‌ماند.
 */
class HandleLegacyUrls
{
    /** ساختارِ خودِ وردپرس. */
    protected const WORDPRESS = '~^/(wp-admin|wp-includes|wp-content|wp-json|wp-login\.php|wp-cron\.php|xmlrpc\.php|wp-signup\.php|wp-trackback\.php|wp-comments-post\.php|feed|comments/feed)(/|$)|/feed$|/trackback$|/amp$~';

    /** آنچه ثبت‌کردنش فقط جدول را پر می‌کند: پنل، فایل‌های سایت، اسکنِ ربات‌ها. */
    protected const NOISE = '~^/(admin|build|storage|livewire|vendor|up)(/|$)|/\.|\.(php\d?|aspx?|jsp|cgi|env|ini|sql|bak|old|zip|tar|gz|log|ya?ml|xml|txt|js|css|map|ico|png|jpe?g|gif|webp|svg|woff2?)$~i';

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return $next($request);
        }

        $key = Redirect::normalize($request->getRequestUri());
        $map = Redirect::map();

        if ($hit = $map[$key] ?? null) {
            [$id, $to, $status] = $hit;
            Redirect::query()->whereKey($id)->update(['hits' => \DB::raw('hits + 1'), 'last_hit_at' => now()]);

            if ($status === 410 || ! $to) {
                return $this->gone();
            }

            return redirect()->to($to, in_array($status, [301, 302, 307, 308], true) ? $status : 301);
        }

        if (preg_match(self::WORDPRESS, $key)) {
            return $this->gone();
        }

        $response = $next($request);

        if ($response->getStatusCode() === 404 && ! preg_match(self::NOISE, $key) && $key !== '/') {
            Redirect::record404($key);
        }

        return $response;
    }

    protected function gone(): Response
    {
        return response('<!doctype html><meta charset="utf-8"><title>410</title><p>این صفحه دیگر وجود ندارد. <a href="/">صفحه‌ی اصلی</a></p>', 410)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('X-Robots-Tag', 'noindex');
    }
}
