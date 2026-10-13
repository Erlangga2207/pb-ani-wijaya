<?php

namespace Tests\Feature\Admin;

use App\Enums\LeadStatus;
use App\Models\ContactMessage;
use App\Models\Gallery;
use App\Models\Product;
use App\Models\SiteSetting;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

// PRD-08 Fase 6: modul Website di /panel + konten dinamis per bahasa.
class WebsiteModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SiteSettingSeeder::class);
        Storage::fake('public');
    }

    // ---------- SiteSetting::get per bahasa ----------

    public function test_site_setting_follows_active_locale_with_fallback(): void
    {
        // hero_title = tagline brand: versi ID sengaja sama dengan EN (keputusan Erlan 2026-10-13).
        $this->assertSame('Heritage of Excellence', SiteSetting::get('hero_title'));
        $this->assertStringStartsWith('Meningkatkan standar', SiteSetting::get('hero_description'));
        app()->setLocale('en');
        $this->assertSame('Heritage of Excellence', SiteSetting::get('hero_title'));
        $this->assertStringStartsWith('Elevating the standard', SiteSetting::get('hero_description'));
        app()->setLocale('ja');
        $this->assertSame('受け継がれる卓越', SiteSetting::get('hero_title'));

        // Terjemahan kosong → versi ID.
        SiteSetting::where('key', 'hero_title')->first()->update(['value' => json_encode(['id' => 'Judul ID', 'ja' => ''])]);
        $this->assertSame('Judul ID', SiteSetting::get('hero_title'));

        // Setting biasa sama di semua bahasa.
        $this->assertSame('PB. Ani Wijaya', SiteSetting::get('company_name'));
    }

    public function test_missing_key_default_is_not_cached(): void
    {
        $this->assertSame('a', SiteSetting::get('tidak_ada', 'a'));
        $this->assertSame('b', SiteSetting::get('tidak_ada', 'b'));
    }

    public function test_editing_english_setting_shows_on_english_site_and_empty_falls_back(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $this->get('/en')->assertSee('Elevating the standard');

        $this->put('/panel/pengaturan-website', $this->settingsForm(['hero_description' => ['en' => 'Finest Sticky Rice']]))->assertSessionHasNoErrors();
        $this->get('/en')->assertSee('Finest Sticky Rice');
        $this->get('/')->assertSee('Meningkatkan standar')->assertDontSee('Finest Sticky Rice');

        // EN dikosongkan → /en menampilkan versi ID, bukan string kosong.
        $this->put('/panel/pengaturan-website', $this->settingsForm(['hero_description' => ['en' => '']]))->assertSessionHasNoErrors();
        $this->get('/en')->assertSee('Meningkatkan standar');
    }

    public function test_indonesian_setting_text_is_required(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        $this->put('/panel/pengaturan-website', $this->settingsForm(['hero_title' => ['id' => '']]))
            ->assertSessionHasErrors('settings.hero_title.id');
    }

    public function test_seo_meta_follows_locale(): void
    {
        $this->get('/')->assertSee('<title>Produsen Beras Ketan Premium dari Indramayu - PB. Ani Wijaya</title>', false);
        $this->get('/en')->assertSee('produces premium head-grade white glutinous rice', false);

        // SEO versi ID berbahasa Indonesia dan memuat "beras ketan" (keputusan Erlan 2026-10-13).
        app()->setLocale('id');
        $this->assertStringContainsStringIgnoringCase('beras ketan', SiteSetting::get('seo_meta_title'));
        $this->assertStringContainsStringIgnoringCase('beras ketan', SiteSetting::get('seo_meta_description'));
    }

    public function test_brand_texts_stay_the_same_in_indonesian(): void
    {
        foreach (['hero_badge' => "INDRAMAYU'S FINEST", 'hero_title' => 'Heritage of Excellence', 'company_tagline' => 'Premium Rice Producer'] as $key => $brand) {
            $this->assertSame($brand, SiteSetting::get($key));
        }
    }

    // ---------- Produk ----------

    public function test_product_translations_and_fallback_on_homepage(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        $this->post('/panel/katalog', [
            'name' => ['id' => 'Ketan Putih Kepala', 'en' => 'Premium White Sticky Rice', 'zh' => ''],
            'description' => ['id' => 'Deskripsi ID', 'en' => 'English description'],
            'weight_per_karung' => 25,
            'is_active' => '1',
        ])->assertSessionHasNoErrors();

        $product = Product::firstOrFail();
        // assertEquals: kolom JSON MySQL menyimpan key berurutan abjad, urutan tidak penting.
        $this->assertEquals(['id' => 'Ketan Putih Kepala', 'en' => 'Premium White Sticky Rice'], $product->getTranslations('name'));

        $this->get('/')->assertSee('Ketan Putih Kepala');
        $this->get('/en')->assertSee('Premium White Sticky Rice');
        $this->get('/zh')->assertSee('Ketan Putih Kepala'); // fallback ke ID

        $this->get('/panel/katalog')->assertOk()->assertSee('EN ✓')->assertSee('中文 ✗');

        // Edit: EN dikosongkan benar-benar terhapus.
        $this->put("/panel/katalog/{$product->id}", [
            'name' => ['id' => 'Ketan Putih Kepala', 'en' => ''],
            'slug' => $product->slug,
            'weight_per_karung' => 25,
            'is_active' => '1',
        ])->assertSessionHasNoErrors();
        $this->assertSame(['id' => 'Ketan Putih Kepala'], $product->fresh()->getTranslations('name'));
    }

    public function test_product_without_description_keeps_public_fallback_text(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $this->post('/panel/katalog', ['name' => ['id' => 'Ketan'], 'weight_per_karung' => 25, 'is_active' => '1']);

        $this->assertNull(Product::firstOrFail()->getRawOriginal('description'));
        $this->get('/')->assertSee('Beras Ketan Putih premium kelas KEPALA');
    }

    public function test_product_validation(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        Product::create(['name' => ['id' => 'Ada'], 'slug' => 'ada', 'weight_per_karung' => 25]);

        $this->post('/panel/katalog', ['name' => ['id' => '', 'xx' => 'bahasa asing'], 'slug' => 'ada', 'weight_per_karung' => 0])
            ->assertSessionHasErrors(['name', 'name.id', 'slug', 'weight_per_karung']);
    }

    // ---------- Galeri ----------

    public function test_gallery_upload_translation_and_file_lifecycle(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        $this->post('/panel/galeri', [
            'title' => ['id' => 'Sawah Losarang', 'ko' => '로사랑 논'],
            'description' => ['id' => 'Panen ketan', 'ko' => '찹쌀 수확'],
            'category' => 'origin',
            'file' => UploadedFile::fake()->image('Sawah Panen.jpg'),
            'order' => 1,
            'is_featured' => '1',
            'is_active' => '1',
        ])->assertRedirect('/panel/galeri')->assertSessionHasNoErrors();

        $gallery = Gallery::firstOrFail();
        $this->assertSame('image', $gallery->file_type);
        $this->assertMatchesRegularExpression('#^gallery/sawah-panen-[a-z0-9]{6}\.jpg$#', $gallery->file_path);
        Storage::disk('public')->assertExists($gallery->file_path);

        $this->get('/ko/galeri')->assertSee('로사랑 논');
        $this->get('/en/galeri')->assertSee('Sawah Losarang'); // fallback ke ID
        $this->get('/panel/galeri')->assertSee('한국어 ✓')->assertSee('EN ✗');

        // Ganti file → file lama terhapus; video MP4 dikenali.
        $old = $gallery->file_path;
        $this->put("/panel/galeri/{$gallery->id}", [
            'title' => ['id' => 'Sawah Losarang'], 'category' => 'origin', 'order' => 1, 'is_active' => '1',
            'file' => UploadedFile::fake()->create('panen.mp4', 1000, 'video/mp4'),
        ])->assertSessionHasNoErrors();
        $gallery->refresh();
        $this->assertSame('video', $gallery->file_type);
        Storage::disk('public')->assertMissing($old);

        $this->delete("/panel/galeri/{$gallery->id}")->assertRedirect('/panel/galeri');
        Storage::disk('public')->assertMissing($gallery->file_path);
        $this->assertModelMissing($gallery);
    }

    public function test_gallery_upload_validation(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $base = ['title' => ['id' => 'X'], 'category' => 'origin'];

        $this->post('/panel/galeri', $base)->assertSessionHasErrors('file');
        $this->post('/panel/galeri', $base + ['file' => UploadedFile::fake()->create('virus.exe', 10)])->assertSessionHasErrors('file');
        $this->post('/panel/galeri', $base + ['file' => UploadedFile::fake()->image('besar.jpg')->size(6000)])->assertSessionHasErrors('file');
        $this->post('/panel/galeri', ['title' => ['id' => 'X'], 'category' => 'salah', 'file' => UploadedFile::fake()->image('a.jpg')])
            ->assertSessionHasErrors('category');

        $this->assertSame(0, Gallery::count());
    }

    // ---------- Pesan & calon pembeli ----------

    public function test_contact_message_follow_up(): void
    {
        $owner = $this->userWithRole('owner');
        $this->actingAs($owner);
        $lead = ContactMessage::create(['name' => 'Siti', 'email' => 'siti@example.com', 'message' => 'Halo']);

        $this->get('/panel/pesan')->assertSee('belum dibaca');
        $this->get("/panel/pesan/{$lead->id}")->assertOk()->assertSee('Halo');
        $this->assertTrue($lead->fresh()->is_read);

        $this->put("/panel/pesan/{$lead->id}", ['status' => 'won', 'phone' => '0812', 'admin_notes' => 'Deal 1 ton'])->assertSessionHasNoErrors();
        $lead->refresh();
        $this->assertSame(LeadStatus::Won, $lead->status);
        $this->assertSame($owner->id, $lead->handled_by);

        $this->put("/panel/pesan/{$lead->id}", ['status' => 'salah'])->assertSessionHasErrors('status');

        $this->delete("/panel/pesan/{$lead->id}")->assertRedirect('/panel/pesan');
        $this->assertModelMissing($lead);
    }

    public function test_staff_cannot_use_website_actions_by_direct_url(): void
    {
        $lead = ContactMessage::create(['name' => 'Siti', 'email' => 'siti@example.com', 'message' => 'Halo']);
        $product = Product::create(['name' => ['id' => 'Ketan'], 'slug' => 'ketan', 'weight_per_karung' => 25]);
        $this->actingAs($this->userWithRole('staff'));

        $this->get("/panel/pesan/{$lead->id}")->assertForbidden();
        $this->post("/panel/pesan/{$lead->id}/jadikan-pembeli")->assertForbidden();
        $this->post("/panel/pesan/{$lead->id}/spam")->assertForbidden();
        $this->get("/panel/katalog/{$product->id}/edit")->assertForbidden();
        $this->post('/panel/galeri', [])->assertForbidden();
        $this->put('/panel/pengaturan-website', [])->assertForbidden();
    }

    public function test_owner_can_open_all_website_pages(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $product = Product::create(['name' => ['id' => 'Ketan'], 'slug' => 'ketan', 'weight_per_karung' => 25]);
        $gallery = Gallery::create(['title' => ['id' => 'Foto'], 'category' => 'other', 'file_path' => 'gallery/a.jpg', 'file_type' => 'image']);

        foreach (['/panel/katalog', '/panel/katalog/create', "/panel/katalog/{$product->id}/edit", '/panel/galeri', '/panel/galeri/create',
            "/panel/galeri/{$gallery->id}/edit", '/panel/pengaturan-website', '/panel/pesan'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/panel')->assertSee('Katalog Produk')->assertSee('Pengaturan Website');
    }

    private function settingsForm(array $override = []): array
    {
        $settings = SiteSetting::all()->mapWithKeys(fn (SiteSetting $s) => [
            $s->key => $s->is_translatable ? $s->translations() : $s->value,
        ])->all();

        return ['settings' => array_replace_recursive($settings, $override)];
    }
}
