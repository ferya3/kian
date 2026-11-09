<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * دروازه‌ی پنل مدیریت.
 *
 * سه شرط، به همین ترتیب: کاربر وارد شده باشد، حسابش فعال باشد، و اگر منبع
 * «فقط مدیر کل» است نقشش admin باشد. حساب غیرفعال بلافاصله خارج می‌شود تا
 * نشست باقی‌مانده کار نکند.
 */
class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next, ?string $role = null): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('admin.login');
        }

        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'حساب کاربری شما غیرفعال شده است.']);
        }

        if ($role === 'admin' && ! $user->isAdmin()) {
            abort(403, 'این بخش فقط برای مدیر کل در دسترس است.');
        }

        /*
         * فروشنده کارمندِ سایت نیست.
         *
         * منابع پنل از Registry::accessible رد می‌شوند و آنجا مهار شده‌اند،
         * ولی داشبورد و کتابخانه‌ی تصاویر کنترلرِ جداگانه دارند و از آن
         * دروازه نمی‌گذرند — یعنی فروشنده آمارِ کل سایت و همه‌ی فایل‌های
         * آپلودشده را می‌دید.
         */
        if ($role === 'staff' && $user->isVendor()) {
            abort(403, 'این بخش برای فروشندگان در دسترس نیست.');
        }

        $response = $next($request);

        /*
        | headers->set و نه withHeaders: پاسخِ پنل همیشه Response ِ لاراول نیست —
        | دانلودِ فایل (عکسِ رسید، اسناد) StreamedResponse است و withHeaders
        | ندارد؛ هر دانلودی از پنل با خطای ۵۰۰ می‌افتاد.
        */
        $response->headers->set('X-Frame-Options', 'DENY');   // پنل هرگز داخلِ iframe
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        $response->headers->set('Referrer-Policy', 'same-origin');

        return $response;
    }
}
