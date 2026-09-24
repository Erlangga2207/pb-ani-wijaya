<?php

namespace App\Models\Concerns;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

// Dokumen yang bisa dicicil: pembelian (utang) & penjualan (piutang).
// Kolom: total, paid_amount, payment_status, due_date.
trait HasPayments
{
    public function initializeHasPayments(): void
    {
        $this->mergeCasts([
            'payment_status' => PaymentStatus::class,
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
        ]);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function remainingAmount(): float
    {
        return round((float) $this->total - (float) $this->paid_amount, 2);
    }

    public function isOverdue(): bool
    {
        return $this->payment_status !== PaymentStatus::Paid
            && $this->due_date !== null
            && $this->due_date->lt(today());
    }
}
