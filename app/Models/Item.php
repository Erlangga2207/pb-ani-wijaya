<?php

namespace App\Models;

use App\Enums\ItemType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    // stock_kg & avg_cost_per_kg sengaja tidak fillable: hanya StockService yang mengubah.
    protected $fillable = [
        'code',
        'name',
        'type',
        'product_id',
        'weight_per_sack_kg',
        'default_price_per_kg',
        'min_stock_kg',
        'is_active',
    ];

    protected $casts = [
        'type' => ItemType::class,
        'weight_per_sack_kg' => 'decimal:2',
        'stock_kg' => 'decimal:2',
        'avg_cost_per_kg' => 'decimal:2',
        'default_price_per_kg' => 'decimal:2',
        'min_stock_kg' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeOfType(Builder $query, ItemType ...$types): Builder
    {
        return $query->whereIn('type', $types);
    }

    public function isLowStock(): bool
    {
        return $this->min_stock_kg !== null && (float) $this->stock_kg <= (float) $this->min_stock_kg;
    }

    public function stockInSacks(): float
    {
        return (float) $this->weight_per_sack_kg > 0
            ? round((float) $this->stock_kg / (float) $this->weight_per_sack_kg, 1)
            : 0;
    }

    public function toKg(float $quantity, string $unit): float
    {
        return round($unit === 'sack' ? $quantity * (float) $this->weight_per_sack_kg : $quantity, 2);
    }
}
