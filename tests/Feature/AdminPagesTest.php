<?php

namespace Tests\Feature;

use App\Models\Item;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Smoke test: halaman admin bisa dirender tanpa error, sesuai role.
// PRD-08 Fase 7: dulu /admin (Filament), kini /panel. Menu Peran (Shield) dihapus sesuai PRD.
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
        $item = Item::first()->id;

        foreach ([
            '/panel', '/panel/barang', '/panel/barang/create', "/panel/barang/{$item}", "/panel/barang/{$item}/edit",
            '/panel/kartu-stok', '/panel/opname', '/panel/opname/create',
            '/panel/petani', '/panel/petani/create', '/panel/pembeli', '/panel/pembeli/create',
            '/panel/pengguna', '/panel/pengguna/create',
            '/panel/pembelian', '/panel/produksi', '/panel/penjualan', '/panel/biaya', '/panel/biaya/create',
            '/panel/utang', '/panel/piutang', '/panel/laporan',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_staff_pages_and_hidden_cost(): void
    {
        $this->actingAs($this->userWithRole('staff'));

        $this->get('/panel/barang')->assertOk()->assertDontSee('Biaya rata-rata');
        $this->get('/panel/barang/'.Item::first()->id)->assertOk()->assertDontSee('Biaya rata-rata'); // id tidak selalu 1 di MySQL
        $this->get('/panel/kartu-stok')->assertOk()->assertDontSee('Biaya/kg');
        $this->get('/panel/barang/create')->assertForbidden();
    }
}
