<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

// PRD-08 Fase 7: migration penghapus permission Filament Shield.
class RemoveShieldPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_shield_permissions_are_removed_and_roles_stay(): void
    {
        foreach (['ViewAny:Item', 'Create:StockTransaction', 'view_any_item', 'page_Reports', 'kelola-laporan'] as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }
        Role::findByName('owner')->givePermissionTo(['ViewAny:Item', 'kelola-laporan']);
        $user = $this->userWithRole('staff');
        $user->givePermissionTo('view_any_item');

        $migration = require database_path('migrations/2026_10_13_000001_remove_shield_permissions.php');
        $migration->up();

        $this->assertSame(['kelola-laporan'], DB::table('permissions')->pluck('name')->all());
        $this->assertSame(1, DB::table('role_has_permissions')->count());
        $this->assertSame(0, DB::table('model_has_permissions')->count());
        $this->assertEqualsCanonicalizing(User::ROLES, Role::pluck('name')->all());
        $this->assertTrue($user->fresh()->hasRole('staff'));
    }
}
