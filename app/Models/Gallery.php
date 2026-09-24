<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
