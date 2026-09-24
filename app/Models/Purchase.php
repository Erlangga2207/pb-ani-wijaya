<?php

namespace App\Models;

use App\Enums\QuantityUnit;
use App\Models\Concerns\HasPayments;
use App\Models\Concerns\IsDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Pembelian gabah dari petani. Tidak bisa diedit/dihapus, hanya void.
class Purchase extends Model
{
    use HasPayments, IsDocument;

    protected $guarded = ['id'];

    protected $casts = [
        'purchase_date' => 'date',
        'input_unit' => QuantityUnit::class,
        'input_quantity' => 'decimal:2',
        'quantity_kg' => 'decimal:2',
        'moisture_percent' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
