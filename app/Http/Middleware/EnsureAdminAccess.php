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

        return $next($request)
            // پنل هرگز نباید داخل iframe سایت دیگری بارگذاری شود
            ->withHeaders([
                'X-Frame-Options' => 'DENY',
                'X-Robots-Tag' => 'noindex, nofollow',
                'Referrer-Policy' => 'same-origin',
            ]);
    }
}
