<?php

namespace Tests;

use App\Models\Item;
use App\Models\Production;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use App\Services\ProductionService;
use App\Services\PurchaseService;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (method_exists($this, 'refreshDatabase')) {
            $this->seed(RoleSeeder::class);
        }

        Filament::setCurrentPanel('admin');
    }

    protected function userWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }

    protected function item(string $code): Item
    {
        return Item::where('code', $code)->firstOrFail();
    }

    // Skenario PRD-03: beli 40 karung (@25 kg) × Rp7.500 + ongkos Rp100.000 -> 1.000 kg @Rp7.600.
    protected function buyGabah(array $override = []): Purchase
    {
        return app(PurchaseService::class)->create(array_merge([
            'supplier_id' => Supplier::firstOrCreate(['name' => 'Pak Tani'], ['village' => 'Losarang'])->id,
            'item_id' => $this->item('GBH')->id,
            'purchase_date' => today()->toDateString(),
            'input_quantity' => 40,
            'input_unit' => 'sack',
            'price_per_kg' => 7500,
            'other_cost' => 100000,
            'pay_now' => 7600000,
        ], $override), auth()->user());
    }

    // Skenario PRD-04: giling 1.000 kg -> beras 560, menir 60, dedak 80, sekam 200.
    protected function millGabah(array $outputs = [], float $inputKg = 1000): Production
    {
        $outputs = $outputs ?: ['BKT' => 560, 'MNR' => 60, 'DDK' => 80, 'SKM' => 200];

        return app(ProductionService::class)->create([
            'production_date' => today()->toDateString(),
            'input_item_id' => $this->item('GBH')->id,
            'input_kg' => $inputKg,
            'outputs' => collect($outputs)->mapWithKeys(fn ($kg, $code) => [$this->item($code)->id => $kg])->all(),
        ], auth()->user());
    }
}
