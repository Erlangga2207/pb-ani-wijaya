<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Hero Section
            ['key' => 'hero_badge', 'value' => "INDRAMAYU'S FINEST", 'group' => 'hero', 'label' => 'Hero Badge Text', 'type' => 'text', 'order' => 1],
            ['key' => 'hero_title', 'value' => 'Heritage of Excellence', 'group' => 'hero', 'label' => 'Hero Main Title', 'type' => 'text', 'order' => 2],
            ['key' => 'hero_subtitle', 'value' => 'Mutu Terjamin', 'group' => 'hero', 'label' => 'Hero Subtitle (italic)', 'type' => 'text', 'order' => 3],
            ['key' => 'hero_description', 'value' => 'Elevating the standard of Indonesian staple grain through meticulous cultivation and modern processing techniques in the heart of Indramayu.', 'group' => 'hero', 'label' => 'Hero Description', 'type' => 'textarea', 'order' => 4],

            // Company Info
            ['key' => 'company_name', 'value' => 'PB. Ani Wijaya', 'group' => 'general', 'label' => 'Company Name', 'type' => 'text', 'order' => 1],
            ['key' => 'company_tagline', 'value' => 'Premium Rice Producer', 'group' => 'general', 'label' => 'Tagline', 'type' => 'text', 'order' => 2],
            ['key' => 'company_nib', 'value' => '1412210030165', 'group' => 'general', 'label' => 'NIB Registration', 'type' => 'text', 'order' => 3],
            ['key' => 'years_experience', 'value' => '25', 'group' => 'general', 'label' => 'Years of Experience', 'type' => 'text', 'description' => 'Angka saja, tanpa "tahun"', 'order' => 4],
            ['key' => 'partners_count', 'value' => '500', 'group' => 'general', 'label' => 'Number of Partners', 'type' => 'text', 'order' => 5],

            // About
            ['key' => 'about_title', 'value' => 'A Journey of Resilience and Excellence', 'group' => 'about', 'label' => 'About Page Title', 'type' => 'text', 'order' => 1],
            ['key' => 'about_paragraph_1', 'value' => 'Founded in the heart of Indramayu, West Java, PB. Ani Wijaya began as a humble family dream to provide consistent, high-quality rice to local communities. What started as a single mill has evolved into a premier industrial rice producer, serving wholesale distributors across Indonesia.', 'group' => 'about', 'label' => 'About Paragraph 1', 'type' => 'textarea', 'order' => 2],
            ['key' => 'about_paragraph_2', 'value' => 'The name "Ani Wijaya" is synonymous with trust. We understand that rice is more than just a staple; it is the center of the Indonesian dining table. This profound responsibility drives our "Mutu Terjamin" (Guaranteed Quality) philosophy, ensuring that every bag leaving our warehouse meets the highest standards of purity, texture, and taste.', 'group' => 'about', 'label' => 'About Paragraph 2', 'type' => 'textarea', 'order' => 3],

            // Contact
            ['key' => 'contact_phone', 'value' => '+62 813-2431-4300', 'group' => 'contact', 'label' => 'Phone / WhatsApp', 'type' => 'phone', 'order' => 1],
            ['key' => 'contact_email', 'value' => 'info@pbaniwijaya.com', 'group' => 'contact', 'label' => 'Email', 'type' => 'email', 'order' => 2],
            ['key' => 'contact_address', 'value' => "Kec. Losarang, Kab. Indramayu\nJawa Barat, Indonesia 45253", 'group' => 'contact', 'label' => 'Full Address', 'type' => 'textarea', 'order' => 3],
            ['key' => 'contact_short_address', 'value' => 'Indramayu, Jawa Barat', 'group' => 'contact', 'label' => 'Short Address (for footer)', 'type' => 'text', 'order' => 4],
            ['key' => 'maps_embed_url', 'value' => '', 'group' => 'contact', 'label' => 'Google Maps Embed URL', 'type' => 'url', 'description' => 'Paste embed URL dari Google Maps (untuk iframe)', 'order' => 5],
            ['key' => 'contact_maps_link', 'value' => 'https://maps.app.goo.gl/f9F7nw54f1uvdswc6', 'group' => 'contact', 'label' => 'Google Maps Link (Share URL)', 'type' => 'url', 'description' => 'URL share/shortlink Google Maps untuk tombol Petunjuk Arah', 'order' => 6],

            // Social
            ['key' => 'social_whatsapp', 'value' => '6281324314300', 'group' => 'social', 'label' => 'WhatsApp Number', 'type' => 'text', 'description' => 'Format: 62812... tanpa +', 'order' => 1],
            ['key' => 'social_instagram', 'value' => '', 'group' => 'social', 'label' => 'Instagram URL', 'type' => 'url', 'order' => 2],
            ['key' => 'social_facebook', 'value' => '', 'group' => 'social', 'label' => 'Facebook URL', 'type' => 'url', 'order' => 3],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
