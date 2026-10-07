<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;

/**
 * تغییر گذرواژه‌ی یک کاربرِ پنل.
 *
 * گذرواژه‌ی فراموش‌شده بازیابی نمی‌شود — در دیتابیس هَش است و یک‌طرفه. تنها
 * کارِ ممکن جایگزینی‌اش است، و تا پیش از این تنها راهش tinker بود: روی سرور
 * با کاربر www-data خطا می‌دهد، چون psysh می‌خواهد برای تاریخچه‌اش در
 * خانه‌ی کاربر بنویسد و آنجا نوشتنی نیست.
 *
 * admin:create هم جایش را نمی‌گرفت؛ ایمیل را unique می‌گیرد و روی کاربرِ
 * موجود شکست می‌خورد.
 */
class ResetAdminPassword extends Command
{
    protected $signature = 'admin:password {--email= : ایمیل کاربر}';

    protected $description = 'تغییر گذرواژه‌ی کاربر پنل مدیریت';

    public function handle(): int
    {
        $user = $this->user();

        if (! $user) {
            return self::FAILURE;
        }

        /*
        | گذرواژه هرگز از آرگومان خط فرمان گرفته نمی‌شود: آرگومان‌ها در
        | history شل می‌مانند و در خروجی ps دیده می‌شوند. همان قاعده‌ی
        | admin:create.
        */
        $secret = password('گذرواژه‌ی تازه (حداقل ۱۲ نویسه)', required: true);
        $confirm = password('تکرار گذرواژه', required: true);

        $validator = Validator::make(
            ['secret' => $secret, 'secret_confirmation' => $confirm],
            ['secret' => ['required', 'string', 'min:12', 'confirmed']]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        /*
        | کاربرِ غیرفعال دوباره فعال نمی‌شود.
        |
        | کسی که دسترسی‌اش عمداً بسته شده، نباید با عوض‌کردن گذرواژه برگردد.
        | اگر واقعاً باید برگردد، جایش پنل است و نه این دستور.
        */
        $user->update(['password' => $secret]);

        $this->newLine();
        $this->info("گذرواژه‌ی «{$user->name}» عوض شد.");

        if (! $user->is_active) {
            $this->warn('  ولی این کاربر غیرفعال است و هنوز نمی‌تواند وارد شود.');
        }

        $this->line('  ورود: '.route('admin.login'));

        return self::SUCCESS;
    }

    protected function user(): ?User
    {
        if ($email = $this->option('email')) {
            $user = User::query()->where('email', $email)->first();

            if (! $user) {
                $this->error("کاربری با ایمیل «{$email}» نیست.");
            }

            return $user;
        }

        $users = User::query()->orderBy('name')->get();

        if ($users->isEmpty()) {
            $this->error('هیچ کاربری در پنل نیست. اول admin:create را بزنید.');

            return null;
        }

        $id = select('کدام کاربر؟', $users->mapWithKeys(fn (User $u) => [
            $u->id => $u->name.' — '.$u->email.($u->is_active ? '' : ' (غیرفعال)'),
        ])->all());

        return $users->firstWhere('id', $id);
    }
}
