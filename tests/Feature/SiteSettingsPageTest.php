<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// PRD-08 Fase 6: halaman Pengaturan Website pindah dari Filament (/admin/website-settings) ke /panel/pengaturan-website.
// Assertion bisnis dipertahankan: nilai tersimpan, cache direset, tidak ada key hilang, URL salah ditolak, staff 403.
class SiteSettingsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SiteSettingSeeder::class);
    }

    public function test_owner_updates_value_and_cache_is_reset(): void
    {
        $this->actingAs($this->userWithRole('owner'));
        $this->assertSame('Heritage of Excellence', SiteSetting::get('hero_title')); // isi cache

        $this->get('/panel/pengaturan-website')->assertOk()->assertSee('Heritage of Excellence');
        $this->put('/panel/pengaturan-website', $this->formData(['hero_title' => ['id' => 'Ketan Terbaik Indramayu']]))
            ->assertRedirect('/panel/pengaturan-website')
            ->assertSessionHasNoErrors();

        $this->assertSame('Ketan Terbaik Indramayu', SiteSetting::get('hero_title'));
        $this->get('/')->assertSee('Ketan Terbaik Indramayu');
        $this->assertSame(23, SiteSetting::count()); // 21 key lama + 2 key SEO, tidak ada yang hilang
    }

    public function test_invalid_url_is_rejected(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        $this->put('/panel/pengaturan-website', $this->formData(['social_instagram' => 'bukan url']))
            ->assertSessionHasErrors(['settings.social_instagram']);
    }

    public function test_staff_cannot_access_website_menu(): void
    {
        $this->actingAs($this->userWithRole('staff'));

        $this->get('/panel/pengaturan-website')->assertForbidden();
        $this->get('/panel/pesan')->assertForbidden();
        $this->get('/panel/galeri')->assertForbidden();
        $this->get('/panel/katalog')->assertForbidden();
    }

    /** Isi form lengkap dari database (seperti browser), lalu timpa sebagian. */
    private function formData(array $override = []): array
    {
        $settings = SiteSetting::all()->mapWithKeys(fn (SiteSetting $s) => [
            $s->key => $s->is_translatable ? $s->translations() : $s->value,
        ])->all();

        return ['settings' => array_replace_recursive($settings, $override)];
    }
}
