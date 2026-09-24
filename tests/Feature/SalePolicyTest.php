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

        $this->get('/admin/sales')->assertOk()->assertSee($sale->number);
        $this->get('/admin/sales/create')->assertOk();
        $this->get("/admin/sales/{$sale->id}")->assertOk()->assertDontSee('HPP')->assertDontSee('Laba Kotor');
        $this->get('/admin/receivables')->assertForbidden();

        $owner = $this->userWithRole('owner');
        $this->assertTrue($owner->can('void', $sale));
        $this->actingAs($owner)->get("/admin/sales/{$sale->id}")->assertOk()->assertSee('HPP')->assertSee('Laba Kotor');
        $this->get('/admin/receivables')->assertOk()->assertSee($sale->number);
    }
}
