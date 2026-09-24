<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roles = config('permission.table_names.roles');

        // Kalau role owner sudah ada, jangan buat duplikat.
        if (DB::table($roles)->where('name', 'owner')->exists()) {
            return;
        }

        DB::table($roles)->where('name', 'admin')->update(['name' => 'owner']);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        DB::table(config('permission.table_names.roles'))->where('name', 'owner')->update(['name' => 'admin']);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
