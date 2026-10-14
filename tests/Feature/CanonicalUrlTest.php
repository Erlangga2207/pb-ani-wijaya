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

    // Root .htaccess meneruskan /produk ke public/index.php → SCRIPT_NAME=/public/index.php.
    // Link tidak boleh berisi "/public".
    public function test_production_urls_have_no_public_segment_behind_root_htaccess(): void
    {
        $this->seed(SiteSettingSeeder::class);
        $this->app['env'] = 'production';
        config(['app.url' => 'https://aniwijaya.com']);
        (new AppServiceProvider($this->app))->boot();

        $server = ['SCRIPT_NAME' => '/public/index.php', 'PHP_SELF' => '/public/index.php', 'SCRIPT_FILENAME' => base_path('public/index.php')];

        $html = $this->call('GET', 'https://aniwijaya.com/produk', server: $server)->assertOk()->getContent();
        $this->assertStringContainsString('<link rel="canonical" href="https://aniwijaya.com/produk">', $html);
        $this->assertStringNotContainsString('aniwijaya.com/public', $html);

        $this->call('GET', 'https://aniwijaya.com/en/kontak', server: $server)->assertOk()
            ->assertSee('hreflang="ja" href="https://aniwijaya.com/ja/kontak"', false);
    }

    public function test_local_urls_follow_request(): void
    {
        $this->seed(SiteSettingSeeder::class);

        $this->get('http://localhost/produk')->assertOk()
            ->assertSee('<link rel="canonical" href="http://localhost/produk">', false);
    }
}
