<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// PRD-08 Fase 6: pengaturan website yang bisa diterjemahkan.
// - Kolom baru is_translatable; hanya key di database/data/site_setting_translations.php (keputusan Fase 0 no. 1).
// - Nilai yang MASIH SAMA dengan default seeder lama → diisi draf 5 bahasa (keputusan no. 4).
//   Nilai yang sudah diedit admin tidak diubah, hanya dibungkus ke key "id".
// - Key baru seo_meta_title & seo_meta_description (keputusan no. 3).
// - Label diganti Bahasa Indonesia (admin panel hanya Bahasa Indonesia).
return new class extends Migration
{
    private const LABELS = [
        'hero_badge' => ['Hero Badge Text', 'Teks lencana hero'],
        'hero_title' => ['Hero Main Title', 'Judul utama hero'],
        'hero_subtitle' => ['Hero Subtitle (italic)', 'Subjudul hero (miring)'],
        'hero_description' => ['Hero Description', 'Deskripsi hero'],
        'company_name' => ['Company Name', 'Nama perusahaan'],
        'company_tagline' => ['Tagline', 'Tagline'],
        'company_nib' => ['NIB Registration', 'Nomor NIB'],
        'years_experience' => ['Years of Experience', 'Tahun pengalaman'],
        'partners_count' => ['Number of Partners', 'Jumlah mitra'],
        'about_title' => ['About Page Title', 'Judul halaman Tentang Kami'],
        'about_paragraph_1' => ['About Paragraph 1', 'Paragraf Tentang Kami 1'],
        'about_paragraph_2' => ['About Paragraph 2', 'Paragraf Tentang Kami 2'],
        'contact_phone' => ['Phone / WhatsApp', 'Telepon'],
        'contact_email' => ['Email', 'Email'],
        'contact_address' => ['Full Address', 'Alamat lengkap'],
        'contact_short_address' => ['Short Address (for footer)', 'Alamat singkat (untuk footer)'],
        'maps_embed_url' => ['Google Maps Embed URL', 'URL embed Google Maps'],
        'contact_maps_link' => ['Google Maps Link (Share URL)', 'Link Google Maps (tombol Petunjuk Arah)'],
        'social_whatsapp' => ['WhatsApp Number', 'Nomor WhatsApp'],
        'social_instagram' => ['Instagram URL', 'URL Instagram'],
        'social_facebook' => ['Facebook URL', 'URL Facebook'],
    ];

    private const NEW_KEYS = [
        'seo_meta_title' => ['label' => 'Judul SEO (beranda)', 'type' => 'text', 'order' => 1, 'description' => 'Tampil sebagai judul tab & hasil pencarian Google untuk beranda.'],
        'seo_meta_description' => ['label' => 'Deskripsi SEO', 'type' => 'textarea', 'order' => 2, 'description' => 'Ringkasan 1–2 kalimat untuk hasil pencarian Google.'],
    ];

    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('is_translatable')->default(false)->after('type');
        });

        $drafts = require database_path('data/site_setting_translations.php');

        foreach ($drafts as $key => $draft) {
            $row = DB::table('site_settings')->where('key', $key)->first();

            if (! $row && isset(self::NEW_KEYS[$key])) {
                DB::table('site_settings')->insert(self::NEW_KEYS[$key] + [
                    'key' => $key,
                    'value' => $this->json($draft),
                    'group' => 'seo',
                    'is_translatable' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                continue;
            }

            if (! $row) {
                continue;
            }

            $value = $row->value === $draft['old']
                ? $this->json($draft)
                : ($row->value === null ? null : json_encode(['id' => $row->value], JSON_UNESCAPED_UNICODE));

            DB::table('site_settings')->where('id', $row->id)->update(['value' => $value, 'is_translatable' => true]);
        }

        foreach (self::LABELS as $key => [, $label]) {
            DB::table('site_settings')->where('key', $key)->update(['label' => $label]);
        }
    }

    public function down(): void
    {
        $drafts = require database_path('data/site_setting_translations.php');

        foreach (DB::table('site_settings')->where('is_translatable', true)->get() as $row) {
            $translations = json_decode((string) $row->value, true);
            $value = is_array($translations) ? ($translations['id'] ?? null) : $row->value;

            // Versi ID masih draf bawaan (belum diedit admin) → kembalikan teks lama, supaya rollback tidak mengubah isi website.
            $draft = $drafts[$row->key] ?? null;
            if ($draft && $draft['old'] !== null && $value === $draft['id']) {
                $value = $draft['old'];
            }

            DB::table('site_settings')->where('id', $row->id)->update(['value' => $value]);
        }

        DB::table('site_settings')->whereIn('key', array_keys(self::NEW_KEYS))->delete();

        foreach (self::LABELS as $key => [$label]) {
            DB::table('site_settings')->where('key', $key)->update(['label' => $label]);
        }

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('is_translatable');
        });
    }

    private function json(array $draft): string
    {
        return json_encode(array_intersect_key($draft, config('locales')), JSON_UNESCAPED_UNICODE);
    }
};
