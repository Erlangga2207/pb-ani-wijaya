<?php

namespace Tests\Feature;

use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_load(): void
    {
        $this->seed(SiteSettingSeeder::class);

        foreach (['/', '/tentang-kami', '/produk', '/galeri', '/kontak'] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
