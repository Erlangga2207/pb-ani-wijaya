<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

// PRD-08 Fase 7: Filament Shield dihapus. Permission buatan `shield:generate` tidak dipakai lagi
// (otorisasi lewat role super_admin/owner/staff + Policy). Role tidak disentuh.
return new class extends Migration
{
    public function up(): void
    {
        $tables = config('permission.table_names');

        $ids = DB::table($tables['permissions'])->get(['id', 'name'])
            ->filter(fn ($p) => self::isShieldPermission($p->name))
            ->pluck('id');

        // Pivot dihapus eksplisit (tidak bergantung pada cascade foreign key).
        foreach ($ids->chunk(500) as $chunk) {
            DB::table($tables['role_has_permissions'])->whereIn('permission_id', $chunk)->delete();
            DB::table($tables['model_has_permissions'])->whereIn('permission_id', $chunk)->delete();
            DB::table($tables['permissions'])->whereIn('id', $chunk)->delete();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    // Tidak dikembalikan: permission ini hanya dibuat oleh Shield, yang sudah tidak terpasang.
    public function down(): void {}

    // Format Shield v4 "ViewAny:Item" (separator ":"), dan format lama "view_any_item" / "page_Reports" / "widget_X".
    public static function isShieldPermission(string $name): bool
    {
        return preg_match('/^[A-Z][A-Za-z]*:[A-Z]/', $name) === 1
            || preg_match('/^(view|view_any|create|update|delete|delete_any|restore|restore_any|replicate|reorder|force_delete|force_delete_any|page|widget)_/', $name) === 1;
    }
};
