<?php

namespace Tests\Feature;

use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_void_and_sees_no_cogs(): void
    {
        $this->seed(ItemSeeder::class);
        $staff = $this->userWithRole('staff');
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();
        $this->millGabah();

        $this->actingAs($staff);
        $sale = $this->sellRice();

        $this->assertFalse($staff->can('void', $sale));
        $this->assertFalse($staff->can('update', $sale));
        $this->assertFalse($staff->can('delete', $sale));

        $this->get('/panel/penjualan')->assertOk()->assertSee($sale->number);
        $this->get('/panel/penjualan/create')->assertOk();
        $this->get("/panel/penjualan/{$sale->id}")->assertOk()->assertDontSee('HPP')->assertDontSee('Laba kotor');
        $this->get('/panel/piutang')->assertForbidden();

        $owner = $this->userWithRole('owner');
        $this->assertTrue($owner->can('void', $sale));
        $this->actingAs($owner)->get("/panel/penjualan/{$sale->id}")->assertOk()->assertSee('HPP')->assertSee('Laba kotor');
        $this->get('/panel/piutang')->assertOk()->assertSee($sale->number);
    }
}
