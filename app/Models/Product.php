<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

// Katalog produk untuk website. Stok ada di App\Models\Item.
class Product extends Model
{
    use HasTranslations;

    // Teks per bahasa (JSON). Kosong → bahasa default (id).
    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'weight_per_karung',
        'is_active',
    ];

    protected $casts = [
        'weight_per_karung' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
