<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Katalog produk untuk website. Stok ada di App\Models\Item.
class Product extends Model
{
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
