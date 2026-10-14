<?php

namespace Tests\Feature;

use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// PRD-09 A8: robots.txt untuk peluncuran + halaman /panel tidak diindeks.
// SEO teknis: meta per halaman, Open Graph, JSON-LD, sitemap.
class SeoLaunchTest extends TestCase
{
    use RefreshDatabase;

    private const PAGES = ['/', '/tentang-kami', '/produk', '/galeri', '/kontak'];

    public function test_robots_txt_blocks_internal_areas_and_points_to_sitemap(): void
    {
        $robots = file_get_contents(base_path('robots.txt'));

        $this->assertStringContainsString("User-agent: *\n", $robots);
        foreach (['/panel', '/nota/', '/pembelian/', '/laporan/'] as $path) {
            $this->assertStringContainsString("Disallow: {$path}\n", $robots);
        }
        $this->assertStringContainsString('Sitemap: https://aniwijaya.com/sitemap.xml', $robots);

        // Halaman publik tidak boleh ikut terblok.
        $this->assertDoesNotMatchRegularExpression('#^Disallow: /\s*$#m', $robots);
        foreach (['/produk', '/galeri', '/kontak', '/tentang-kami', '/en'] as $path) {
            $this->assertStringNotContainsString("Disallow: {$path}", $robots);
        }
    }

    public function test_panel_pages_are_noindex(): void
    {
        $meta = '<meta name="robots" content="noindex">';

        $this->get('/panel/login')->assertOk()->assertSee($meta, false)->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->actingAs($this->userWithRole('staff'))->get('/panel')->assertOk()->assertSee($meta, false)->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_public_pages_are_indexable(): void
    {
        foreach (self::PAGES as $page) {
            $this->get($page)->assertOk()->assertHeaderMissing('X-Robots-Tag')->assertDontSee('noindex', false);
        }
    }

    public function test_sitemap_uses_app_url_for_every_page_and_locale(): void
    {
        $base = rtrim(config('app.url'), '/');

        $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml');
        $xml = simplexml_load_string($response->getContent());

        $this->assertNotFalse($xml, 'sitemap.xml bukan XML valid');
        $this->assertCount(5 * 5, $xml->url);

        $locs = array_map('strval', iterator_to_array($xml->xpath('//*[local-name()="loc"]'), false));
        foreach ($locs as $loc) {
            $this->assertStringStartsWith($base, $loc);
        }
        foreach (['', '/produk', '/en/kontak', '/ko/galeri'] as $path) {
            $this->assertContains($base.$path, $locs);
        }
        $this->assertStringContainsString('hreflang="x-default" href="'.$base.'/produk"', $response->getContent());
    }

    public function test_each_page_has_unique_title_and_description(): void
    {
        $this->seed(SiteSettingSeeder::class);

        foreach (['', '/en'] as $prefix) {
            $titles = $descriptions = [];
            foreach (self::PAGES as $page) {
                $html = $this->get(rtrim($prefix.$page, '/') ?: '/')->assertOk()->getContent();
                preg_match('#<title>(.*?)</title>#s', $html, $title);
                preg_match('#<meta name="description" content="([^"]+)"#', $html, $description);
                $titles[] = $title[1];
                $descriptions[] = $description[1];
            }

            $this->assertSame($titles, array_unique($titles), "Judul duplikat di {$prefix}");
            $this->assertSame($descriptions, array_unique($descriptions), "Deskripsi duplikat di {$prefix}");
        }
    }

    public function test_open_graph_follows_page_and_locale(): void
    {
        $this->seed(SiteSettingSeeder::class);

        $this->get('/en/produk')
            ->assertSee('<meta property="og:title" content="Premium White Glutinous Rice - Wholesale Supplier - PB. Ani Wijaya">', false)
            ->assertSee('<meta property="og:url" content="'.url('/en/produk').'">', false)
            ->assertSee('<meta property="og:image" content="'.asset('images/foto_pabrik.webp').'">', false)
            ->assertSee('<meta property="og:locale" content="en_US">', false)
            ->assertSee('<meta property="og:locale:alternate" content="id_ID">', false)
            ->assertDontSee('<meta property="og:locale:alternate" content="en_US">', false)
            ->assertSee('Indonesian glutinous rice supplier', false);
    }

    public function test_home_has_local_business_json_ld_from_settings(): void
    {
        $this->seed(SiteSettingSeeder::class);

        $html = $this->get('/')->assertOk()->getContent();
        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $html, $match);
        $data = json_decode($match[1] ?? '', true);

        $this->assertSame('LocalBusiness', $data['@type']);
        $this->assertSame('PB. Ani Wijaya', $data['name']);
        $this->assertSame('+62 813-2431-4300', $data['telephone']);
        $this->assertSame(asset('images/logo.png'), $data['logo']);
        $this->assertSame('Kec. Sukra, Kab. Indramayu', $data['address']['streetAddress']);
        $this->assertSame('Indramayu', $data['address']['addressLocality']);
        $this->assertSame('Jawa Barat', $data['address']['addressRegion']);
        $this->assertSame('45259', $data['address']['postalCode']);
        $this->assertSame('ID', $data['address']['addressCountry']);
        $this->assertStringNotContainsString('Subang', $data['address']['streetAddress']);

        $this->get('/produk')->assertDontSee('application/ld+json', false);
    }
}
