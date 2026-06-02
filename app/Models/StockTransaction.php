<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    protected $fillable = [
        'type',
        'transaction_date',
        'reference_number',
        'product_id',
        'supplier_id',
        'buyer_id',
        'quantity_kg',
        'input_quantity',
        'input_unit',
        'price_per_kg',
        'total_price',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'quantity_kg' => 'decimal:2',
        'input_quantity' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Relasi: setiap transaksi punya 1 produk
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi: setiap transaksi punya 1 supplier (opsional)
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relasi: setiap transaksi punya 1 buyer (opsional)
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    // Relasi: siapa user yang input
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
