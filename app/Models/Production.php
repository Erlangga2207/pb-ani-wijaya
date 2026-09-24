<?php

namespace App\Models;

use App\Models\Concerns\IsDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Satu batch penggilingan: gabah masuk mesin -> beras + hasil samping.
class Production extends Model
{
    use IsDocument;

    protected $guarded = ['id'];

    protected $casts = [
        'production_date' => 'date',
        'input_kg' => 'decimal:2',
        'input_cost_per_kg' => 'decimal:2',
        'input_total_cost' => 'decimal:2',
        'main_output_kg' => 'decimal:2',
        'yield_percent' => 'decimal:2',
        'shrinkage_kg' => 'decimal:2',
        'machine_downtime_minutes' => 'integer',
    ];

    public function inputItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'input_item_id');
    }

    public function outputs(): HasMany
    {
        return $this->hasMany(ProductionOutput::class);
    }

    // Di luar rentang rendemen normal (config/production.php) -> peringatan, bukan blokir.
    public function isYieldOutOfRange(): bool
    {
        $yield = (float) $this->yield_percent;

        return $yield < config('production.yield_min') || $yield > config('production.yield_max');
    }
}
