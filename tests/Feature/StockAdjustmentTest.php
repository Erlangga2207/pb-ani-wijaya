<?php

namespace Tests\Feature;

use App\Enums\DocumentStatus;
use App\Models\Item;
use App\Models\StockAdjustment;
use App\Services\StockAdjustmentService;
use App\Services\StockService;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StockAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    private Item $gabah;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ItemSeeder::class);
        $this->gabah = Item::where('code', 'GBH')->first();
        $owner = $this->userWithRole('owner');
        $this->actingAs($owner);
        DB::transaction(fn () => app(StockService::class)->increase($this->gabah, 1000, 7000, $owner, today()));
    }

    public function test_physical_less_than_system_creates_out_movement(): void
    {
        $adj = app(StockAdjustmentService::class)->create(
            ['item_id' => $this->gabah->id, 'physical_kg' => 950, 'reason' => 'susut timbangan'],
            auth()->user(),
        );

        $this->assertEquals(-50, (float) $adj->difference_kg);
        $this->assertEquals(950, (float) $this->gabah->fresh()->stock_kg);
        $this->assertSame('out', $adj->stockMovements()->first()->direction);
    }

    public function test_physical_more_than_system_creates_in_movement_and_keeps_avg(): void
    {
        $adj = app(StockAdjustmentService::class)->create(
            ['item_id' => $this->gabah->id, 'physical_kg' => 1020, 'reason' => 'salah hitung'],
            auth()->user(),
        );

        $this->assertEquals(20, (float) $adj->difference_kg);
        $this->assertEquals(1020, (float) $this->gabah->fresh()->stock_kg);
        $this->assertEquals(7000, (float) $this->gabah->fresh()->avg_cost_per_kg);
    }

    public function test_reason_is_required(): void
    {
        $this->expectException(ValidationException::class);

        app(StockAdjustmentService::class)->create(
            ['item_id' => $this->gabah->id, 'physical_kg' => 900, 'reason' => '  '],
            auth()->user(),
        );
    }

    public function test_void_reverses_stock(): void
    {
        $service = app(StockAdjustmentService::class);
        $adj = $service->create(['item_id' => $this->gabah->id, 'physical_kg' => 900, 'reason' => 'x'], auth()->user());

        $service->void($adj, 'salah item', auth()->user());

        $this->assertSame(DocumentStatus::Void, $adj->fresh()->status);
        $this->assertEquals(1000, (float) $this->gabah->fresh()->stock_kg);
    }

    // PRD-08 Fase 7: dulu lewat form & aksi void Filament, kini /panel/opname. Data & assertion sama.
    public function test_owner_creates_and_voids_via_panel(): void
    {
        $this->post('/panel/opname', ['adjustment_date' => today()->toDateString(), 'item_id' => $this->gabah->id, 'physical_kg' => 980, 'reason' => 'opname bulanan'])
            ->assertSessionHasNoErrors();

        $adj = StockAdjustment::first();
        $this->assertEquals(980, (float) $this->gabah->fresh()->stock_kg);

        $this->post("/panel/opname/{$adj->id}/batal", ['reason' => 'salah hitung'])->assertSessionHasNoErrors();

        $this->assertTrue($adj->fresh()->isVoid());
        $this->assertEquals(1000, (float) $this->gabah->fresh()->stock_kg);
    }

    public function test_staff_cannot_access_stock_opname(): void
    {
        $this->actingAs($this->userWithRole('staff'));

        $this->get('/panel/opname')->assertForbidden();
        $this->get('/panel/opname/create')->assertForbidden();
    }
}
