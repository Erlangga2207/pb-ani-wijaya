<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// PRD-09 A2: di produksi semua URL absolut memakai APP_URL + https, bukan host request.
class CanonicalUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_urls_use_app_url_and_https(): void
    {
        $this->seed(SiteSettingSeeder::class);
        $this->app['env'] = 'production';
        config(['app.url' => 'https://aniwijaya.com']);
        (new AppServiceProvider($this->app))->boot();

        $this->get('http://www.aniwijaya.com/produk')->assertOk()
            ->assertSee('<link rel="canonical" href="https://aniwijaya.com/produk">', false)
            ->assertSee('hreflang="en" href="https://aniwijaya.com/en/produk"', false)
            ->assertDontSee('www.aniwijaya.com');

        $this->get('http://www.aniwijaya.com/sitemap.xml')->assertOk()
            ->assertSee('<loc>https://aniwijaya.com/en/kontak</loc>', false)
            ->assertDontSee('http://www.aniwijaya.com');
    }

    public function test_local_urls_follow_request(): void
    {
        $this->seed(SiteSettingSeeder::class);

        $this->get('http://localhost/produk')->assertOk()
            ->assertSee('<link rel="canonical" href="http://localhost/produk">', false);
    }
}
