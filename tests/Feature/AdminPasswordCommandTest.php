<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * دستورِ تغییر گذرواژه‌ی پنل.
 *
 * گذرواژه‌ی فراموش‌شده بازیابی نمی‌شود؛ تنها کارِ ممکن جایگزینی است. این
 * تست‌ها همان مرزها را می‌بندند که آدمِ پشتِ ترمینال ممکن است از آن رد شود.
 */
class AdminPasswordCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_replaces_the_password(): void
    {
        $user = User::factory()->create([
            'email' => 'a@kian.test',
            'is_active' => true,
            'password' => 'گذرواژه‌ی قدیمی',
        ]);

        $this->artisan('admin:password', ['--email' => 'a@kian.test'])
            ->expectsQuestion('گذرواژه‌ی تازه (حداقل ۱۲ نویسه)', 'یک-گذرواژه‌ی-تازه')
            ->expectsQuestion('تکرار گذرواژه', 'یک-گذرواژه‌ی-تازه')
            ->assertSuccessful();

        $this->assertTrue(Hash::check('یک-گذرواژه‌ی-تازه', $user->fresh()->password));
    }

    public function test_the_two_entries_must_match(): void
    {
        $user = User::factory()->create(['email' => 'a@kian.test', 'password' => 'قدیمی‌تر از این حرف‌ها']);

        $this->artisan('admin:password', ['--email' => 'a@kian.test'])
            ->expectsQuestion('گذرواژه‌ی تازه (حداقل ۱۲ نویسه)', 'یک-گذرواژه‌ی-تازه')
            ->expectsQuestion('تکرار گذرواژه', 'چیزِ دیگری')
            ->assertFailed();

        $this->assertTrue(Hash::check('قدیمی‌تر از این حرف‌ها', $user->fresh()->password));
    }

    public function test_a_short_password_is_refused(): void
    {
        User::factory()->create(['email' => 'a@kian.test', 'password' => 'قدیمی‌تر از این حرف‌ها']);

        $this->artisan('admin:password', ['--email' => 'a@kian.test'])
            ->expectsQuestion('گذرواژه‌ی تازه (حداقل ۱۲ نویسه)', 'کوتاه')
            ->expectsQuestion('تکرار گذرواژه', 'کوتاه')
            ->assertFailed();
    }

    public function test_an_unknown_email_stops_before_asking_anything(): void
    {
        $this->artisan('admin:password', ['--email' => 'hich@kian.test'])
            ->expectsOutputToContain('کاربری با ایمیل')
            ->assertFailed();
    }

    /**
     * کاربرِ غیرفعال با گذرواژه‌ی تازه برنمی‌گردد.
     *
     * دسترسی‌ای که عمداً بسته شده، نباید از این راه باز شود — و اگر کسی
     * گمان کند باز شده، بدتر است. پس هشدار می‌دهد.
     */
    public function test_a_disabled_user_is_told_they_still_cannot_sign_in(): void
    {
        $user = User::factory()->create([
            'email' => 'a@kian.test',
            'is_active' => false,
            'password' => 'قدیمی‌تر از این حرف‌ها',
        ]);

        $this->artisan('admin:password', ['--email' => 'a@kian.test'])
            ->expectsQuestion('گذرواژه‌ی تازه (حداقل ۱۲ نویسه)', 'یک-گذرواژه‌ی-تازه')
            ->expectsQuestion('تکرار گذرواژه', 'یک-گذرواژه‌ی-تازه')
            ->expectsOutputToContain('غیرفعال')
            ->assertSuccessful();

        $this->assertFalse($user->fresh()->is_active);
    }
}
