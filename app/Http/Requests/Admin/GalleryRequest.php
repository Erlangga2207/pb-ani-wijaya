<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\TranslatableRules;
use App\Models\Gallery;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GalleryRequest extends FormRequest
{
    use TranslatableRules;

    public function rules(): array
    {
        return [
            ...$this->translatableRules('title', true, 255),
            ...$this->translatableRules('description', false, 1000),
            'category' => ['required', Rule::in(array_keys(Gallery::CATEGORIES))],
            // Foto JPG/PNG/WEBP maks. 5 MB, video MP4 maks. 50 MB. Wajib saat tambah, opsional saat ubah.
            'file' => [
                $this->route('gallery') ? 'nullable' : 'required',
                'file',
                'mimes:jpg,jpeg,png,webp,mp4',
                'max:51200',
                function (string $attribute, $file, Closure $fail) {
                    if (strtolower($file->getClientOriginalExtension()) !== 'mp4' && $file->getSize() > 5 * 1024 * 1024) {
                        $fail('Foto maksimal 5 MB.');
                    }
                },
            ],
            'order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            ...$this->translatableAttributes('title', 'judul'),
            ...$this->translatableAttributes('description', 'deskripsi'),
            'category' => 'kategori',
            'file' => 'file',
            'order' => 'urutan',
        ];
    }

    public function galleryData(): array
    {
        $data = $this->safe()->except('file');
        $data['title'] = $this->cleanTranslations($data['title']);
        $data['description'] = $this->cleanTranslations($data['description'] ?? []);
        $data['order'] = (int) ($data['order'] ?? 0);

        return $data;
    }
}
