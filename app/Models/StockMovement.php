<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LogicException;

// Buku besar stok. Append-only: baris tidak boleh diubah/dihapus.
class StockMovement extends Model
{
    public const UPDATED_AT = null;

    protected $guarded = ['id'];

    protected $casts = [
        'movement_date' => 'date',
        'quantity_kg' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'balance_after_kg' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Mutasi stok tidak boleh diubah.'));
        static::deleting(fn () => throw new LogicException('Mutasi stok tidak boleh dihapus.'));
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
