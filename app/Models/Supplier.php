<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
