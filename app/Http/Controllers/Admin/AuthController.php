<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** حداکثر تلاش ناموفق پیش از قفل موقت. */
    protected const MAX_ATTEMPTS = 5;

    protected const DECAY_SECONDS = 300;

    public function show()
    {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email:rfc', 'max:190'],
            'password' => ['required', 'string', 'max:190'],
        ]);

        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);

            throw ValidationException::withMessages([
                'email' => "تلاش‌های ناموفق زیاد بود. {$minutes} دقیقه دیگر دوباره امتحان کنید.",
            ]);
        }

        // حساب غیرفعال حتی با گذرواژه‌ی درست وارد نمی‌شود
        if (! Auth::attempt([...$credentials, 'is_active' => true], $request->boolean('remember'))) {
            RateLimiter::hit($key, self::DECAY_SECONDS);
            ActivityLog::record('login_failed', null, [], $credentials['email']);

            throw ValidationException::withMessages([
                'email' => 'ایمیل یا گذرواژه درست نیست.',
            ]);
        }

        RateLimiter::clear($key);

        // جلوگیری از session fixation
        $request->session()->regenerate();

        $request->user()->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        ActivityLog::record('login');

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        ActivityLog::record('logout');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /** کلید محدودیت بر اساس ایمیل و IP — تا حمله‌ی توزیع‌شده هم شمرده شود. */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }
}
