<?php

namespace App\Models;

use App\Enums\BuyerType;
use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'city',
        'province',
        'country',
        'type',
        'notes',
        'is_active',
    ];

    protected $attributes = [
        'type' => 'retail',
        'country' => 'Indonesia',
        'is_active' => true,
    ];

    protected $casts = [
        'type' => BuyerType::class,
        'is_active' => 'boolean',
    ];
}
