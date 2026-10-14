<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Alamat dipisah: contact_address = kantor (Subang), factory_address = pabrik (Sukra, Indramayu).
// Hanya label/urutan & baris baru; nilai yang sudah diisi admin tidak diubah.
return new class extends Migration
{
    private const LAYOUT = [
        'contact_address' => ['label' => 'Alamat kantor', 'description' => 'Alamat bisnis utama (didatangi pembeli). Dipakai di footer, nota, dan Google (JSON-LD).', 'order' => 3],
        'contact_short_address' => ['order' => 5],
        'maps_embed_url' => ['order' => 6],
        'contact_maps_link' => ['order' => 7],
    ];

    public function up(): void
    {
        if (! DB::table('site_settings')->where('key', 'factory_address')->exists()) {
            DB::table('site_settings')->insert([
                'key' => 'factory_address',
                'value' => "Kec. Sukra, Kab. Indramayu\nJawa Barat, Indonesia 45259",
                'group' => 'contact',
                'label' => 'Alamat pabrik',
                'type' => 'textarea',
                'description' => 'Lokasi produksi, tampil di halaman Kontak.',
                'order' => 4,
                'is_translatable' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (self::LAYOUT as $key => $columns) {
            DB::table('site_settings')->where('key', $key)->update($columns);
        }
    }

    public function down(): void
    {
        DB::table('site_settings')->where('key', 'factory_address')->delete();
        DB::table('site_settings')->where('key', 'contact_address')->update(['label' => 'Alamat lengkap', 'description' => null]);
    }
};
