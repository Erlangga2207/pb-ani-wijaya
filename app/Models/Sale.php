<?php

namespace App\Models;

use App\Models\Concerns\HasPayments;
use App\Models\Concerns\IsDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Penjualan (nota). Tidak bisa diedit/dihapus, hanya void.
class Sale extends Model
{
    use HasPayments, IsDocument;

    protected $guarded = ['id'];

    protected $casts = [
        'sale_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'cogs_total' => 'decimal:2',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // Laba kotor nota = penjualan barang (setelah diskon) − HPP. Ongkir tidak termasuk.
    public function grossProfit(): float
    {
        return round((float) $this->subtotal - (float) $this->discount - (float) $this->cogs_total, 2);
    }

    // Link WhatsApp ke HP pembeli, null bila tidak ada nomor.
    public function whatsappUrl(string $text): ?string
    {
        $phone = preg_replace('/\D/', '', (string) $this->buyer?->phone);
        if ($phone === '') {
            return null;
        }
        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        return "https://wa.me/{$phone}?text=".rawurlencode($text);
    }
}
