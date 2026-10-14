<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    private array $valid = ['name' => 'Siti', 'email' => 'siti@example.com', 'subject' => 'Harga', 'message' => 'Berapa harga ketan per karung?'];

    public function test_valid_message_becomes_new_lead_with_ip(): void
    {
        $this->post('/kontak', $this->valid)->assertRedirect(route('contact'))->assertSessionHas('success');

        $lead = ContactMessage::first();
        $this->assertSame(LeadStatus::New, $lead->status);
        $this->assertNotNull($lead->ip_address);
    }

    public function test_valid_message_is_emailed_to_sales_with_reply_to_sender(): void
    {
        Mail::fake();

        $this->post('/kontak', $this->valid)->assertRedirect();

        Mail::assertSent(NewContactMessage::class, fn ($mail) => $mail->hasTo('sales@aniwijaya.com')
            && $mail->hasReplyTo('siti@example.com'));
    }

    public function test_spam_is_not_emailed(): void
    {
        Mail::fake();

        $this->post('/kontak', $this->valid + ['website' => 'http://spam.example'])->assertRedirect();

        Mail::assertNothingSent();
    }

    public function test_mail_failure_still_saves_lead(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP down'));

        $this->post('/kontak', $this->valid)->assertRedirect(route('contact'))->assertSessionHas('success');
        $this->assertSame(1, ContactMessage::count());
    }

    public function test_validation(): void
    {
        $this->post('/kontak', ['name' => '', 'email' => 'bukan-email', 'message' => ''])
            ->assertSessionHasErrors(['name', 'email', 'message']);
        $this->assertSame(0, ContactMessage::count());
    }

    public function test_honeypot_is_saved_as_spam_but_looks_successful(): void
    {
        $this->post('/kontak', $this->valid + ['website' => 'http://spam.example'])
            ->assertRedirect(route('contact'))
            ->assertSessionHas('success');

        $this->assertSame(0, ContactMessage::where('status', LeadStatus::New)->count());
        $this->assertSame(LeadStatus::Spam, ContactMessage::first()->status);
    }

    public function test_sixth_submission_in_a_minute_is_throttled(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/kontak', $this->valid)->assertRedirect();
        }

        $this->post('/kontak', $this->valid)->assertStatus(429);
        $this->assertSame(5, ContactMessage::count());
    }

    public function test_honeypot_field_is_hidden_on_page(): void
    {
        $this->seed(SiteSettingSeeder::class);

        $this->get('/kontak')->assertOk()->assertSee('name="website" class="hidden" tabindex="-1"', false);
    }
}
