<?php

namespace Tests\Feature\Admin;

use App\Enums\DocumentStatus;
use App\Models\Expense;
use App\Services\ReportService;
use Database\Seeders\ItemSeeder;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([ItemSeeder::class, SiteSettingSeeder::class]);
    }

    // ---------- Biaya operasional ----------

    public function test_owner_records_and_voids_expense(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        $this->get('/panel/biaya/create')->assertOk();
        $this->post('/panel/biaya', $this->expenseData())->assertRedirect('/panel/biaya')->assertSessionHas('sukses');

        $expense = Expense::firstOrFail();
        $this->assertSame('150000.00', $expense->amount);
        $this->get('/panel/biaya')->assertOk()->assertSee($expense->number)->assertSee('Rp 150.000');
        $this->get("/panel/biaya/{$expense->id}")->assertOk()->assertSee('Solar mesin');

        $this->post("/panel/biaya/{$expense->id}/batal", ['reason' => ''])->assertSessionHasErrors('reason');
        $this->post("/panel/biaya/{$expense->id}/batal", ['reason' => 'dobel'])->assertSessionHas('sukses');
        $this->assertSame(DocumentStatus::Void, $expense->fresh()->status);
        $this->get('/panel/biaya')->assertSee('Total tercatat: <strong class="angka text-body">Rp 0</strong>', false);
    }

    public function test_expense_validation(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        $this->post('/panel/biaya', $this->expenseData(['expense_date' => today()->addDay()->toDateString(), 'amount' => 0, 'category' => 'salah']))
            ->assertSessionHasErrors(['expense_date', 'amount', 'category']);
        $this->assertSame(0, Expense::count());
    }

    public function test_staff_cannot_access_expenses(): void
    {
        $owner = $this->userWithRole('owner');
        $this->actingAs($owner);
        $this->post('/panel/biaya', $this->expenseData());
        $expense = Expense::firstOrFail();

        $this->actingAs($this->userWithRole('staff'));
        $this->get('/panel/biaya')->assertForbidden();
        $this->get('/panel/biaya/create')->assertForbidden();
        $this->post('/panel/biaya', $this->expenseData())->assertForbidden();
        $this->get("/panel/biaya/{$expense->id}")->assertForbidden();
        $this->post("/panel/biaya/{$expense->id}/batal", ['reason' => 'x'])->assertForbidden();
    }

    // ---------- Laporan ----------

    // Fase 4 membandingkan angka tiap tab dengan Filament (identik). Filament dihapus di Fase 7; angka kunci dicek di ReportAccessTest.
    public function test_report_tabs_show_numbers(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();
        $this->millGabah();
        $this->sellRice();
        $this->post('/panel/biaya', $this->expenseData());

        foreach (array_keys(ReportService::TABS) as $tab) {
            $panel = $this->get("/panel/laporan?tab={$tab}")->assertOk()->getContent();
            $this->assertNotEmpty($this->cells($panel), "Tab {$tab} kosong");
        }

        $this->get('/panel/laporan?tab=pnl')->assertSee('LABA BERSIH')
            ->assertSee(route('reports.print', ['tab' => 'pnl', 'preset' => 'this_month', 'from' => today()->startOfMonth()->toDateString(), 'to' => today()->toDateString()]))
            ->assertSee(route('reports.csv', ['tab' => 'pnl', 'preset' => 'this_month', 'from' => today()->startOfMonth()->toDateString(), 'to' => today()->toDateString()]));
    }

    public function test_report_custom_period_and_presets(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();

        $this->get('/panel/laporan?tab=purchases&preset=custom&from=2020-01-01&to=2020-01-31')
            ->assertOk()->assertSee('1 Jan 2020')->assertSee('Tidak ada data pada periode ini.');
        $this->get('/panel/laporan?tab=purchases&preset=bukan-preset')->assertOk(); // jatuh ke "Bulan ini"
    }

    public function test_staff_only_sees_stock_and_yield_reports(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();
        $this->millGabah();

        $this->actingAs($this->userWithRole('staff'));
        $this->get('/panel/laporan')->assertOk()->assertSee('Rendemen')->assertDontSee('Laba Rugi')->assertDontSee('LABA');
        $this->get('/panel/laporan?tab=yield')->assertOk()->assertSee('56,00 %');
        $this->get('/panel/laporan?tab=stock')->assertOk()->assertSee('Beras Ketan Putih')->assertDontSee('Nilai persediaan');

        foreach (['pnl', 'sales', 'purchases', 'aging', 'tidak-ada'] as $tab) {
            $this->get("/panel/laporan?tab={$tab}")->assertForbidden();
        }
    }

    // Isi semua <td> di tabel laporan (partial reports.sections), spasi dirapikan.
    private function cells(string $html): array
    {
        preg_match_all('#<table class="lines".*?</table>#s', $html, $tables);
        preg_match_all('#<td[^>]*>(.*?)</td>#s', implode('', $tables[0]), $cells);

        return array_map(fn ($c) => trim(preg_replace('/\s+/', ' ', strip_tags($c))), $cells[1]);
    }

    private function expenseData(array $override = []): array
    {
        return array_merge([
            'expense_date' => today()->toDateString(),
            'category' => 'fuel',
            'description' => 'Solar mesin 20 liter',
            'amount' => 150000,
            'method' => 'cash',
        ], $override);
    }
}
