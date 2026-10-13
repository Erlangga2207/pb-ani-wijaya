<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Migration data PRD-08 Fase 6: tidak ada data hilang saat up(), dan down() mengembalikan nilai ID.
class TranslatableMigrationTest extends TestCase
{
    use RefreshDatabase;

    private function migration(string $file): object
    {
        return require database_path("migrations/{$file}");
    }

    public function test_product_and_gallery_texts_move_to_id_and_back(): void
    {
        $migration = $this->migration('2026_10_12_000001_make_product_and_gallery_texts_translatable.php');
        $migration->down(); // kembali ke skema lama (kolom teks biasa)

        DB::table('products')->insert(['name' => 'Ketan "Kepala"', 'slug' => 'ketan', 'description' => null, 'weight_per_karung' => 25, 'is_active' => true]);
        DB::table('galleries')->insert(['title' => 'Sawah', 'description' => 'Panen', 'category' => 'origin', 'file_path' => 'gallery/a.jpg', 'file_type' => 'image']);

        $migration->up();

        $this->assertSame(['id' => 'Ketan "Kepala"'], json_decode(DB::table('products')->value('name'), true));
        $this->assertNull(DB::table('products')->value('description'));
        $this->assertSame(['id' => 'Panen'], json_decode(DB::table('galleries')->value('description'), true));

        $migration->down();

        $this->assertSame('Ketan "Kepala"', DB::table('products')->value('name'));
        $this->assertSame('Sawah', DB::table('galleries')->value('title'));
        $this->assertSame('Panen', DB::table('galleries')->value('description'));

        $migration->up(); // biarkan skema akhir untuk RefreshDatabase
    }

    public function test_site_settings_defaults_get_drafts_and_edited_values_are_kept(): void
    {
        $migration = $this->migration('2026_10_12_000002_make_site_settings_translatable.php');
        $migration->down();
        $oldDescription = (require database_path('data/site_setting_translations.php'))['hero_description']['old'];

        $row = fn (string $key, ?string $value, string $label = 'Label') => ['key' => $key, 'value' => $value, 'label' => $label, 'group' => 'hero', 'type' => 'text'];
        DB::table('site_settings')->insert([
            $row('hero_title', 'Heritage of Excellence', 'Hero Main Title'), // masih default lama (tagline brand)
            $row('hero_description', $oldDescription, 'Hero Description'),  // masih default lama
            $row('about_title', 'Judul Buatan Admin'),                       // sudah diedit admin
            $row('company_name', 'PB. Ani Wijaya'),                          // tidak diterjemahkan
        ]);

        $migration->up();

        $value = fn (string $key) => DB::table('site_settings')->where('key', $key)->first();

        // Tagline brand: versi ID tetap teks Inggris (keputusan Erlan 2026-10-13).
        $this->assertSame('Heritage of Excellence', json_decode($value('hero_title')->value, true)['id']);
        $this->assertSame('Heritage of Excellence', json_decode($value('hero_title')->value, true)['en']);
        $this->assertSame('Judul utama hero', $value('hero_title')->label);
        // Teks lain: ID = draf Indonesia, EN = teks Inggris lama.
        $this->assertStringStartsWith('Meningkatkan standar', json_decode($value('hero_description')->value, true)['id']);
        $this->assertSame($oldDescription, json_decode($value('hero_description')->value, true)['en']);
        $this->assertSame('Deskripsi hero', $value('hero_description')->label);
        $this->assertSame(['id' => 'Judul Buatan Admin'], json_decode($value('about_title')->value, true));
        $this->assertSame('PB. Ani Wijaya', $value('company_name')->value);
        $this->assertEquals(0, $value('company_name')->is_translatable);
        $this->assertNotNull($value('seo_meta_title'));
        $this->assertNotNull($value('seo_meta_description'));

        $migration->down();

        $this->assertSame('Heritage of Excellence', $value('hero_title')->value);
        $this->assertSame('Hero Main Title', $value('hero_title')->label);
        $this->assertSame($oldDescription, $value('hero_description')->value); // draf belum diedit → teks lama kembali
        $this->assertSame('Hero Description', $value('hero_description')->label);
        $this->assertSame('Judul Buatan Admin', $value('about_title')->value);
        $this->assertNull($value('seo_meta_title'));

        $migration->up();
    }
}
