<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientStockException;
use App\Models\Item;
use App\Models\StockMovement;
use App\Services\StockService;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use LogicException;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockService $stock;

    private Item $gabah;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ItemSeeder::class);
        $this->stock = app(StockService::class);
        $this->gabah = Item::where('code', 'GBH')->first();
        $this->actingAs($this->userWithRole('owner'));
    }

    public function test_increase_and_decrease_update_balance_and_ledger(): void
    {
        $source = auth()->user();

        DB::transaction(fn () => $this->stock->increase($this->gabah, 1000, 7000, $source, today()));
        $out = DB::transaction(fn () => $this->stock->decrease($this->gabah, 300, $source, today()));

        $this->assertEquals(700, (float) $this->gabah->fresh()->stock_kg);
        $this->assertEquals(700, (float) $out->balance_after_kg);
        $this->assertEquals(7000, (float) $out->unit_cost);
        $this->assertSame(2, StockMovement::count());
    }

    public function test_moving_average_cost(): void
    {
        $source = auth()->user();

        DB::transaction(fn () => $this->stock->increase($this->gabah, 1000, 7000, $source, today()));
        DB::transaction(fn () => $this->stock->increase($this->gabah, 500, 8000, $source, today()));

        $this->assertEquals(7333.33, (float) $this->gabah->fresh()->avg_cost_per_kg);
    }

    public function test_decrease_more_than_stock_throws_and_rolls_back(): void
    {
        $source = auth()->user();
        DB::transaction(fn () => $this->stock->increase($this->gabah, 100, 7000, $source, today()));

        try {
            DB::transaction(function () use ($source) {
                $this->stock->decrease($this->gabah, 40, $source, today());
                $this->stock->decrease($this->gabah, 150, $source, today());
            });
            $this->fail('Exception expected');
        } catch (InsufficientStockException $e) {
            $this->assertStringContainsString('Stok Gabah Ketan tidak cukup', $e->getMessage());
        }

        // Mutasi pertama (40 kg) ikut dibatalkan karena satu transaksi.
        $this->assertEquals(100, (float) $this->gabah->fresh()->stock_kg);
        $this->assertSame(1, StockMovement::count());
    }

    public function test_reverse_restores_balance(): void
    {
        $source = auth()->user();
        DB::transaction(fn () => $this->stock->increase($this->gabah, 1000, 7000, $source, today()));
        DB::transaction(fn () => $this->stock->reverse($source, 'salah input'));

        $this->assertEquals(0, (float) $this->gabah->fresh()->stock_kg);
        $this->assertSame(2, StockMovement::count());
        $this->artisan('stock:verify')->assertExitCode(0);
    }

    public function test_ledger_rows_are_immutable(): void
    {
        $movement = DB::transaction(fn () => $this->stock->increase($this->gabah, 10, 7000, auth()->user(), today()));

        $this->expectException(LogicException::class);
        $movement->delete();
    }
}
