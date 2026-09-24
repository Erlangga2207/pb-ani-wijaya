<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

// Pembayaran untuk dokumen yang memakai trait HasPayments (Purchase, Sale).
class PaymentService
{
    public function __construct(private DocumentNumberService $numbers) {}

    /**
     * @param  array{amount:float, payment_date?:string, method?:string|PaymentMethod, notes?:?string}  $data
     */
    public function record(Model $payable, array $data, User $user): Payment
    {
        return DB::transaction(function () use ($payable, $data, $user) {
            $payable = $payable->newQuery()->lockForUpdate()->findOrFail($payable->getKey());

            if ($payable->isVoid()) {
                throw ValidationException::withMessages(['amount' => 'Dokumen sudah dibatalkan.']);
            }

            $amount = round((float) ($data['amount'] ?? 0), 2);
            $remaining = $payable->remainingAmount();

            if ($amount <= 0) {
                throw ValidationException::withMessages(['amount' => 'Jumlah bayar harus lebih dari 0.']);
            }
            if ($amount > $remaining) {
                throw ValidationException::withMessages([
                    'amount' => 'Jumlah bayar melebihi sisa tagihan (Rp '.number_format($remaining, 2, ',', '.').').',
                ]);
            }

            $date = Carbon::parse($data['payment_date'] ?? today());
            if ($date->isAfter(today())) {
                throw ValidationException::withMessages(['payment_date' => 'Tanggal bayar tidak boleh di masa depan.']);
            }

            $payment = $payable->payments()->create([
                'number' => $this->numbers->next('BY', $date),
                'payment_date' => $date,
                'amount' => $amount,
                'method' => $data['method'] ?? PaymentMethod::Cash,
                'notes' => $data['notes'] ?? null,
                'status' => DocumentStatus::Posted,
                'user_id' => $user->id,
            ]);

            $this->recalculate($payable);

            return $payment;
        });
    }

    public function void(Payment $payment, string $reason, User $user): void
    {
        DB::transaction(function () use ($payment, $reason, $user) {
            $payment = $payment->lockForVoid($reason);
            $payment->markVoid($reason, $user);
            $this->recalculate($payment->payable);
        });
    }

    // Dipanggil saat dokumen induk di-void: semua pembayarannya ikut batal.
    public function voidAllFor(Model $payable, string $reason, User $user): void
    {
        $payable->payments()->posted()->get()->each(fn (Payment $p) => $p->markVoid($reason, $user));
        $this->recalculate($payable);
    }

    public function recalculate(Model $payable): void
    {
        $paid = round((float) $payable->payments()->posted()->sum('amount'), 2);
        $total = round((float) $payable->total, 2);

        $payable->forceFill([
            'paid_amount' => $paid,
            'payment_status' => match (true) {
                $paid <= 0 => PaymentStatus::Unpaid,
                $paid < $total => PaymentStatus::Partial,
                default => PaymentStatus::Paid,
            },
        ])->save();
    }
}
