<?php

namespace Tests\Feature;

use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Smoke test: halaman admin bisa dirender tanpa error, sesuai role.
class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ItemSeeder::class);
    }

    public function test_owner_pages_render(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        foreach ([
            '/admin', '/admin/items', '/admin/items/create', '/admin/items/1', '/admin/items/1/edit',
            '/admin/stock-movements', '/admin/stock-adjustments', '/admin/stock-adjustments/create',
            '/admin/suppliers', '/admin/suppliers/create', '/admin/buyers', '/admin/buyers/create',
            '/admin/users', '/admin/users/create',
            '/admin/purchases', '/admin/productions', '/admin/sales', '/admin/expenses', '/admin/expenses/create',
            '/admin/payables', '/admin/receivables', '/admin/reports',
            // Modul Website pindah ke /panel di PRD-08 Fase 6 (diuji di Admin\WebsiteModuleTest).
        ] as $url) {
            $this->get($url)->assertOk();
        }

        // Menu Roles (Shield) hanya super_admin.
        $this->get('/admin/shield/roles')->assertForbidden();
        $this->actingAs($this->userWithRole('super_admin'))->get('/admin/shield/roles')->assertOk();
    }

    public function test_staff_pages_and_hidden_cost(): void
    {
        $this->actingAs($this->userWithRole('staff'));

        $this->get('/admin/items')->assertOk()->assertDontSee('Biaya Rata-rata');
        $this->get('/admin/items/1')->assertOk()->assertDontSee('Biaya Rata-rata');
        $this->get('/admin/stock-movements')->assertOk()->assertDontSee('Biaya/kg');
        $this->get('/admin/items/create')->assertForbidden();
    }
}
