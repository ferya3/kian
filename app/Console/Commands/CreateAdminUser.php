<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create
                            {--name= : نام کاربر}
                            {--email= : ایمیل}
                            {--role=admin : نقش (admin یا editor)}';

    protected $description = 'ساخت کاربر پنل مدیریت';

    public function handle(): int
    {
        $name = $this->option('name') ?: text('نام و نام خانوادگی', required: true);
        $email = $this->option('email') ?: text('ایمیل', required: true);
        $role = $this->option('role') ?: select('نقش', User::ROLES);

        // گذرواژه هرگز از آرگومان خط فرمان گرفته نمی‌شود:
        // آرگومان‌ها در history شل و در خروجی ps دیده می‌شوند.
        $secret = password('گذرواژه (حداقل ۱۲ نویسه)', required: true);
        $confirm = password('تکرار گذرواژه', required: true);

        $validator = Validator::make(
            compact('name', 'email', 'role', 'secret') + ['secret_confirmation' => $confirm],
            [
                'name' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email:rfc', 'unique:users,email'],
                'role' => ['required', 'in:admin,editor'],
                'secret' => ['required', 'string', 'min:12', 'confirmed'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'is_active' => true,
            'password' => $secret,
        ]);

        $this->newLine();
        $this->info("کاربر «{$name}» با نقش ".User::ROLES[$role].' ساخته شد.');
        $this->line('  ورود: '.route('admin.login'));

        return self::SUCCESS;
    }
}
