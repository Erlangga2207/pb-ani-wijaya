<?php

namespace Tests\Feature;

use Database\Seeders\ItemSeeder;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// PRD-08 Fase 7: halaman laporan & widget dashboard dulu di Filament (/admin), kini /panel. Angka yang dicek sama.
class ReportAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([ItemSeeder::class, SiteSettingSeeder::class]);
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();
        $this->millGabah();
        $this->sellRice();
    }

    public function test_owner_sees_all_tabs_print_and_csv(): void
    {
        foreach (['pnl', 'sales', 'purchases', 'yield', 'stock', 'aging'] as $tab) {
            $this->get("/panel/laporan?tab={$tab}")->assertOk();
            $this->get("/laporan/{$tab}/cetak")->assertOk();
            $this->get("/laporan/{$tab}/csv")->assertOk();
        }

        $this->get('/panel/laporan?tab=pnl')->assertSee('LABA BERSIH')->assertSee('1.957.142,50');
    }

    public function test_dashboard_per_role(): void
    {
        $this->get('/panel')->assertOk()->assertSee('Rp 5.350.000');

        $this->actingAs($this->userWithRole('staff'));
        $this->get('/panel')->assertOk()->assertSee('310 kg')->assertDontSee('Rp ');
    }

    public function test_csv_is_excel_friendly(): void
    {
        $csv = $this->get('/laporan/pnl/csv')->streamedContent();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $this->assertStringContainsString('Keterangan;Jumlah', $csv);
        $this->assertStringContainsString('"LABA KOTOR";1957142,50', $csv);
    }

    public function test_staff_only_gets_stock_and_yield_without_money(): void
    {
        $this->actingAs($this->userWithRole('staff'));

        foreach (['pnl', 'sales', 'purchases', 'aging'] as $tab) {
            $this->get("/laporan/{$tab}/cetak")->assertForbidden();
            $this->get("/laporan/{$tab}/csv")->assertForbidden();
            $this->get("/panel/laporan?tab={$tab}")->assertForbidden(); // Filament: 200 tanpa angka; /panel: 403 (PRD-08 B)
        }

        $this->get('/panel/laporan?tab=stock')->assertOk()->assertSee('Beras Ketan Putih')->assertDontSee('Nilai persediaan');
        $this->get('/laporan/stock/csv')->assertOk();
        $this->get('/laporan/yield/cetak')->assertOk()->assertSee('56,00 %');
    }
}
