<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Models\Concerns\IsDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use IsDocument;

    protected $guarded = ['id'];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'method' => PaymentMethod::class,
    ];

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}
