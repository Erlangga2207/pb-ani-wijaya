<?php

namespace App\Http\Requests\Admin\Concerns;

// Aturan validasi field yang bisa diterjemahkan: input {field}[id], {field}[en], ...
// Bahasa default (id) wajib bila $required; bahasa lain boleh kosong (tampil versi id).
trait TranslatableRules
{
    protected function translatableRules(string $field, bool $required = true, int $max = 255): array
    {
        $locales = array_keys(config('locales'));
        $default = $locales[0];

        return [
            $field => [$required ? 'required' : 'nullable', 'array:'.implode(',', $locales)],
            "{$field}.{$default}" => [$required ? 'required' : 'nullable', 'string', "max:{$max}"],
            "{$field}.*" => ['nullable', 'string', "max:{$max}"],
        ];
    }

    protected function translatableAttributes(string $field, string $label): array
    {
        $attributes = [$field => $label];
        foreach (config('locales') as $code => $locale) {
            $attributes["{$field}.{$code}"] = "{$label} ({$locale['tab']})";
        }

        return $attributes;
    }

    /** Buang bahasa yang dikosongkan supaya jatuh ke versi bahasa default, bukan string kosong. */
    protected function cleanTranslations(?array $values): array
    {
        return array_filter($values ?? [], fn ($v) => filled($v));
    }
}
