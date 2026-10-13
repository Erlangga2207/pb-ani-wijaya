<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'label',
        'type',
        'is_translatable',
        'description',
        'order',
    ];

    protected $casts = [
        'is_translatable' => 'boolean',
    ];

    /**
     * Static helper untuk get setting by key, sesuai bahasa aktif.
     * Usage: SiteSetting::get('hero_title', 'Default Title')
     */
    public static function get(string $key, $default = null): ?string
    {
        $locale = app()->getLocale();

        // Default tidak ikut di-cache: panggilan lain dengan default berbeda tetap benar.
        $value = Cache::remember("setting.{$locale}.{$key}", 3600, function () use ($key, $locale) {
            return self::where('key', $key)->first()?->valueFor($locale);
        });

        return $value ?? $default;
    }

    /**
     * Nilai untuk satu bahasa. Setting yang bisa diterjemahkan disimpan JSON {"id": "...", "en": "..."};
     * terjemahan kosong → bahasa default (id), supaya halaman tidak menampilkan string kosong.
     */
    public function valueFor(string $locale): ?string
    {
        if (! $this->is_translatable) {
            return $this->value;
        }

        $translations = $this->translations();
        $default = array_key_first(config('locales'));

        return filled($translations[$locale] ?? null) ? $translations[$locale] : ($translations[$default] ?? null);
    }

    /** @return array<string, string|null> */
    public function translations(): array
    {
        $decoded = json_decode((string) $this->value, true);

        return is_array($decoded) ? $decoded : [array_key_first(config('locales')) => $this->value];
    }

    /**
     * Clear cache saat setting diupdate (semua bahasa)
     */
    protected static function booted(): void
    {
        $forget = function (SiteSetting $setting) {
            foreach (array_keys(config('locales')) as $locale) {
                Cache::forget("setting.{$locale}.{$setting->key}");
            }
        };

        static::saved($forget);
        static::deleted($forget);
    }
}
