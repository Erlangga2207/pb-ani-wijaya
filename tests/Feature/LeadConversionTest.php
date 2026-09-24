<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Filament\Admin\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Models\Buyer;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeadConversionTest extends TestCase
{
    use RefreshDatabase;

    public function test_convert_lead_to_buyer_via_action(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $lead = ContactMessage::create([
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'subject' => 'Minta harga',
            'message' => 'Butuh 2 ton ketan',
        ]);
        $lead->update(['phone' => '0812-3333-4444', 'company' => 'CV Ketan Jaya', 'country' => 'Malaysia']);

        $this->get('/admin/contact-messages')->assertOk()->assertSee('Budi');

        Livewire::test(ViewContactMessage::class, ['record' => $lead->getRouteKey()])
            ->callAction('convertToBuyer');

        $lead->refresh();
        $buyer = Buyer::first();
        $this->assertSame('CV Ketan Jaya', $buyer->name);
        $this->assertSame('Malaysia', $buyer->country);
        $this->assertSame($buyer->id, $lead->buyer_id);
        $this->assertSame(LeadStatus::Contacted, $lead->status);
        $this->assertTrue($lead->is_read);
        $this->assertStringContainsString('wa.me/6281233334444', $lead->whatsappUrl());
    }

    public function test_mark_as_spam(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $lead = ContactMessage::create(['name' => 'Bot', 'email' => 'bot@x.com', 'message' => 'spam']);

        Livewire::test(ViewContactMessage::class, ['record' => $lead->getRouteKey()])
            ->callAction('spam');

        $this->assertSame(LeadStatus::Spam, $lead->fresh()->status);
    }
}
