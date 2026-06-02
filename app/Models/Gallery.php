<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Gallery extends Model
{
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

    // Accessor: bersihkan file_path kalau ke-store sebagai JSON/array
    public function getFilePathAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        // Kalau value adalah JSON string, decode dan ambil item pertama
        if (is_string($value) && (str_starts_with($value, '[') || str_starts_with($value, '{'))) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded[0] ?? null;
            }
        }

        return $value;
    }

    public function getFileUrlAttribute(): string
    {
        if (!$this->file_path) {
            return '';
        }
        return Storage::url($this->file_path);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'origin' => '🌾 Origin',
            'processing' => '⚙️ Processing',
            'quality' => '✓ Quality Control',
            'logistics' => '📦 Logistics',
            'product' => '🍚 Final Product',
            'facility' => '🏭 Facility',
            'other' => '📷 Other',
            default => 'Other',
        };
    }
}
