<?php

namespace Tests\Feature\Admin;

use Database\Seeders\ItemSeeder;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Staff yang mengirim form owner (bahkan dengan data kosong/salah) harus mendapat 403, bukan pesan validasi.
class AuthorizationBeforeValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_gets_403_before_validation_on_owner_actions(): void
    {
        $this->seed([ItemSeeder::class, SiteSettingSeeder::class]);
        $owner = $this->userWithRole('owner');
        $this->actingAs($owner);
        $purchase = $this->buyGabah();
        $payment = $purchase->payments()->firstOrFail();
        $staff = $this->userWithRole('staff');
        $gabah = $this->item('GBH');

        $this->actingAs($staff);

        foreach ([
            ['post', "/panel/pembelian/{$purchase->id}/batal"],
            ['post', "/panel/pembayaran/{$payment->id}/batal"],
            ['post', '/panel/biaya'],
            ['post', '/panel/opname'],
            ['post', '/panel/barang'],
            ['put', "/panel/barang/{$gabah->id}"],
            ['post', '/panel/pengguna'],
            ['put', "/panel/pengguna/{$owner->id}"],
            ['post', '/panel/galeri'],
            ['post', '/panel/katalog'],
            ['put', '/panel/pengaturan-website'],
        ] as [$method, $url]) {
            $this->{$method}($url, [])->assertForbidden();
        }
    }
}
