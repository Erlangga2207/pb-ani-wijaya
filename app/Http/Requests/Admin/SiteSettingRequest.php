<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\TranslatableRules;
use App\Models\SiteSetting;
use Illuminate\Foundation\Http\FormRequest;

// Aturan dibentuk dari tabel site_settings: tipe url/email divalidasi, teks terjemahan wajib di ID.
class SiteSettingRequest extends FormRequest
{
    use TranslatableRules;

    public function rules(): array
    {
        $rules = ['settings' => ['required', 'array']];

        foreach (SiteSetting::all() as $setting) {
            $field = "settings.{$setting->key}";
            $max = $setting->type === 'textarea' ? 5000 : 255;

            if ($setting->is_translatable) {
                $rules += $this->translatableRules($field, true, $max);

                continue;
            }

            $rules[$field] = match ($setting->type) {
                'url' => ['nullable', 'url', 'max:2000'],
                'email' => ['nullable', 'email', 'max:255'],
                default => ['nullable', 'string', "max:{$max}"],
            };
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [];
        foreach (SiteSetting::all() as $setting) {
            $field = "settings.{$setting->key}";
            $attributes += $setting->is_translatable
                ? $this->translatableAttributes($field, $setting->label)
                : [$field => $setting->label];
        }

        return $attributes;
    }

    /** [key => nilai] untuk setting biasa, [key => [locale => teks]] untuk yang bisa diterjemahkan. */
    public function settingValues(): array
    {
        return collect($this->validated('settings'))
            ->map(fn ($value) => is_array($value) ? $this->cleanTranslations($value) : $value)
            ->all();
    }
}
