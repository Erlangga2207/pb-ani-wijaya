<?php

namespace Tests\Feature;

use App\Models\Gallery;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GalleryCaptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_caption_comes_from_description_and_shows_on_gallery_page(): void
    {
        $this->seed(SiteSettingSeeder::class);
        $gallery = Gallery::create([
            'title' => 'Sawah Losarang',
            'description' => 'Panen ketan musim ini',
            'category' => 'origin',
            'file_path' => 'gallery/sawah.jpg',
            'file_type' => 'image',
        ]);

        $this->assertSame('Panen ketan musim ini', $gallery->caption);
        $this->assertStringEndsWith('/storage/gallery/sawah.jpg', $gallery->file_url);
        $this->get('/galeri')->assertOk()->assertSee('Panen ketan musim ini');
    }

    public function test_normalize_command_fixes_json_paths(): void
    {
        $id = DB::table('galleries')->insertGetId([
            'title' => 'Lama', 'category' => 'other', 'file_type' => 'image',
            'file_path' => '["gallery\/lama.jpg"]', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->artisan('gallery:normalize-paths')->assertExitCode(0);

        $this->assertSame('gallery/lama.jpg', Gallery::find($id)->file_path);
    }

    public function test_owner_gallery_pages_render(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        $this->get('/admin/galleries')->assertOk();
        $this->get('/admin/galleries/create')->assertOk();
    }
}
