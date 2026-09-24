<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SiteSettingSeeder::class,
            ItemSeeder::class,
        ]);

        // Super admin dari .env (ADMIN_EMAIL / ADMIN_PASSWORD). Kosong = dilewati, jangan hardcode password.
        $email = config('app.admin.email');
        $password = config('app.admin.password');

        if ($email && $password) {
            $admin = User::firstOrCreate(
                ['email' => $email],
                ['name' => 'Super Admin', 'password' => $password, 'email_verified_at' => now()]
            );
            $admin->syncRoles(['super_admin']);
        }
    }
}
