<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientStockException;
use App\Models\Production;
use App\Services\ProductionService;
use App\Services\StockService;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProductionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ItemSeeder::class);
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah(); // 1.000 kg @Rp7.600
    }

    public function test_scenario_from_prd(): void
    {
        $p = $this->millGabah();

        $this->assertMatchesRegularExpression('/^PR-\d{8}-0001$/', $p->number);
        $this->assertEquals(56.00, (float) $p->yield_percent);
        $this->assertEquals(100, (float) $p->shrinkage_kg);
        $this->assertEquals(7600000, (float) $p->input_total_cost);
        $this->assertFalse($p->isYieldOutOfRange());

        $this->assertEquals(0, (float) $this->item('GBH')->stock_kg);
        $this->assertEquals(560, (float) $this->item('BKT')->stock_kg);
        $this->assertEquals(13571.43, (float) $this->item('BKT')->avg_cost_per_kg);
        $this->assertEquals(0, (float) $this->item('MNR')->avg_cost_per_kg);
        $this->assertEquals(200, (float) $this->item('SKM')->stock_kg);
        $this->assertSame(4, $p->outputs()->count());
    }

    public function test_zero_byproduct_rows_are_not_saved(): void
    {
        $p = $this->millGabah(['BKT' => 560, 'MNR' => 0, 'DDK' => 80]);

        $this->assertSame(2, $p->outputs()->count());
    }

    public function test_output_heavier_than_input_is_rejected(): void
    {
        $this->expectException(ValidationException::class);
        $this->millGabah(['BKT' => 900, 'SKM' => 200]);
    }

    public function test_without_rice_is_rejected(): void
    {
        $this->expectException(ValidationException::class);
        $this->millGabah(['MNR' => 60, 'SKM' => 200]);
    }

    public function test_insufficient_gabah_is_rejected_without_saving(): void
    {
        try {
            $this->millGabah(['BKT' => 600], 1200);
            $this->fail('Exception expected');
        } catch (InsufficientStockException) {
        }

        $this->assertSame(0, Production::count());
        $this->assertEquals(1000, (float) $this->item('GBH')->stock_kg);
    }

    public function test_low_yield_is_saved_with_warning_flag(): void
    {
        $p = $this->millGabah(['BKT' => 450, 'SKM' => 200]);

        $this->assertEquals(45, (float) $p->yield_percent);
        $this->assertTrue($p->isYieldOutOfRange());
    }

    public function test_void_rejected_when_rice_already_sold(): void
    {
        $p = $this->millGabah();
        DB::transaction(fn () => app(StockService::class)->decrease($this->item('BKT'), 100, auth()->user(), today()));

        $this->expectException(ValidationException::class);
        app(ProductionService::class)->void($p, 'salah', auth()->user());
    }

    public function test_void_restores_gabah(): void
    {
        $p = $this->millGabah();

        app(ProductionService::class)->void($p, 'salah timbang', auth()->user());

        $this->assertTrue($p->fresh()->isVoid());
        $this->assertEquals(1000, (float) $this->item('GBH')->stock_kg);
        $this->assertEquals(7600, (float) $this->item('GBH')->avg_cost_per_kg);
        $this->assertEquals(0, (float) $this->item('BKT')->stock_kg);
        $this->artisan('stock:verify')->assertExitCode(0);
    }

    // PRD-08 Fase 7: dulu lewat form & halaman Filament, kini /panel/produksi. Data & assertion sama.
    public function test_staff_creates_via_form_without_seeing_cost(): void
    {
        $this->actingAs($this->userWithRole('staff'));

        $this->post('/panel/produksi', [
            'production_date' => today()->toDateString(),
            'input_item_id' => $this->item('GBH')->id,
            'input_kg' => 1000,
            'outputs' => [$this->item('BKT')->id => 560, $this->item('SKM')->id => 200],
        ])->assertSessionHasNoErrors();

        $p = Production::first();
        $this->assertEquals(56, (float) $p->yield_percent);

        $this->get('/panel/produksi')->assertOk();
        $this->get('/panel/produksi/create')->assertOk();
        $this->get("/panel/produksi/{$p->id}")->assertOk()->assertDontSee('HPP')->assertDontSee('Biaya gabah');

        $this->actingAs($this->userWithRole('owner'));
        $this->get("/panel/produksi/{$p->id}")->assertOk()->assertSee('HPP');
    }
}
