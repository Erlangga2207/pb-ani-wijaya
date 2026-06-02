<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    // Relasi: 1 product punya banyak transaksi
    public function transactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    // Accessor: stok real-time dalam kg
    // Bisa dipakai dengan: $product->current_stock_kg
    public function getCurrentStockKgAttribute(): float
    {
        $in = $this->transactions()->where('type', 'in')->sum('quantity_kg');
        $out = $this->transactions()->where('type', 'out')->sum('quantity_kg');
        return (float) ($in - $out);
    }

    // Accessor: stok dalam karung (dibulatkan ke bawah)
    public function getCurrentStockKarungAttribute(): float
    {
        if ($this->weight_per_karung <= 0) return 0;
        return (float) ($this->current_stock_kg / $this->weight_per_karung);
    }
}
