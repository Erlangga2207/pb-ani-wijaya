<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Hero Section
            ['key' => 'hero_badge', 'group' => 'hero', 'label' => 'Teks lencana hero', 'type' => 'text', 'order' => 1],
            ['key' => 'hero_title', 'group' => 'hero', 'label' => 'Judul utama hero', 'type' => 'text', 'order' => 2],
            ['key' => 'hero_subtitle', 'group' => 'hero', 'label' => 'Subjudul hero (miring)', 'type' => 'text', 'order' => 3],
            ['key' => 'hero_description', 'group' => 'hero', 'label' => 'Deskripsi hero', 'type' => 'textarea', 'order' => 4],

            // Company Info
            ['key' => 'company_name', 'value' => 'PB. Ani Wijaya', 'group' => 'general', 'label' => 'Nama perusahaan', 'type' => 'text', 'order' => 1],
            ['key' => 'company_tagline', 'group' => 'general', 'label' => 'Tagline', 'type' => 'text', 'order' => 2],
            ['key' => 'company_nib', 'value' => '1412210030165', 'group' => 'general', 'label' => 'Nomor NIB', 'type' => 'text', 'order' => 3],
            ['key' => 'years_experience', 'value' => '25', 'group' => 'general', 'label' => 'Tahun pengalaman', 'type' => 'text', 'description' => 'Angka saja, tanpa "tahun"', 'order' => 4],
            ['key' => 'partners_count', 'value' => '500', 'group' => 'general', 'label' => 'Jumlah mitra', 'type' => 'text', 'order' => 5],

            // About
            ['key' => 'about_title', 'group' => 'about', 'label' => 'Judul halaman Tentang Kami', 'type' => 'text', 'order' => 1],
            ['key' => 'about_paragraph_1', 'group' => 'about', 'label' => 'Paragraf Tentang Kami 1', 'type' => 'textarea', 'order' => 2],
            ['key' => 'about_paragraph_2', 'group' => 'about', 'label' => 'Paragraf Tentang Kami 2', 'type' => 'textarea', 'order' => 3],

            // Contact
            ['key' => 'contact_phone', 'value' => '+62 813-2431-4300', 'group' => 'contact', 'label' => 'Telepon', 'type' => 'phone', 'order' => 1],
            ['key' => 'contact_email', 'value' => 'sales@aniwijaya.com', 'group' => 'contact', 'label' => 'Email', 'type' => 'email', 'order' => 2],
            ['key' => 'contact_address', 'value' => "Kec. Sukra, Kab. Indramayu\nJawa Barat, Indonesia 45259", 'group' => 'contact', 'label' => 'Alamat lengkap', 'type' => 'textarea', 'order' => 3],
            ['key' => 'contact_short_address', 'value' => 'Indramayu, Jawa Barat', 'group' => 'contact', 'label' => 'Alamat singkat (untuk footer)', 'type' => 'text', 'order' => 4],
            ['key' => 'maps_embed_url', 'value' => '', 'group' => 'contact', 'label' => 'URL embed Google Maps', 'type' => 'url', 'description' => 'Paste embed URL dari Google Maps (untuk iframe)', 'order' => 5],
            ['key' => 'contact_maps_link', 'value' => 'https://maps.app.goo.gl/f9F7nw54f1uvdswc6', 'group' => 'contact', 'label' => 'Link Google Maps (tombol Petunjuk Arah)', 'type' => 'url', 'description' => 'URL share/shortlink Google Maps untuk tombol Petunjuk Arah', 'order' => 6],

            // Social
            ['key' => 'social_whatsapp', 'value' => '6281324314300', 'group' => 'social', 'label' => 'Nomor WhatsApp', 'type' => 'text', 'description' => 'Format: 62812... tanpa +', 'order' => 1],
            ['key' => 'social_instagram', 'value' => '', 'group' => 'social', 'label' => 'URL Instagram', 'type' => 'url', 'order' => 2],
            ['key' => 'social_facebook', 'value' => '', 'group' => 'social', 'label' => 'URL Facebook', 'type' => 'url', 'order' => 3],

            // SEO (PRD-08)
            ['key' => 'seo_meta_title', 'group' => 'seo', 'label' => 'Judul SEO (beranda)', 'type' => 'text', 'description' => 'Tampil sebagai judul tab & hasil pencarian Google untuk beranda.', 'order' => 1],
            ['key' => 'seo_meta_description', 'group' => 'seo', 'label' => 'Deskripsi SEO', 'type' => 'textarea', 'description' => 'Ringkasan 1–2 kalimat untuk hasil pencarian Google.', 'order' => 2],
        ];

        // Teks yang bisa diterjemahkan: isi awal 5 bahasa (draf), disimpan sebagai JSON per bahasa.
        $translations = require database_path('data/site_setting_translations.php');

        foreach ($settings as $setting) {
            if (isset($translations[$setting['key']])) {
                $setting['is_translatable'] = true;
                $setting['value'] = json_encode(array_intersect_key($translations[$setting['key']], config('locales')), JSON_UNESCAPED_UNICODE);
            }

            // Aman dijalankan ulang di produksi: key baru dibuat lengkap; key yang sudah ada hanya
            // diperbarui label/grup/tipe/deskripsi/urutan — nilai yang diedit admin tidak ditimpa.
            $existing = SiteSetting::firstWhere('key', $setting['key']);

            if ($existing) {
                $existing->update(Arr::except($setting, ['key', 'value', 'is_translatable']));
            } else {
                SiteSetting::create($setting);
            }
        }
    }
}
