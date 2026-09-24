<?php

namespace Tests\Feature;

use App\Filament\Admin\Pages\WebsiteSettings;
use App\Models\SiteSetting;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

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

        Livewire::test(WebsiteSettings::class)
            ->assertSchemaStateSet(['hero_title' => 'Heritage of Excellence'])
            ->fillForm(['hero_title' => 'Ketan Terbaik Indramayu'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Ketan Terbaik Indramayu', SiteSetting::get('hero_title'));
        $this->get('/')->assertSee('Ketan Terbaik Indramayu');
        $this->assertSame(21, SiteSetting::count()); // tidak ada key yang hilang/bertambah
    }

    public function test_invalid_url_is_rejected(): void
    {
        $this->actingAs($this->userWithRole('owner'));

        Livewire::test(WebsiteSettings::class)
            ->fillForm(['social_instagram' => 'bukan url'])
            ->call('save')
            ->assertHasFormErrors(['social_instagram']);
    }

    public function test_staff_cannot_access_website_menu(): void
    {
        $this->actingAs($this->userWithRole('staff'));

        $this->get('/admin/website-settings')->assertForbidden();
        $this->get('/admin/contact-messages')->assertForbidden();
        $this->get('/admin/galleries')->assertForbidden();
        $this->get('/admin/products')->assertForbidden();
    }
}
