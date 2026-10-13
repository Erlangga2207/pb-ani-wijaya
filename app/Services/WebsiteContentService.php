<?php

namespace App\Services;

use App\Models\Gallery;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Modul Website di panel: katalog produk, galeri, pengaturan website (PRD-07 + PRD-08 multi-bahasa).
class WebsiteContentService
{
    private const TRANSLATABLE_FIELDS = [
        Product::class => ['name', 'description'],
        Gallery::class => ['title', 'description'],
    ];

    public function saveProduct(Product $product, array $data): Product
    {
        $this->fill($product, $data)->save();

        return $product;
    }

    public function createGallery(array $data, UploadedFile $file): Gallery
    {
        $gallery = $this->fill(new Gallery, $data);
        $gallery->fill($this->storeFile($file))->save();

        return $gallery;
    }

    public function updateGallery(Gallery $gallery, array $data, ?UploadedFile $file): Gallery
    {
        $oldPath = $gallery->file_path;
        $this->fill($gallery, $data);

        if ($file) {
            $gallery->fill($this->storeFile($file));
        }
        $gallery->save();

        // File lama dihapus setelah data baru tersimpan.
        if ($file && $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return $gallery;
    }

    public function deleteGallery(Gallery $gallery): void
    {
        $gallery->delete();
        Storage::disk('public')->delete($gallery->file_path);
    }

    /**
     * @param  array<string, string|array<string, string>|null>  $values  key => nilai, atau key => [locale => teks]
     */
    public function updateSettings(array $values): void
    {
        DB::transaction(function () use ($values) {
            foreach (SiteSetting::all() as $setting) {
                if (! array_key_exists($setting->key, $values)) {
                    continue;
                }

                $value = $values[$setting->key];
                $setting->value = $setting->is_translatable
                    ? json_encode($value, JSON_UNESCAPED_UNICODE)
                    : $value;

                // save() per baris supaya event "saved" mereset cache SiteSetting::get() semua bahasa.
                if ($setting->isDirty('value')) {
                    $setting->save();
                }
            }
        });
    }

    /** Isi atribut biasa + terjemahan. Bahasa yang tidak dikirim/dikosongkan dihapus (tampil versi bahasa default). */
    private function fill(Model $model, array $data): Model
    {
        $fields = self::TRANSLATABLE_FIELDS[$model::class];
        $model->fill(collect($data)->except($fields)->all());

        foreach ($fields as $field) {
            $translations = $data[$field] ?? [];

            if ($translations === []) {
                // Kosong di semua bahasa → NULL (bukan "{}"), supaya teks cadangan di halaman publik tetap muncul.
                $model->setRawAttributes([$field => null] + $model->getAttributes());
            } else {
                $model->replaceTranslations($field, $translations);
            }
        }

        return $model;
    }

    /** @return array{file_path: string, file_type: string} */
    private function storeFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'galeri';

        return [
            // Akhiran acak supaya file bernama sama tidak saling menimpa.
            'file_path' => $file->storeAs('gallery', "{$name}-".Str::lower(Str::random(6)).".{$extension}", 'public'),
            'file_type' => $extension === 'mp4' ? 'video' : 'image',
        ];
    }
}
