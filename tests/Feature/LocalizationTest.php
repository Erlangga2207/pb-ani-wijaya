<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Gallery;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// PRD-08 D (bagian statis): routing prefix bahasa, teks statis, switcher, SEO, form kontak per bahasa.
class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    private const PAGES = ['/', '/tentang-kami', '/produk', '/galeri', '/kontak'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SiteSettingSeeder::class);
    }

    public function test_every_public_page_opens_in_every_locale(): void
    {
        foreach (config('locales') as $code => $locale) {
            foreach (self::PAGES as $page) {
                $url = $code === 'id' ? $page : '/'.$code.rtrim($page, '/');
                $this->get($url)->assertOk()->assertSee('<html lang="'.$locale['hreflang'].'"', false);
            }
        }
    }

    public function test_unknown_locale_is_404(): void
    {
        $this->get('/fr')->assertNotFound();
        $this->get('/fr/produk')->assertNotFound();
        $this->get('/id/produk')->assertNotFound(); // bahasa default tidak memakai prefix
    }

    public function test_static_text_changes_per_locale(): void
    {
        $this->get('/')->assertSee('Lihat Produk')->assertSee('Tahun Pengalaman');
        $this->get('/en')->assertSee('View Products')->assertSee('Years of Experience')->assertDontSee('Lihat Produk');
        $this->get('/zh')->assertSee('查看产品');
        $this->get('/ja')->assertSee('製品を見る');
        $this->get('/ko')->assertSee('제품 보기');

        // Menu & link internal tetap di bahasa yang sama.
        $this->get('/en/produk')->assertSee('About Us')->assertSee('href="http://localhost/en/kontak"', false);
    }

    public function test_switcher_links_to_same_page_in_every_language(): void
    {
        $response = $this->get('/en/galeri');

        foreach (config('locales') as $code => $locale) {
            $response->assertSee($locale['name']);
            $response->assertSee('href="'.localized_route('gallery', [], $code).'"', false);
        }
        $response->assertSee('href="http://localhost/galeri"', false);
    }

    public function test_hreflang_and_canonical_are_complete(): void
    {
        $response = $this->get('/ja/tentang-kami');

        $response->assertSee('<link rel="canonical" href="http://localhost/ja/tentang-kami">', false);
        foreach (config('locales') as $code => $locale) {
            $response->assertSee('<link rel="alternate" hreflang="'.$locale['hreflang'].'" href="'.localized_route('about', [], $code).'">', false);
        }
        $response->assertSee('<link rel="alternate" hreflang="x-default" href="http://localhost/tentang-kami">', false);
    }

    public function test_cjk_fonts_only_load_for_cjk_locales(): void
    {
        $this->get('/')->assertDontSee('Noto');
        $this->get('/en')->assertDontSee('Noto');
        $this->get('/zh')->assertSee('Noto Sans SC')->assertDontSee('keep-all');
        $this->get('/ja')->assertSee('Noto Sans JP');
        $this->get('/ko')->assertSee('Noto Sans KR')->assertSee('word-break: keep-all');
    }

    public function test_whatsapp_prefill_is_translated_and_encoded(): void
    {
        $this->get('/en/produk')
            ->assertSee('?text='.rawurlencode('Hello PB. Ani Wijaya, I am interested in your Premium White Glutinous Rice. Please share your wholesale prices.'), false);
    }

    public function test_contact_form_redirects_and_validates_in_origin_language(): void
    {
        $this->from('/en/kontak')->post('/kontak', ['locale' => 'en'])
            ->assertRedirect('/en/kontak')
            ->assertSessionHasErrors(['name' => 'The Full Name field is required.']);

        $this->from('/ja/kontak')->post('/kontak', ['locale' => 'ja', 'name' => 'Tanaka', 'email' => 'bukan-email', 'message' => 'Halo'])
            ->assertSessionHasErrors(['email' => 'メール には有効なメールアドレスを入力してください。']);

        $this->post('/kontak', ['locale' => 'en', 'name' => 'John', 'email' => 'john@example.com', 'message' => 'Hello'])
            ->assertRedirect('/en/kontak')
            ->assertSessionHas('success', 'Your message has been sent! Our team will contact you shortly.');
        $this->assertSame(1, ContactMessage::count());

        // Bahasa tak dikenal → bahasa default.
        $this->post('/kontak', ['locale' => 'xx', 'name' => 'Budi', 'email' => 'budi@example.com', 'message' => 'Halo'])
            ->assertRedirect('/kontak');
    }

    public function test_gallery_category_label_is_translated(): void
    {
        $gallery = new Gallery(['category' => 'origin']);

        $this->assertSame('🌾 Origin', $gallery->category_label);
        app()->setLocale('zh');
        $this->assertSame('🌾 产地', $gallery->category_label);
    }

    public function test_sitemap_lists_every_page_in_every_locale(): void
    {
        $xml = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml')->getContent();

        $this->assertSame(count(self::PAGES) * count(config('locales')), substr_count($xml, '<loc>'));
        $this->assertStringContainsString('<loc>http://localhost/ko/produk</loc>', $xml);
        $this->assertStringContainsString('hreflang="zh-Hans" href="http://localhost/zh/kontak"', $xml);
        $this->assertNotFalse(simplexml_load_string($xml));
    }

    public function test_translation_files_are_complete(): void
    {
        $keys = array_keys(json_decode(file_get_contents(lang_path('id.json')), true));

        foreach (['en', 'zh', 'ja', 'ko'] as $locale) {
            $translations = json_decode(file_get_contents(lang_path("{$locale}.json")), true);
            $this->assertSame($keys, array_keys($translations), "lang/{$locale}.json tidak lengkap");
            $this->assertEmpty(array_filter($translations, fn ($v) => trim($v) === ''), "lang/{$locale}.json punya nilai kosong");
        }
    }
}
