<?php

namespace App\Models;

use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Pesan dari form kontak website = calon pembeli (lead).
class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'country',
        'subject',
        'message',
        'status',
        'buyer_id',
        'handled_by',
        'ip_address',
        'is_read',
        'admin_notes',
        'read_at',
    ];

    protected $attributes = [
        'status' => 'new',
    ];

    protected $casts = [
        'status' => LeadStatus::class,
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    // Mark as read otomatis saat dilihat
    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function whatsappUrl(): ?string
    {
        $phone = preg_replace('/\D/', '', (string) $this->phone);
        if ($phone === '') {
            return null;
        }
        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        return "https://wa.me/{$phone}?text=".rawurlencode("Halo {$this->name}, terima kasih sudah menghubungi kami.");
    }
}
