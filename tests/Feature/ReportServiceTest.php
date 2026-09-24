<?php

namespace Tests\Feature;

use App\Services\ExpenseService;
use App\Services\ReportService;
use App\Services\SaleService;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ItemSeeder::class);
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();
        $this->millGabah();
    }

    private function electricity(float $amount = 500000)
    {
        return app(ExpenseService::class)->create([
            'expense_date' => today()->toDateString(),
            'category' => 'electricity',
            'description' => 'Listrik bulan ini',
            'amount' => $amount,
        ], auth()->user());
    }

    public function test_profit_and_loss_scenario_from_prd(): void
    {
        $this->sellRice();
        $this->electricity();

        $pnl = app(ReportService::class)->profitAndLoss(today()->startOfMonth(), today());

        $this->assertEquals(5350000, $pnl['revenue']);
        $this->assertEquals(3392857.50, $pnl['cogs']);
        $this->assertEquals(1957142.50, $pnl['gross_profit']);
        $this->assertEquals(500000, $pnl['expenses']['Listrik']);
        $this->assertEquals(1457142.50, $pnl['net_profit']);
    }

    public function test_void_documents_are_excluded(): void
    {
        $sale = $this->sellRice();
        $expense = $this->electricity();
        app(SaleService::class)->void($sale, 'batal', auth()->user());
        app(ExpenseService::class)->void($expense, 'dobel', auth()->user());

        $pnl = app(ReportService::class)->profitAndLoss(today()->startOfMonth(), today());

        $this->assertEquals(0, $pnl['revenue']);
        $this->assertEquals(0, $pnl['total_expenses']);
        $this->assertSame([], app(ReportService::class)->salesSummary(today()->startOfMonth(), today())['per_item']->all());
    }

    public function test_weighted_yield_is_not_simple_average(): void
    {
        // Batch 1: 1.000 kg -> 560 (56%). Batch 2: 100 kg -> 40 (40%).
        $this->buyGabah(['input_quantity' => 100, 'input_unit' => 'kg', 'other_cost' => 0, 'pay_now' => 750000]);
        $this->millGabah(['BKT' => 40], 100);

        $yield = app(ReportService::class)->yieldSummary(today()->startOfMonth(), today());

        // (560 + 40) / (1000 + 100) = 54,55% — rata-rata biasa akan 48%.
        $this->assertEquals(54.55, $yield['weighted_yield']);
        $this->assertCount(2, $yield['batches']);
    }

    public function test_aging_buckets(): void
    {
        $this->travelTo(today()->subDays(45));
        $this->sellRice(['due_date' => today()->addDays(5)->toDateString()]); // lewat ~40 hari
        $this->travelBack();

        $this->sellRice([
            'items' => [['item_id' => $this->item('BKT')->id, 'input_quantity' => 10, 'input_unit' => 'kg', 'price_per_kg' => 20000]],
            'discount' => 0,
            'due_date' => today()->addDays(7)->toDateString(),
        ]);

        $aging = app(ReportService::class)->agingSummary(today());

        $this->assertEquals(200000, $aging['receivables']['current']['amount']);
        $this->assertEquals(5350000, $aging['receivables']['d31_60']['amount']);
        $this->assertEquals(0, $aging['payables']['total']); // pembelian sudah lunas
    }

    public function test_stock_summary_from_ledger(): void
    {
        $stock = app(ReportService::class)->stockSummary(today());
        $beras = $stock['items']->firstWhere('code', 'BKT');

        $this->assertEquals(560, $beras['balance_kg']);
        $this->assertEquals(7600000.80, $stock['total_value']); // 560 × 13.571,43 (pembulatan HPP)
    }
}
