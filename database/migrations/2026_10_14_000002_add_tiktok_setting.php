<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Setting URL TikTok (grup Sosial). Ditambah lewat migration supaya muncul di panel produksi tanpa db:seed.
return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('site_settings')->where('key', 'social_tiktok')->exists()) {
            return;
        }

        DB::table('site_settings')->insert([
            'key' => 'social_tiktok',
            'value' => '',
            'group' => 'social',
            'label' => 'URL TikTok',
            'type' => 'url',
            'description' => 'Contoh: https://www.tiktok.com/@namaakun',
            'order' => 4,
            'is_translatable' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('site_settings')->where('key', 'social_tiktok')->delete();
    }
};
