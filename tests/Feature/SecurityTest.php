<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Receipt;
use App\Models\User;
use App\Support\Admin\Field;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * لایه‌های امنیتیِ سیستمی — آنچه نباید به دقتِ تک‌تکِ کنترلرها وابسته باشد.
 */
class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * نقش و وضعیت از ورودی نوشتنی نیستند.
     *
     * امروز هیچ کنترلری ورودیِ خام را به مدل نمی‌دهد؛ این تست برای روزی است
     * که کسی بنویسد User::create($request->all()) — «role=admin» در فرم
     * نباید کسی را مدیر کند.
     */
    public function test_privileged_columns_cannot_be_mass_assigned(): void
    {
        $user = User::create([
            'name' => 'مهاجم', 'email' => 'x@example.com', 'password' => 'secret-pass',
            'role' => 'admin', 'is_active' => true, 'vendor_id' => 7,
        ]);

        $this->assertNotSame('admin', $user->fresh()->role);
        $this->assertNull($user->fresh()->vendor_id);

        $order = (new Order)->fill(['customer_name' => 'الف', 'status' => 'paid', 'total' => 1, 'token' => 't']);
        $this->assertSame(['customer_name' => 'الف'], $order->getAttributes());

        $receipt = (new Receipt)->fill(['reference' => '۱۲', 'status' => 'confirmed', 'amount' => 1]);
        $this->assertSame(['reference' => '۱۲'], $receipt->getAttributes());
    }

    /** SVG سند است و می‌تواند اسکریپت داشته باشد — نه در عکس پذیرفته می‌شود، نه در فایل. */
    public function test_svg_uploads_are_rejected_everywhere(): void
    {
        $svg = UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');

        foreach ([Field::image('x', 'x', 'site'), Field::file('x', 'x', 'documents')] as $field) {
            $this->assertTrue(
                Validator::make(['x' => $svg], ['x' => $field->validationRules()])->fails(),
                "فیلدِ {$field->type} نباید SVG بپذیرد."
            );
        }

        $pdf = UploadedFile::fake()->create('sheet.pdf', 10, 'application/pdf');
        $this->assertTrue(Validator::make(['x' => $pdf], ['x' => Field::file('x', 'x', 'documents')->validationRules()])->passes());
    }

    /** سرآیندها و CSP روی صفحه‌ی عمومی؛ nonce ِ CSP همان nonce ِ اسکریپتِ inline است. */
    public function test_pages_carry_security_headers_and_a_nonce_csp(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $response = $this->get(route('home'))->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertNotNull($csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
        $this->assertStringNotContainsString("script-src 'self' 'unsafe-inline'", $csp, 'اسکریپتِ inline فقط با nonce.');

        preg_match("~'nonce-([^']+)'~", $csp, $m);
        $this->assertNotEmpty($m[1] ?? null);
        $this->assertStringContainsString('<script nonce="'.$m[1].'">', $response->getContent());

        // روی http، HSTS نه
        $response->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_https_adds_hsts_without_subdomains(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $hsts = $this->get('https://localhost/fa')->headers->get('Strict-Transport-Security');

        $this->assertSame('max-age=31536000', $hsts, 'زیردامنه‌ها (مثل nobat) نباید مجبور به https شوند.');
    }

    /** پنل DENY می‌گیرد — یک بار، نه کنارِ SAMEORIGIN. */
    public function test_the_panel_is_never_framed(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();

        $this->assertSame(['DENY'], $response->headers->all('x-frame-options'));
    }

    /**
     * پیش‌فرض‌های پیکربندیِ نشست، بی ‎.env: رمزنگاری روشن، و Secure از روی
     * طرحِ APP_URL — تا نصبِ تازه روی http قفل نشود و سایتِ https بی تنظیم امن باشد.
     */
    public function test_session_defaults_encrypt_and_follow_the_site_scheme(): void
    {
        $saved = [$_ENV, $_SERVER];
        unset($_ENV['SESSION_ENCRYPT'], $_SERVER['SESSION_ENCRYPT'], $_ENV['SESSION_SECURE_COOKIE'], $_SERVER['SESSION_SECURE_COOKIE']);
        putenv('SESSION_ENCRYPT'); putenv('SESSION_SECURE_COOKIE');

        try {
            foreach (['https://kiyanbehsaz.com' => true, 'http://94.184.45.129' => false] as $url => $secure) {
                $_ENV['APP_URL'] = $_SERVER['APP_URL'] = $url;
                putenv("APP_URL={$url}");

                $config = require config_path('session.php');

                $this->assertTrue($config['encrypt'], 'رمزنگاری پیش‌فرض روشن است.');
                $this->assertSame($secure, $config['secure'], $url);
            }
        } finally {
            [$_ENV, $_SERVER] = $saved;
            putenv('APP_URL='.($saved[0]['APP_URL'] ?? ''));
        }
    }

    /**
     * ورودِ ناموفق یک خطِ ثابت در security.log می‌گذارد — همان که fail2ban
     * (deploy/install.sh) با «admin-login-failed ip=<HOST>» می‌خواند.
     * گذرواژه هرگز در آن نیست.
     */
    public function test_a_failed_login_is_written_for_fail2ban(): void
    {
        $log = sys_get_temp_dir().'/kian-security-'.uniqid().'.log';
        config(['logging.channels.security.path' => $log]);

        $this->post(route('admin.login'), ['email' => 'nobody@example.com', 'password' => 'Secret-Guess-1'])
            ->assertSessionHasErrors('email');

        $line = file_get_contents($log);
        @unlink($log);

        $this->assertMatchesRegularExpression('~admin-login-failed ip=127\.0\.0\.1 email=nobody@example\.com~', $line);
        $this->assertStringNotContainsString('Secret-Guess-1', $line);
    }
}
