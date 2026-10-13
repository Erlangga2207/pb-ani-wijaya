<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Gallery extends Model
{
    use HasTranslations;

    // Teks per bahasa (JSON). Kosong → bahasa default (id).
    public array $translatable = ['title', 'description'];

    // Label kategori untuk admin (Bahasa Indonesia). Label di website: getCategoryLabelAttribute().
    public const CATEGORIES = [
        'origin' => 'Asal padi',
        'processing' => 'Produksi',
        'quality' => 'Kontrol kualitas',
        'logistics' => 'Logistik',
        'product' => 'Produk jadi',
        'facility' => 'Fasilitas',
        'other' => 'Lainnya',
    ];

    protected $fillable = [
        'title',
        'description',
        'category',
        'file_path',
        'file_type',
        'order',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function getFileUrlAttribute(): string
    {
        return $this->file_path ? asset('storage/'.$this->file_path) : '';
    }

    // galeri.blade.php memakai $gallery->caption; kolomnya bernama description.
    public function getCaptionAttribute(): ?string
    {
        return $this->description;
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'origin' => __('🌾 Origin'),
            'processing' => __('⚙️ Processing'),
            'quality' => __('✓ Quality Control'),
            'logistics' => __('📦 Logistics'),
            'product' => __('🍚 Final Product'),
            'facility' => __('🏭 Facility'),
            'other' => __('📷 Other'),
            default => __('Other'),
        };
    }
}
