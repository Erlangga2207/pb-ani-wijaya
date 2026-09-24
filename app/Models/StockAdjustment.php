<?php

namespace App\Models;

use App\Models\Concerns\IsDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    use IsDocument;

    protected $guarded = ['id'];

    protected $casts = [
        'adjustment_date' => 'date',
        'system_kg' => 'decimal:2',
        'physical_kg' => 'decimal:2',
        'difference_kg' => 'decimal:2',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
