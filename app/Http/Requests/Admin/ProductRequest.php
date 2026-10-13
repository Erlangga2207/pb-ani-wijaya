<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\TranslatableRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    use TranslatableRules;

    // Slug otomatis dari nama (ID) saat dikosongkan. Slug tidak diterjemahkan dan tidak diubah otomatis saat edit.
    protected function prepareForValidation(): void
    {
        if (blank($this->input('slug'))) {
            $this->merge(['slug' => Str::slug((string) $this->input('name.'.array_key_first(config('locales'))))]);
        }
    }

    public function rules(): array
    {
        return [
            ...$this->translatableRules('name', true, 255),
            ...$this->translatableRules('description', false, 5000),
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('products', 'slug')->ignore($this->route('product'))],
            'weight_per_karung' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            ...$this->translatableAttributes('name', 'nama produk'),
            ...$this->translatableAttributes('description', 'deskripsi'),
            'weight_per_karung' => 'berat per karung',
        ];
    }

    /** Data siap simpan: bahasa yang dikosongkan dibuang. */
    public function productData(): array
    {
        $data = $this->validated();
        $data['name'] = $this->cleanTranslations($data['name']);
        $data['description'] = $this->cleanTranslations($data['description'] ?? []);

        return $data;
    }
}
