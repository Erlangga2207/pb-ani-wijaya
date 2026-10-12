<?php

namespace Tests\Feature;

use App\Services\SaleService;
use Database\Seeders\ItemSeeder;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_access_rules(): void
    {
        $this->seed([ItemSeeder::class, SiteSettingSeeder::class]);
        $creator = $this->userWithRole('staff');
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();
        $this->millGabah();

        $this->actingAs($creator);
        $sale = $this->sellRice();
        $url = "/nota/{$sale->id}";

        // Pembuat: bisa, tanpa HPP.
        $this->get($url)->assertOk()->assertSee($sale->number)->assertSee('wa.me/6281234567890')->assertDontSee('HPP');

        // Owner: bisa.
        $this->actingAs($this->userWithRole('owner'))->get($url)->assertOk();

        // Staff lain: 403.
        $this->actingAs($this->userWithRole('staff'))->get($url)->assertForbidden();

        // Tamu: redirect login.
        auth()->logout();
        $this->get($url)->assertRedirect('/panel/login');
    }

    public function test_void_invoice_shows_watermark(): void
    {
        $this->seed([ItemSeeder::class, SiteSettingSeeder::class]);
        $this->actingAs($this->userWithRole('owner'));
        $this->buyGabah();
        $this->millGabah();
        $sale = $this->sellRice();
        app(SaleService::class)->void($sale, 'batal', auth()->user());

        $this->get("/nota/{$sale->id}")->assertOk()->assertSee('DIBATALKAN');
    }
}
