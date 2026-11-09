<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'type' => 'quote',
            'name' => 'رضا محمدی',
            'company' => 'ساختمانی البرز',
            'phone' => '۰۹۱۲۱۲۳۴۵۶۷',
            'city' => 'تهران',
            'message' => 'برای پروژه چهار هزار متری در تهران به بلوک ۲۰ نیاز داریم.',
            // فرم ده ثانیه پیش باز شده — آهنگِ انسان
            '_ts' => \Illuminate\Support\Facades\Crypt::encryptString((string) (time() - 10)),
        ], $overrides);
    }

    public function test_a_valid_request_is_stored(): void
    {
        $this->post(route('contact.store'), $this->payload())
            ->assertRedirect(route('contact'))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('contact_messages', 1);
    }

    public function test_persian_digits_in_the_phone_number_are_normalised(): void
    {
        $this->post(route('contact.store'), $this->payload());

        $this->assertSame('09121234567', ContactMessage::first()->phone);
    }

    public function test_a_malformed_phone_number_is_rejected(): void
    {
        $this->post(route('contact.store'), $this->payload(['phone' => '123']))
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_a_too_short_message_is_rejected(): void
    {
        $this->post(route('contact.store'), $this->payload(['message' => 'سلام']))
            ->assertSessionHasErrors('message');
    }

    public function test_the_honeypot_field_blocks_bots(): void
    {
        $this->post(route('contact.store'), $this->payload(['website' => 'https://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    /** ربات فرم را بی‌درنگ می‌فرستد؛ یا مهرِ زمان ندارد، یا مهرِ ساختگی. */
    public function test_a_form_sent_too_fast_or_without_a_valid_stamp_is_rejected(): void
    {
        foreach ([
            'too fast' => \Illuminate\Support\Facades\Crypt::encryptString((string) time()),
            'forged' => (string) (time() - 60),
            'missing' => null,
        ] as $case => $stamp) {
            $this->post(route('contact.store'), $this->payload(['_ts' => $stamp]))
                ->assertSessionHasErrors('_ts');
        }

        $this->assertSame(0, \App\Models\ContactMessage::count());
    }

    public function test_the_form_carries_a_time_stamp(): void
    {
        $html = $this->get(route('contact'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('~<input type="hidden" name="_ts" value="[^"]{40,}">~', $html);
    }
}
