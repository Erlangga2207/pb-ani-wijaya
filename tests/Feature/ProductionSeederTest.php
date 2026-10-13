<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

// PRD-09 A6: "php artisan db:seed --force" di produksi aman dijalankan ulang — tanpa duplikat, tanpa menimpa editan admin.
class ProductionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_is_idempotent_and_keeps_admin_edits(): void
    {
        config(['app.admin.email' => 'admin@aniwijaya.test', 'app.admin.password' => 'rahasia-awal-123']);
        $this->seed(DatabaseSeeder::class);

        $counts = fn () => [Role::count(), SiteSetting::count(), Item::count(), User::count()];
        $before = $counts();

        // Editan admin setelah deploy pertama.
        SiteSetting::where('key', 'hero_title')->first()->update(['value' => json_encode(['id' => 'Judul Admin', 'en' => 'Admin Title'])]);
        SiteSetting::where('key', 'contact_phone')->first()->update(['value' => '+62 811-0000-0000', 'label' => 'label lama']);
        Item::where('code', 'BKT')->update(['name' => 'Ketan Putih Super']);
        User::where('email', 'admin@aniwijaya.test')->update(['password' => Hash::make('password-baru-456')]);

        $this->seed(DatabaseSeeder::class);

        $this->assertSame($before, $counts());
        $this->assertSame(['id' => 'Judul Admin', 'en' => 'Admin Title'], json_decode(DB::table('site_settings')->where('key', 'hero_title')->value('value'), true));
        $this->assertSame('+62 811-0000-0000', SiteSetting::where('key', 'contact_phone')->value('value'));
        $this->assertSame('Telepon', SiteSetting::where('key', 'contact_phone')->value('label')); // metadata tetap mengikuti kode
        $this->assertSame('Ketan Putih Super', Item::where('code', 'BKT')->value('name'));

        $admin = User::where('email', 'admin@aniwijaya.test')->first();
        $this->assertTrue(Hash::check('password-baru-456', $admin->password));
        $this->assertSame(['super_admin'], $admin->getRoleNames()->all());
    }
}
