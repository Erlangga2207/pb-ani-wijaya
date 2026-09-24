<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Exceptions\InsufficientStockException;
use App\Filament\Admin\Resources\Sales\Pages\CreateSale;
use App\Models\Buyer;
use App\Models\Sale;
use App\Services\SaleService;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class SaleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ItemSeeder::class);
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();
        $this->millGabah(); // beras 560 kg @Rp13.571,43, menir 60 kg @0
    }

    public function test_scenario_from_prd(): void
    {
        $sale = $this->sellRice();

        $this->assertMatchesRegularExpression('/^PJ-\d{8}-0001$/', $sale->number);
        $this->assertEquals(5400000, (float) $sale->subtotal);
        $this->assertEquals(5350000, (float) $sale->total);
        $this->assertEquals(3392857.50, (float) $sale->cogs_total);
        $this->assertEquals(1957142.50, $sale->grossProfit());
        $this->assertSame(PaymentStatus::Unpaid, $sale->payment_status);

        $this->assertEquals(310, (float) $this->item('BKT')->stock_kg);
        $this->assertEquals(10, (float) $this->item('MNR')->stock_kg);
        $this->assertEquals(20000, (float) $this->item('BKT')->default_price_per_kg);
    }

    public function test_selling_more_than_stock_saves_nothing(): void
    {
        try {
            $this->sellRice(['items' => [
                ['item_id' => $this->item('MNR')->id, 'input_quantity' => 10, 'input_unit' => 'kg', 'price_per_kg' => 8000],
                ['item_id' => $this->item('BKT')->id, 'input_quantity' => 600, 'input_unit' => 'kg', 'price_per_kg' => 20000],
            ], 'discount' => 0]);
            $this->fail('Exception expected');
        } catch (InsufficientStockException) {
        }

        $this->assertSame(0, Sale::count());
        $this->assertEquals(60, (float) $this->item('MNR')->stock_kg);
        $this->assertEquals(560, (float) $this->item('BKT')->stock_kg);
    }

    public function test_discount_greater_than_subtotal_is_rejected(): void
    {
        $this->expectException(ValidationException::class);
        $this->sellRice(['discount' => 6000000]);
    }

    public function test_duplicate_item_and_raw_item_are_rejected(): void
    {
        $line = ['item_id' => $this->item('BKT')->id, 'input_quantity' => 1, 'input_unit' => 'kg', 'price_per_kg' => 20000];

        try {
            $this->sellRice(['items' => [$line, $line], 'discount' => 0]);
            $this->fail('Duplicate expected to fail');
        } catch (ValidationException) {
        }

        $this->expectException(ValidationException::class);
        $this->sellRice(['items' => [['item_id' => $this->item('GBH')->id] + $line], 'discount' => 0]);
    }

    public function test_void_restores_stock_and_payments(): void
    {
        $sale = $this->sellRice(['pay_now' => 1000000]);

        app(SaleService::class)->void($sale, 'pembeli batal', auth()->user());

        $sale->refresh();
        $this->assertTrue($sale->isVoid());
        $this->assertEquals(0, (float) $sale->paid_amount);
        $this->assertEquals(560, (float) $this->item('BKT')->stock_kg);
        $this->assertEquals(13571.43, (float) $this->item('BKT')->avg_cost_per_kg);
        $this->artisan('stock:verify')->assertExitCode(0);
    }

    public function test_create_via_filament_form(): void
    {
        $buyer = Buyer::create(['name' => 'Toko A', 'phone' => '0812']);

        Livewire::test(CreateSale::class)
            ->fillForm([
                'buyer_id' => $buyer->id,
                'sale_date' => today()->toDateString(),
                'items' => [
                    ['item_id' => $this->item('BKT')->id, 'input_quantity' => 10, 'input_unit' => 'sack', 'price_per_kg' => 20000],
                ],
                'discount' => 0,
                'shipping_cost' => 25000,
                'pay_now' => 5025000,
                'payment_method' => 'transfer',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $sale = Sale::first();
        $this->assertEquals(5025000, (float) $sale->total);
        $this->assertSame(PaymentStatus::Paid, $sale->payment_status);
    }
}
