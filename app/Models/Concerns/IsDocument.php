<?php

namespace App\Models\Concerns;

use App\Enums\DocumentStatus;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Validation\ValidationException;

// Kolom bersama dokumen transaksi: number, status, void_reason, voided_at, voided_by, user_id.
trait IsDocument
{
    public function initializeIsDocument(): void
    {
        $this->mergeCasts([
            'status' => DocumentStatus::class,
            'voided_at' => 'datetime',
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'source');
    }

    public function isVoid(): bool
    {
        return $this->status === DocumentStatus::Void;
    }

    public function scopePosted(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), DocumentStatus::Posted);
    }

    /**
     * Ambil ulang dokumen dengan lock (cegah void dobel) lalu pastikan boleh dibatalkan.
     * Panggil di dalam DB::transaction.
     */
    public function lockForVoid(string $reason): static
    {
        if (trim($reason) === '') {
            throw ValidationException::withMessages(['reason' => 'Alasan pembatalan wajib diisi.']);
        }

        $fresh = static::query()->lockForUpdate()->findOrFail($this->getKey());

        if ($fresh->isVoid()) {
            throw ValidationException::withMessages(['reason' => 'Dokumen sudah dibatalkan.']);
        }

        return $fresh;
    }

    public function markVoid(string $reason, User $by): void
    {
        $this->forceFill([
            'status' => DocumentStatus::Void,
            'void_reason' => $reason,
            'voided_at' => now(),
            'voided_by' => $by->id,
        ])->save();
    }
}
