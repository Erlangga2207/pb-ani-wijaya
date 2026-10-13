<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Supplier = petani pemasok gabah.
class Supplier extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'village',
        'district',
        'city',
        'notes',
        'is_active',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}
