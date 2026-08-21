<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * یک حساب مدیر می‌سازد.
     *
     * گذرواژه از ADMIN_PASSWORD خوانده می‌شود؛ اگر تنظیم نشده باشد یک گذرواژه‌ی
     * تصادفی ساخته و *یک‌بار* چاپ می‌شود. هیچ گذرواژه‌ی پیش‌فرضی در کد نیست —
     * نصب‌های فراموش‌شده نباید با رمز عمومی باز بمانند.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@kian-ceramic.ir');

        if (User::where('email', $email)->exists()) {
            return;
        }

        $secret = env('ADMIN_PASSWORD') ?: Str::password(16, symbols: false);

        User::create([
            'name' => env('ADMIN_NAME', 'مدیر سیستم'),
            'email' => $email,
            'role' => 'admin',
            'is_active' => true,
            'password' => $secret,
        ]);

        $this->command?->newLine();
        $this->command?->info('  حساب مدیر ساخته شد:');
        $this->command?->line("    ایمیل   : {$email}");

        if (! env('ADMIN_PASSWORD')) {
            $this->command?->line("    گذرواژه : {$secret}");
            $this->command?->warn('    این گذرواژه فقط همین یک‌بار نمایش داده می‌شود — ذخیره‌اش کنید.');
        }

        $this->command?->newLine();
    }
}
