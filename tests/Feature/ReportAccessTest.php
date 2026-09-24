<?php

namespace Tests\Feature;

use App\Filament\Admin\Widgets\FinanceStatsWidget;
use App\Filament\Admin\Widgets\LowStockAlertWidget;
use App\Filament\Admin\Widgets\OverdueWidget;
use App\Filament\Admin\Widgets\SalesTrendChart;
use App\Filament\Admin\Widgets\StockStatsWidget;
use App\Filament\Admin\Widgets\TodayActivityWidget;
use App\Filament\Admin\Widgets\YieldTrendChart;
use Database\Seeders\ItemSeeder;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

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
            $this->get("/admin/reports?tab={$tab}")->assertOk();
            $this->get("/laporan/{$tab}/cetak")->assertOk();
            $this->get("/laporan/{$tab}/csv")->assertOk();
        }

        $this->get('/admin/reports?tab=pnl')->assertSee('LABA BERSIH')->assertSee('1.957.142,50');
    }

    public function test_dashboard_widgets_render_per_role(): void
    {
        $this->get('/admin')->assertOk();
        $this->assertTrue(FinanceStatsWidget::canView());
        Livewire::test(FinanceStatsWidget::class)->assertOk()->assertSee('Rp 5.350.000');
        foreach ([StockStatsWidget::class, TodayActivityWidget::class, YieldTrendChart::class, SalesTrendChart::class, OverdueWidget::class, LowStockAlertWidget::class] as $widget) {
            Livewire::test($widget)->assertOk();
        }

        $this->actingAs($this->userWithRole('staff'));
        $this->get('/admin')->assertOk();
        foreach ([FinanceStatsWidget::class, SalesTrendChart::class, OverdueWidget::class] as $widget) {
            $this->assertFalse($widget::canView());
        }
        Livewire::test(StockStatsWidget::class)->assertOk()->assertSee('310 kg')->assertDontSee('Rp ');
        Livewire::test(TodayActivityWidget::class)->assertOk()->assertDontSee('Rp ');
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
            $this->get("/admin/reports?tab={$tab}")->assertOk()->assertDontSee('LABA');
        }

        $this->get('/admin/reports?tab=stock')->assertOk()->assertSee('Beras Ketan Putih')->assertDontSee('Nilai persediaan');
        $this->get('/laporan/stock/csv')->assertOk();
        $this->get('/laporan/yield/cetak')->assertOk()->assertSee('56,00 %');
    }
}
