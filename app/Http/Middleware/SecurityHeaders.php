<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * سرآیندهای امنیتی برای هر پاسخِ لاراول.
 *
 * پیش‌تر nginx آن‌ها را در سطحِ server می‌گذاشت؛ دو مشکل داشت: (۱) هر
 * location ای که خودش add_header داشت (assetها، عکس‌ها) همه را گم می‌کرد،
 * و (۲) X-Frame-Options دو بار می‌رسید — SAMEORIGIN از nginx و DENY از پنل —
 * که مرورگر ترکیبِ متناقض را نادیده می‌گیرد. حالا پاسخِ پویا از اینجا
 * می‌گیرد و nginx فقط فایل‌های ایستا را.
 *
 * CSP: اسکریپت فقط از همین دامنه و inline ها فقط با nonce. «unsafe-eval» لازم
 * است چون Alpine عبارت‌های x-* را با new Function می‌سازد؛ برداشتنش یعنی
 * بازنویسیِ همه‌ی ویوها با نسخه‌ی CSP ِ Alpine. style-src «unsafe-inline» دارد
 * چون ویوها ویژگیِ style دارند. بیرون از دامنه چیزی بار نمی‌شود — جز عکس و
 * ویدئو با https، که ممکن است نشانیِ بیرونی از پنل باشند.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // nonce پیش از رندر: @vite و اسکریپتِ inline ِ چیدمان همین را می‌خوانند
        $nonce = Vite::useCspNonce();

        $response = $next($request);
        $headers = $response->headers;

        $defaults = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()',
            'Cross-Origin-Opener-Policy' => 'same-origin',
        ];

        // آنچه لایه‌ی درونی‌تر گذاشته (مثلاً DENY ِ پنل) می‌ماند
        foreach ($defaults as $name => $value) {
            if (! $headers->has($name)) {
                $headers->set($name, $value);
            }
        }

        /*
        | HSTS فقط روی https، و بی includeSubDomains: زیردامنه‌ای مثل
        | nobat.kiyanbehsaz.com شاید روی https نباشد و نباید از دسترس بیفتد.
        */
        if ($request->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        if ($this->wantsCsp($request, $response) && ! $headers->has('Content-Security-Policy')) {
            $headers->set('Content-Security-Policy', $this->policy($nonce, $request->isSecure()));
        }

        return $response;
    }

    /** فقط HTML، و نه در توسعه با سرورِ Vite (که از پورتِ دیگری اسکریپت می‌دهد). */
    protected function wantsCsp(Request $request, Response $response): bool
    {
        if (Vite::isRunningHot()) {
            return false;
        }

        return str_contains((string) $response->headers->get('Content-Type'), 'text/html');
    }

    protected function policy(string $nonce, bool $secure): string
    {
        $rules = [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: blob: https:",
            "font-src 'self' data:",
            "connect-src 'self'",
            "media-src 'self' https:",
            "frame-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ];

        if ($secure) {
            $rules[] = 'upgrade-insecure-requests';
        }

        return implode('; ', $rules);
    }
}
