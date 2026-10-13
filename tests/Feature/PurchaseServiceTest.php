<?php

namespace Tests\Feature;

use App\Enums\DocumentStatus;
use App\Enums\PaymentStatus;
use App\Filament\Admin\Resources\Purchases\Pages\CreatePurchase;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use App\Services\StockService;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class PurchaseServiceTest extends TestCase
{
    use RefreshDatabase;

    private Item $gabah;

    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ItemSeeder::class);
        $this->gabah = Item::where('code', 'GBH')->first();
        $this->supplier = Supplier::create(['name' => 'Pak Tani', 'village' => 'Losarang']);
        $this->actingAs($this->userWithRole('owner'));
    }

    private function purchase(array $override = []): Purchase
    {
        return app(PurchaseService::class)->create(array_merge([
            'supplier_id' => $this->supplier->id,
            'item_id' => $this->gabah->id,
            'purchase_date' => today()->toDateString(),
            'input_quantity' => 40,
            'input_unit' => 'sack',
            'price_per_kg' => 7500,
            'other_cost' => 100000,
            'pay_now' => 0,
            'due_date' => today()->addWeek()->toDateString(),
        ], $override), auth()->user());
    }

    public function test_purchase_scenario_from_prd(): void
    {
        $p = $this->purchase();

        $this->assertMatchesRegularExpression('/^PB-\d{8}-0001$/', $p->number);
        $this->assertEquals(1000, (float) $p->quantity_kg);
        $this->assertEquals(7500000, (float) $p->subtotal);
        $this->assertEquals(7600000, (float) $p->total);
        $this->assertSame(PaymentStatus::Unpaid, $p->payment_status);

        $gabah = $this->gabah->fresh();
        $this->assertEquals(1000, (float) $gabah->stock_kg);
        $this->assertEquals(7600, (float) $gabah->avg_cost_per_kg);
        $this->assertEquals(7500, (float) $gabah->default_price_per_kg);
    }

    public function test_pay_now_creates_first_payment(): void
    {
        $p = $this->purchase(['pay_now' => 7600000, 'due_date' => null]);

        $this->assertSame(PaymentStatus::Paid, $p->payment_status);
        $this->assertSame(1, $p->payments()->count());
    }

    public function test_due_date_required_when_not_fully_paid(): void
    {
        $this->expectException(ValidationException::class);
        $this->purchase(['pay_now' => 1000, 'due_date' => null]);
    }

    public function test_future_date_is_rejected(): void
    {
        $this->expectException(ValidationException::class);
        $this->purchase(['purchase_date' => today()->addDay()->toDateString()]);
    }

    public function test_void_rejected_when_gabah_already_milled(): void
    {
        $p = $this->purchase();
        DB::transaction(fn () => app(StockService::class)->decrease($this->gabah, 500, auth()->user(), today()));

        try {
            app(PurchaseService::class)->void($p, 'salah input', auth()->user());
            $this->fail('Exception expected');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('sudah terpakai', collect($e->errors())->flatten()->first());
        }

        $this->assertSame(DocumentStatus::Posted, $p->fresh()->status);
    }

    public function test_void_reverses_stock_and_payments(): void
    {
        $p = $this->purchase(['pay_now' => 5000000]);

        app(PurchaseService::class)->void($p, 'salah petani', auth()->user());

        $p->refresh();
        $this->assertTrue($p->isVoid());
        $this->assertEquals(0, (float) $this->gabah->fresh()->stock_kg);
        $this->assertSame(0, $p->payments()->posted()->count());
        $this->assertEquals(0, (float) $p->paid_amount);
    }

    public function test_create_via_filament_form(): void
    {
        $this->actingAs($this->userWithRole('staff'));

        Livewire::test(CreatePurchase::class)
            ->fillForm([
                'supplier_id' => $this->supplier->id,
                'purchase_date' => today()->toDateString(),
                'item_id' => $this->gabah->id,
                'input_quantity' => 40,
                'input_unit' => 'sack',
                'price_per_kg' => 7500,
                'other_cost' => 100000,
                'pay_now' => 7600000,
                'payment_method' => 'cash',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $p = Purchase::first();
        $this->assertEquals(1000, (float) $p->quantity_kg);
        $this->assertSame(PaymentStatus::Paid, $p->payment_status);
    }

    public function test_pages_and_receipt_render(): void
    {
        $p = $this->purchase();

        foreach (['/admin/purchases', '/admin/purchases/create', "/admin/purchases/{$p->id}", '/admin/payables', "/pembelian/{$p->id}/bukti"] as $url) {
            $this->get($url)->assertOk();
        }

        auth()->logout();
        $this->get("/pembelian/{$p->id}/bukti")->assertRedirect('/panel/login');
    }
}
