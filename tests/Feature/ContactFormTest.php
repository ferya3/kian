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
}
