<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $editor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);
    }

    public static function adminRoutes(): array
    {
        return [
            ['/admin'],
            ['/admin/products'],
            ['/admin/products/create'],
            ['/admin/messages'],
            ['/admin/users'],
            ['/admin/activity'],
        ];
    }

    /** @dataProvider adminRoutes */
    public function test_a_guest_cannot_reach_the_panel(string $path): void
    {
        $this->get($path)->assertRedirect(route('admin.login'));
    }

    public function test_a_guest_cannot_write_through_the_panel(): void
    {
        $this->post('/admin/faqs', ['question' => 'x', 'answer' => 'y'])
            ->assertRedirect(route('admin.login'));

        $this->delete('/admin/faqs/1')->assertRedirect(route('admin.login'));
    }

    public function test_an_editor_is_blocked_from_admin_only_sections(): void
    {
        $this->actingAs($this->editor)->get('/admin/users')->assertForbidden();
        $this->actingAs($this->editor)->get('/admin/settings')->assertForbidden();
        $this->actingAs($this->editor)->get('/admin/activity')->assertForbidden();
    }

    public function test_an_editor_can_still_manage_content(): void
    {
        $this->actingAs($this->editor)->get('/admin/products')->assertOk();
        $this->actingAs($this->editor)->get('/admin/articles')->assertOk();
    }

    public function test_a_deactivated_account_is_signed_out_on_the_next_request(): void
    {
        $this->admin->update(['is_active' => false]);

        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_a_deactivated_account_cannot_sign_in_even_with_the_right_password(): void
    {
        $user = User::factory()->create([
            'email' => 'off@example.com',
            'password' => 'correct-horse-battery',
            'is_active' => false,
        ]);

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'correct-horse-battery',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_repeated_failures_lock_the_login_form(): void
    {
        RateLimiter::clear('someone@example.com|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['email' => 'someone@example.com', 'password' => 'wrong'])
                ->assertSessionHasErrors('email');
        }

        $response = $this->post('/admin/login', ['email' => 'someone@example.com', 'password' => 'wrong']);

        $this->assertStringContainsString('دقیقه دیگر', $response->getSession()->get('errors')->first('email'));
    }

    public function test_failed_sign_in_attempts_are_recorded(): void
    {
        $this->post('/admin/login', ['email' => 'intruder@example.com', 'password' => 'wrong']);

        $this->assertDatabaseHas('activity_logs', [
            'event' => 'login_failed',
            'subject_label' => 'intruder@example.com',
        ]);
    }

    public function test_a_successful_sign_in_regenerates_the_session_and_is_recorded(): void
    {
        $user = User::factory()->create([
            'email' => 'boss@example.com',
            'password' => 'correct-horse-battery',
            'role' => 'admin',
        ]);

        $this->post('/admin/login', [
            'email' => 'boss@example.com',
            'password' => 'correct-horse-battery',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
        $this->assertDatabaseHas('activity_logs', ['event' => 'login', 'user_id' => $user->id]);
    }

    public function test_panel_pages_are_not_indexable(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertHeader('X-Frame-Options', 'DENY');
    }

    public function test_an_unknown_resource_is_a_404(): void
    {
        $this->actingAs($this->admin)->get('/admin/not-a-resource')->assertNotFound();
    }
}
