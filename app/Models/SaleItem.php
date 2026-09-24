<?php

namespace App\Models;

use App\Enums\QuantityUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'input_unit' => QuantityUnit::class,
        'input_quantity' => 'decimal:2',
        'quantity_kg' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'line_total' => 'decimal:2',
        'cogs_per_kg' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
