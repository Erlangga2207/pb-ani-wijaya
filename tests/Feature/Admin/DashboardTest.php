<?php

namespace Tests\Feature\Admin;

use App\Services\DashboardService;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ItemSeeder::class);
    }

    public function test_owner_sees_finance_section(): void
    {
        $this->actingAs($this->userWithRole('owner'))->get('/panel')
            ->assertOk()
            ->assertSee('Gabah Ketan')
            ->assertSee('Laba Bersih')
            ->assertSee('Lewat Jatuh Tempo');
    }

    public function test_staff_does_not_see_or_query_finance(): void
    {
        // Data keuangan tidak boleh di-query untuk staff (bukan cuma disembunyikan di Blade).
        $this->partialMock(DashboardService::class, function ($mock) {
            $mock->shouldNotReceive('finance', 'salesTrend', 'overdue');
        });

        $this->actingAs($this->userWithRole('staff'))->get('/panel')
            ->assertOk()
            ->assertSee('Aktivitas hari ini')
            ->assertDontSee('Laba Bersih')
            ->assertDontSee('Omzet')
            ->assertDontSee('Utang ke Petani');
    }

    public function test_sidebar_follows_role(): void
    {
        $this->actingAs($this->userWithRole('staff'))->get('/panel')
            ->assertSee('Dashboard')
            ->assertSee('Panel lama');
    }

    public function test_today_activity_and_finance_numbers(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();   // 1.000 kg
        $this->millGabah();  // giling 1.000 kg
        $this->sellRice();   // 250 kg beras (menir bukan barang jadi)

        $service = app(DashboardService::class);

        $this->assertSame(['bought' => 1000.0, 'milled' => 1000.0, 'sold' => 250.0], $service->todayActivity());
        $this->assertCount(1, $service->yieldTrend()['values']);
        $this->assertSame(5350000.0, $service->finance()['revenue']); // 250×20.000 + 50×8.000 − 50.000
        $this->assertSame(5350000.0, $service->finance()['receivables']);
    }
}
