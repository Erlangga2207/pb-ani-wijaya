<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// PRD-09 A8: robots.txt untuk peluncuran + halaman /panel tidak diindeks.
class SeoLaunchTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_blocks_panel_and_points_to_sitemap(): void
    {
        $robots = file_get_contents(base_path('robots.txt'));

        $this->assertStringContainsString("User-agent: *\n", $robots);
        $this->assertStringContainsString("Disallow: /panel\n", $robots);
        $this->assertStringContainsString('Sitemap: https://aniwijaya.com/sitemap.xml', $robots);
    }

    public function test_panel_pages_are_noindex(): void
    {
        $meta = '<meta name="robots" content="noindex">';

        $this->get('/panel/login')->assertOk()->assertSee($meta, false);
        $this->actingAs($this->userWithRole('staff'))->get('/panel')->assertOk()->assertSee($meta, false);
    }
}
