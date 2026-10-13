<?php

namespace Tests\Feature\Admin;

use App\Enums\DocumentStatus;
use App\Enums\PaymentStatus;
use App\Models\Buyer;
use App\Models\Production;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\Supplier;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Acceptance criteria PRD-02/03/04/05 lewat panel /panel (bukan lewat service langsung).
class TransactionPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ItemSeeder::class);
    }

    // ---------- Pembelian (PRD-03) ----------

    public function test_purchase_scenario_via_panel(): void
    {
        $this->actingAs($this->userWithRole('staff'));
        $supplier = Supplier::create(['name' => 'Pak Tani', 'village' => 'Losarang']);

        $this->get('/panel/pembelian/create')->assertOk();
        $response = $this->post('/panel/pembelian', $this->purchaseData(['supplier_id' => $supplier->id]));

        $purchase = Purchase::firstOrFail();
        $response->assertRedirect("/panel/pembelian/{$purchase->id}");
        $this->assertSame('1000.00', $purchase->quantity_kg);
        $this->assertSame('7600000.00', $purchase->total);
        $this->assertSame('1000.00', $this->item('GBH')->stock_kg);
        $this->assertSame('7600.00', $this->item('GBH')->avg_cost_per_kg);

        $this->get("/panel/pembelian/{$purchase->id}")->assertOk()->assertSee($purchase->number)->assertSee('Pak Tani');
        $this->get('/panel/pembelian')->assertOk()->assertSee($purchase->number);
    }

    public function test_purchase_with_new_supplier_and_partial_payments(): void
    {
        $this->actingAs($this->userWithRole('staff'));

        $this->post('/panel/pembelian', $this->purchaseData([
            'new_supplier_name' => 'Bu Rosi', 'new_supplier_village' => 'Krimun',
            'pay_now' => 0, 'due_date' => today()->addWeek()->toDateString(),
        ]))->assertRedirect();

        $purchase = Purchase::with('supplier')->firstOrFail();
        $this->assertSame('Bu Rosi', $purchase->supplier->name);

        // Bayar Rp5.000.000 → Sebagian; bayar sisa → Lunas; melebihi sisa → ditolak.
        $pay = fn ($amount) => $this->from("/panel/pembelian/{$purchase->id}")
            ->post("/panel/pembelian/{$purchase->id}/pembayaran", ['payment_date' => today()->toDateString(), 'amount' => $amount, 'method' => 'cash']);

        $pay(5000000)->assertRedirect("/panel/pembelian/{$purchase->id}");
        $this->assertSame(PaymentStatus::Partial, $purchase->fresh()->payment_status);

        $pay(3000000)->assertSessionHasErrors('amount');
        $this->assertSame(PaymentStatus::Partial, $purchase->fresh()->payment_status);

        $pay(2600000)->assertSessionHasNoErrors();
        $this->assertSame(PaymentStatus::Paid, $purchase->fresh()->payment_status);
    }

    public function test_purchase_validation_future_date_and_due_date(): void
    {
        $this->actingAs($this->userWithRole('staff'));
        $supplier = Supplier::create(['name' => 'Pak Tani']);

        $this->post('/panel/pembelian', $this->purchaseData(['supplier_id' => $supplier->id, 'purchase_date' => today()->addDay()->toDateString()]))
            ->assertSessionHasErrors('purchase_date');
        $this->post('/panel/pembelian', $this->purchaseData(['supplier_id' => $supplier->id, 'pay_now' => 0]))
            ->assertSessionHasErrors('due_date');
        $this->post('/panel/pembelian', $this->purchaseData())->assertSessionHasErrors('supplier_id');

        $this->assertSame(0, Purchase::count());
    }

    public function test_purchase_void_rules(): void
    {
        $owner = $this->userWithRole('owner');
        $staff = $this->userWithRole('staff');
        $this->actingAs($owner);
        $purchase = $this->buyGabah();

        // Staff tidak bisa void (akses URL langsung).
        $this->actingAs($staff)->post("/panel/pembelian/{$purchase->id}/batal", ['reason' => 'salah'])->assertForbidden();

        // Alasan wajib.
        $this->actingAs($owner)->post("/panel/pembelian/{$purchase->id}/batal", ['reason' => ''])->assertSessionHasErrors('reason');

        // Gabah sudah digiling → ditolak dengan pesan jelas.
        $this->millGabah();
        $this->from("/panel/pembelian/{$purchase->id}")->post("/panel/pembelian/{$purchase->id}/batal", ['reason' => 'salah input'])
            ->assertSessionHasErrors(['reason' => 'Pembelian tidak bisa dibatalkan: stok Gabah Ketan tinggal 0,00 kg, sedangkan pembelian ini 1.000,00 kg (gabah sudah terpakai untuk produksi).']);
        $this->assertSame(DocumentStatus::Posted, $purchase->fresh()->status);
    }

    public function test_purchase_void_reverses_stock_and_payments(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $purchase = $this->buyGabah();

        $this->post("/panel/pembelian/{$purchase->id}/batal", ['reason' => 'salah input'])
            ->assertRedirect("/panel/pembelian/{$purchase->id}")->assertSessionHas('sukses');

        $this->assertSame(DocumentStatus::Void, $purchase->fresh()->status);
        $this->assertSame('0.00', $this->item('GBH')->stock_kg);
        $this->assertSame(PaymentStatus::Unpaid, $purchase->fresh()->payment_status);
        $this->get("/panel/pembelian/{$purchase->id}")->assertSee('Dokumen ini dibatalkan')->assertSee('salah input');
    }

    // ---------- Produksi (PRD-04) ----------

    public function test_production_scenario_via_panel(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();

        $this->get('/panel/produksi/create')->assertOk()->assertSee('Beras Ketan Putih');
        $this->post('/panel/produksi', $this->productionData())->assertRedirect()->assertSessionMissing('peringatan');

        $production = Production::with('outputs')->firstOrFail();
        $this->assertSame('56.00', $production->yield_percent);
        $this->assertSame('100.00', $production->shrinkage_kg);
        $this->assertSame('13571.43', $production->outputs->firstWhere('item_id', $this->item('BKT')->id)->unit_cost);
        $this->assertSame('0.00', $this->item('GBH')->stock_kg);
        $this->assertSame('560.00', $this->item('BKT')->stock_kg);

        $this->get("/panel/produksi/{$production->id}")->assertOk()->assertSee('56,00 %')->assertSee('Rp 13.571,43');
    }

    public function test_production_rejections(): void
    {
        $this->actingAs($this->userWithRole('staff'));
        $this->buyGabah();

        // Total hasil > gabah.
        $this->post('/panel/produksi', $this->productionData(['BKT' => 900, 'SKM' => 200]))->assertSessionHasErrors('outputs');
        // Tanpa beras.
        $this->post('/panel/produksi', $this->productionData(['BKT' => 0]))->assertSessionHasErrors('outputs');
        // Gabah kurang → pesan, bukan error 500.
        $this->from('/panel/produksi/create')->post('/panel/produksi', $this->productionData([], 2000))
            ->assertRedirect('/panel/produksi/create')->assertSessionHas('gagal');

        $this->assertSame(0, Production::count());
        $this->assertSame('1000.00', $this->item('GBH')->stock_kg);
    }

    public function test_low_yield_saves_with_warning(): void
    {
        $this->actingAs($this->userWithRole('staff'));
        $this->buyGabah();

        $this->post('/panel/produksi', $this->productionData(['BKT' => 450, 'MNR' => 60, 'DDK' => 80, 'SKM' => 200]))
            ->assertRedirect()->assertSessionHas('peringatan');
        $this->assertSame('45.00', Production::firstOrFail()->yield_percent);
    }

    public function test_staff_does_not_see_production_cost(): void
    {
        $this->actingAs($this->userWithRole('staff'));
        $this->buyGabah();
        $production = $this->millGabah();

        $this->get("/panel/produksi/{$production->id}")->assertOk()
            ->assertDontSee('Biaya gabah')->assertDontSee('HPP')->assertDontSee('13.571');
        $this->post("/panel/produksi/{$production->id}/batal", ['reason' => 'x'])->assertForbidden();
    }

    public function test_production_void_rejected_when_rice_sold(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();
        $production = $this->millGabah();
        $this->sellRice();

        $this->post("/panel/produksi/{$production->id}/batal", ['reason' => 'salah'])->assertSessionHasErrors('reason');
        $this->assertSame(DocumentStatus::Posted, $production->fresh()->status);
    }

    // ---------- Penjualan (PRD-05) ----------

    public function test_sale_scenario_via_panel(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();
        $this->millGabah();
        $buyer = Buyer::create(['name' => 'Toko Berkah', 'city' => 'Cirebon', 'phone' => '081234567890']);

        $this->get('/panel/penjualan/create')->assertOk();
        $this->post('/panel/penjualan', $this->saleData(['buyer_id' => $buyer->id]))->assertRedirect();

        $sale = Sale::firstOrFail();
        $this->assertSame('5400000.00', $sale->subtotal);
        $this->assertSame('5350000.00', $sale->total);
        $this->assertSame('3392857.50', $sale->cogs_total);
        $this->assertSame('310.00', $this->item('BKT')->stock_kg);

        $this->get("/panel/penjualan/{$sale->id}")->assertOk()->assertSee('HPP')->assertSee('Laba kotor');
    }

    public function test_sale_over_stock_is_rejected_without_saving(): void
    {
        $this->actingAs($this->userWithRole('staff'));
        $this->buyGabah();
        $this->millGabah();

        $this->from('/panel/penjualan/create')
            ->post('/panel/penjualan', $this->saleData([
                'new_buyer_name' => 'Pembeli Baru',
                'items' => [['item_id' => $this->item('BKT')->id, 'input_quantity' => 1000, 'input_unit' => 'kg', 'price_per_kg' => 20000]],
            ]))
            ->assertRedirect('/panel/penjualan/create')
            ->assertSessionHas('gagal', fn ($m) => str_contains($m, 'tidak cukup'));

        $this->assertSame(0, Sale::count());
        $this->assertSame(0, Buyer::where('name', 'Pembeli Baru')->count()); // pembeli baru ikut di-rollback
        $this->assertSame('560.00', $this->item('BKT')->stock_kg);
    }

    public function test_sale_duplicate_items_rejected(): void
    {
        $this->actingAs($this->userWithRole('staff'));
        $bkt = $this->item('BKT')->id;

        $this->post('/panel/penjualan', $this->saleData([
            'new_buyer_name' => 'X',
            'items' => [
                ['item_id' => $bkt, 'input_quantity' => 1, 'input_unit' => 'kg', 'price_per_kg' => 1],
                ['item_id' => $bkt, 'input_quantity' => 1, 'input_unit' => 'kg', 'price_per_kg' => 1],
            ],
        ]))->assertSessionHasErrors('items.1.item_id');
    }

    public function test_staff_sees_only_own_sales_and_no_cogs(): void
    {
        $staff = $this->userWithRole('staff');
        $other = $this->userWithRole('staff');
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();
        $this->millGabah();

        $this->actingAs($other);
        $otherSale = $this->sellRice();
        $this->actingAs($staff);
        $ownSale = $this->sellRice(['items' => [['item_id' => $this->item('BKT')->id, 'input_quantity' => 10, 'input_unit' => 'kg', 'price_per_kg' => 20000]], 'discount' => 0]);

        $this->get('/panel/penjualan')->assertOk()->assertSee($ownSale->number)->assertDontSee($otherSale->number);
        $this->get("/panel/penjualan/{$otherSale->id}")->assertForbidden();
        $this->get("/panel/penjualan/{$ownSale->id}")->assertOk()->assertDontSee('HPP')->assertDontSee('Laba kotor');
        $this->post("/panel/penjualan/{$ownSale->id}/batal", ['reason' => 'x'])->assertForbidden();
    }

    public function test_sale_void_returns_stock(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();
        $this->millGabah();
        $sale = $this->sellRice();

        $this->post("/panel/penjualan/{$sale->id}/batal", ['reason' => 'pembeli batal'])->assertSessionHas('sukses');
        $this->assertSame(DocumentStatus::Void, $sale->fresh()->status);
        $this->assertSame('560.00', $this->item('BKT')->stock_kg);
    }

    public function test_payment_void_is_owner_only(): void
    {
        $owner = $this->userWithRole('owner');
        $this->actingAs($owner);
        $purchase = $this->buyGabah(); // bayar lunas Rp7.600.000
        $payment = $purchase->payments()->firstOrFail();

        $this->actingAs($this->userWithRole('staff'))->post("/panel/pembayaran/{$payment->id}/batal", ['reason' => 'x'])->assertForbidden();

        $this->actingAs($owner)->from("/panel/pembelian/{$purchase->id}")
            ->post("/panel/pembayaran/{$payment->id}/batal", ['reason' => 'salah catat'])
            ->assertRedirect("/panel/pembelian/{$purchase->id}");
        $this->assertSame(PaymentStatus::Unpaid, $purchase->fresh()->payment_status);
    }

    // ---------- Stok (PRD-02) ----------

    public function test_stock_card_hides_cost_from_staff(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();
        $this->get('/panel/kartu-stok')->assertOk()->assertSee('Biaya/kg')->assertSee('Rp 7.600,00');

        $this->actingAs($this->userWithRole('staff'));
        $this->get('/panel/kartu-stok')->assertOk()->assertSee('Gabah Ketan')->assertDontSee('Biaya/kg')->assertDontSee('7.600,00');
        $this->get('/panel/kartu-stok?direction=out')->assertOk()->assertSee('Belum ada mutasi stok.');
    }

    public function test_stock_opname_via_panel(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();

        $this->get('/panel/opname/create')->assertOk();
        $this->post('/panel/opname', [
            'adjustment_date' => today()->toDateString(), 'item_id' => $this->item('GBH')->id, 'physical_kg' => 990, 'reason' => 'Tikus',
        ])->assertRedirect();

        $adjustment = StockAdjustment::firstOrFail();
        $this->assertSame('-10.00', $adjustment->difference_kg);
        $this->assertSame('990.00', $this->item('GBH')->stock_kg);
        $this->assertSame('out', StockMovement::where('source_type', $adjustment->getMorphClass())->value('direction'));

        $this->post('/panel/opname', ['adjustment_date' => today()->toDateString(), 'item_id' => $this->item('GBH')->id, 'physical_kg' => 1])
            ->assertSessionHasErrors('reason');

        $this->post("/panel/opname/{$adjustment->id}/batal", ['reason' => 'hitung ulang'])->assertSessionHas('sukses');
        $this->assertSame('1000.00', $this->item('GBH')->stock_kg);
    }

    public function test_stock_opname_is_owner_only(): void
    {
        $this->actingAs($this->userWithRole('staff'));

        $this->get('/panel/opname')->assertForbidden();
        $this->get('/panel/opname/create')->assertForbidden();
        $this->post('/panel/opname', ['adjustment_date' => today()->toDateString(), 'item_id' => $this->item('GBH')->id, 'physical_kg' => 1, 'reason' => 'x'])
            ->assertForbidden();
    }

    // ---------- Utang & piutang ----------

    public function test_debt_pages_are_owner_only(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah(['pay_now' => 0, 'due_date' => today()->addDay()->toDateString()]);
        $this->millGabah();
        $sale = $this->sellRice();

        $this->get('/panel/utang')->assertOk()->assertSee('Pak Tani')->assertSee('Rp 7.600.000');
        $this->get('/panel/piutang')->assertOk()->assertSee($sale->number)->assertSee('Ingatkan via WA')->assertSee('wa.me/6281234567890', false);

        $this->actingAs($this->userWithRole('staff'));
        $this->get('/panel/utang')->assertForbidden();
        $this->get('/panel/piutang')->assertForbidden();
    }

    public function test_guest_redirected_from_transaction_pages(): void
    {
        foreach (['/panel/pembelian', '/panel/produksi', '/panel/penjualan', '/panel/kartu-stok', '/panel/opname', '/panel/utang', '/panel/piutang'] as $url) {
            $this->get($url)->assertRedirect('/panel/login');
        }
    }

    public function test_transaction_documents_have_no_edit_or_delete_routes(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $purchase = $this->buyGabah();

        $this->get("/panel/pembelian/{$purchase->id}/edit")->assertNotFound();
        $this->delete("/panel/pembelian/{$purchase->id}")->assertMethodNotAllowed();
        $this->put("/panel/pembelian/{$purchase->id}", [])->assertMethodNotAllowed();
    }

    private function purchaseData(array $override = []): array
    {
        // Skenario PRD-03: 40 karung (@25 kg) × Rp7.500 + ongkos Rp100.000 → 1.000 kg, Rp7.600.000.
        return array_merge([
            'purchase_date' => today()->toDateString(),
            'item_id' => $this->item('GBH')->id,
            'input_quantity' => 40,
            'input_unit' => 'sack',
            'price_per_kg' => 7500,
            'other_cost' => 100000,
            'pay_now' => 7600000,
            'payment_method' => 'cash',
        ], $override);
    }

    private function productionData(array $outputs = [], float $inputKg = 1000): array
    {
        $outputs = array_merge(['BKT' => 560, 'MNR' => 60, 'DDK' => 80, 'SKM' => 200], $outputs);

        return [
            'production_date' => today()->toDateString(),
            'input_item_id' => $this->item('GBH')->id,
            'input_kg' => $inputKg,
            'outputs' => collect($outputs)->mapWithKeys(fn ($kg, $code) => [$this->item($code)->id => $kg])->all(),
        ];
    }

    private function saleData(array $override = []): array
    {
        // Skenario PRD-05: 10 karung beras @Rp20.000 + menir 50 kg @Rp8.000, diskon Rp50.000.
        return array_merge([
            'sale_date' => today()->toDateString(),
            'items' => [
                ['item_id' => $this->item('BKT')->id, 'input_quantity' => 10, 'input_unit' => 'sack', 'price_per_kg' => 20000],
                ['item_id' => $this->item('MNR')->id, 'input_quantity' => 50, 'input_unit' => 'kg', 'price_per_kg' => 8000],
            ],
            'discount' => 50000,
            'shipping_cost' => 0,
            'pay_now' => 0,
            'payment_method' => 'cash',
            'due_date' => today()->addDays(14)->toDateString(),
        ], $override);
    }
}
