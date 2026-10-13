<?php

namespace Tests\Feature\Admin;

use App\Models\Buyer;
use App\Models\Item;
use App\Models\Supplier;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ItemSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        foreach (['/panel/barang', '/panel/petani', '/panel/pembeli', '/panel/pengguna'] as $url) {
            $this->get($url)->assertRedirect('/panel/login');
        }
    }

    public function test_owner_can_open_all_pages(): void
    {
        $owner = $this->userWithRole('owner');
        $this->actingAs($owner);
        $gabah = $this->item('GBH');
        $supplier = Supplier::create(['name' => 'Pak Udin']);
        $buyer = Buyer::create(['name' => 'Toko Ani']);

        foreach ([
            '/panel/barang', '/panel/barang/create', "/panel/barang/{$gabah->id}", "/panel/barang/{$gabah->id}/edit",
            '/panel/petani', '/panel/petani/create', "/panel/petani/{$supplier->id}/edit",
            '/panel/pembeli', '/panel/pembeli/create', "/panel/pembeli/{$buyer->id}/edit",
            '/panel/pengguna', '/panel/pengguna/create', "/panel/pengguna/{$owner->id}/edit",
        ] as $url) {
            $this->get($url)->assertOk();
        }

        // Menu master data & pengguna muncul untuk owner.
        $this->get('/panel')->assertSee('Petani / Pemasok')->assertSee('Pengguna');
    }

    public function test_staff_can_view_items_without_cost(): void
    {
        $this->actingAs($this->userWithRole('staff'));
        $gabah = $this->item('GBH');

        $this->get('/panel/barang')->assertOk()->assertSee('Gabah Ketan')->assertDontSee('Biaya rata-rata');
        $this->get("/panel/barang/{$gabah->id}")->assertOk()->assertDontSee('Biaya rata-rata');
    }

    public function test_staff_cannot_manage_items_even_by_direct_url(): void
    {
        $this->actingAs($this->userWithRole('staff'));
        $gabah = $this->item('GBH');

        $this->get('/panel/barang/create')->assertForbidden();
        $this->post('/panel/barang', $this->itemData())->assertForbidden();
        $this->get("/panel/barang/{$gabah->id}/edit")->assertForbidden();
        $this->put("/panel/barang/{$gabah->id}", $this->itemData())->assertForbidden();
        $this->delete("/panel/barang/{$gabah->id}")->assertForbidden();
    }

    public function test_owner_sees_cost_and_can_create_item(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        $this->get('/panel/barang')->assertOk()->assertSee('Biaya rata-rata');

        $this->post('/panel/barang', $this->itemData())->assertRedirect()->assertSessionHas('sukses');
        $item = Item::where('code', 'BKH')->firstOrFail();
        $this->assertSame('Beras Ketan Hitam', $item->name);
        $this->assertSame('0.00', $item->stock_kg ?? '0.00'); // stok tidak bisa diisi dari form
    }

    public function test_item_validation_errors(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        $this->post('/panel/barang', $this->itemData(['code' => 'GBH', 'name' => '', 'weight_per_sack_kg' => 0]))
            ->assertSessionHasErrors(['code', 'name', 'weight_per_sack_kg']);
    }

    public function test_item_search_and_type_filter(): void
    {
        $this->actingAs($this->userWithRole('staff'));

        $this->get('/panel/barang?q=sekam')->assertSee('Sekam')->assertDontSee('Gabah Ketan');
        $this->get('/panel/barang?type=byproduct')->assertSee('Menir')->assertDontSee('Gabah Ketan');
    }

    public function test_used_master_data_cannot_be_deleted(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();
        $this->millGabah();
        $this->sellRice();

        $gabah = $this->item('GBH');
        $supplier = Supplier::firstWhere('name', 'Pak Tani');
        $buyer = Buyer::firstWhere('name', 'Toko Berkah');

        foreach (["/panel/barang/{$gabah->id}", "/panel/petani/{$supplier->id}", "/panel/pembeli/{$buyer->id}"] as $url) {
            $this->from('/panel')->delete($url)
                ->assertRedirect('/panel')
                ->assertSessionHas('gagal', fn ($message) => str_contains($message, 'Nonaktifkan'));
        }

        $this->assertModelExists($gabah);
        $this->assertModelExists($supplier);
        $this->assertModelExists($buyer);
    }

    public function test_unused_master_data_can_be_deleted_by_owner(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $item = Item::create($this->itemData());
        $supplier = Supplier::create(['name' => 'Bu Sari']);
        $buyer = Buyer::create(['name' => 'Warung Ani']);

        $this->delete("/panel/barang/{$item->id}")->assertRedirect('/panel/barang');
        $this->delete("/panel/petani/{$supplier->id}")->assertRedirect('/panel/petani');
        $this->delete("/panel/pembeli/{$buyer->id}")->assertRedirect('/panel/pembeli');

        $this->assertModelMissing($item);
        $this->assertModelMissing($supplier);
        $this->assertModelMissing($buyer);
    }

    public function test_staff_can_add_supplier_and_buyer_but_not_delete(): void
    {
        $this->actingAs($this->userWithRole('staff'));

        $this->post('/panel/petani', ['name' => 'Pak Darto', 'village' => 'Losarang', 'is_active' => '1'])
            ->assertRedirect('/panel/petani');
        $this->post('/panel/pembeli', ['name' => 'Toko Maju', 'type' => 'wholesale', 'is_active' => '1'])
            ->assertRedirect('/panel/pembeli');

        $supplier = Supplier::firstWhere('name', 'Pak Darto');
        $buyer = Buyer::firstWhere('name', 'Toko Maju');
        $this->put("/panel/petani/{$supplier->id}", ['name' => 'Pak Darto', 'is_active' => '0'])->assertRedirect();
        $this->assertFalse($supplier->fresh()->is_active);

        $this->delete("/panel/petani/{$supplier->id}")->assertForbidden();
        $this->delete("/panel/pembeli/{$buyer->id}")->assertForbidden();
    }

    public function test_supplier_and_buyer_validation(): void
    {
        $this->actingAs($this->userWithRole('staff'));

        $this->post('/panel/petani', [])->assertSessionHasErrors('name');
        $this->post('/panel/pembeli', ['name' => 'X', 'type' => 'salah', 'email' => 'bukan-email'])
            ->assertSessionHasErrors(['type', 'email']);
    }

    public function test_supplier_village_filter(): void
    {
        $this->actingAs($this->userWithRole('staff'));
        Supplier::create(['name' => 'Pak Udin', 'village' => 'Losarang']);
        Supplier::create(['name' => 'Pak Karta', 'village' => 'Krimun']);

        $this->get('/panel/petani?village=Krimun')->assertOk()->assertSee('Pak Karta')->assertDontSee('Pak Udin');
    }

    private function itemData(array $override = []): array
    {
        return array_merge([
            'code' => 'BKH',
            'name' => 'Beras Ketan Hitam',
            'type' => 'finished',
            'weight_per_sack_kg' => 25,
            'min_stock_kg' => 100,
            'is_active' => '1',
        ], $override);
    }
}
