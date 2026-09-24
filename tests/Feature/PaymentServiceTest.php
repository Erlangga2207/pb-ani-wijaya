<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PaymentService;
use App\Services\PurchaseService;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private Purchase $purchase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ItemSeeder::class);
        $this->actingAs($this->userWithRole('owner'));

        // Total Rp7.600.000, belum dibayar.
        $this->purchase = app(PurchaseService::class)->create([
            'supplier_id' => Supplier::create(['name' => 'Pak Tani'])->id,
            'item_id' => Item::where('code', 'GBH')->value('id'),
            'purchase_date' => today()->toDateString(),
            'input_quantity' => 1000,
            'input_unit' => 'kg',
            'price_per_kg' => 7500,
            'other_cost' => 100000,
            'due_date' => today()->addWeek()->toDateString(),
        ], auth()->user());
    }

    public function test_status_transitions_unpaid_partial_paid(): void
    {
        $service = app(PaymentService::class);

        $service->record($this->purchase, ['amount' => 5000000], auth()->user());
        $this->assertSame(PaymentStatus::Partial, $this->purchase->fresh()->payment_status);

        $service->record($this->purchase, ['amount' => 2600000], auth()->user());
        $this->assertSame(PaymentStatus::Paid, $this->purchase->fresh()->payment_status);
        $this->assertEquals(7600000, (float) $this->purchase->fresh()->paid_amount);
    }

    public function test_overpay_is_rejected(): void
    {
        $service = app(PaymentService::class);
        $service->record($this->purchase, ['amount' => 5000000], auth()->user());

        $this->expectException(ValidationException::class);
        $service->record($this->purchase, ['amount' => 2600001], auth()->user());
    }

    public function test_void_payment_recalculates_status(): void
    {
        $service = app(PaymentService::class);
        $payment = $service->record($this->purchase, ['amount' => 7600000], auth()->user());

        $service->void($payment, 'salah nominal', auth()->user());

        $this->assertSame(PaymentStatus::Unpaid, $this->purchase->fresh()->payment_status);
        $this->assertEquals(0, (float) $this->purchase->fresh()->paid_amount);
    }
}
