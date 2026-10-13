<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Models\Buyer;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LeadService
{
    // "Jadikan Pembeli": buat Buyer dari data pesan lalu tautkan.
    public function convertToBuyer(ContactMessage $lead, User $user): Buyer
    {
        return DB::transaction(function () use ($lead, $user) {
            $buyer = $lead->buyer ?? Buyer::create([
                'name' => $lead->company ?: $lead->name,
                'phone' => $lead->phone,
                'email' => $lead->email,
                'country' => $lead->country ?: 'Indonesia',
                'notes' => trim("Dari form kontak website ({$lead->created_at?->format('d/m/Y')}). Kontak: {$lead->name}"),
            ]);

            $lead->update([
                'buyer_id' => $buyer->id,
                'status' => LeadStatus::Contacted,
                'handled_by' => $user->id,
            ]);

            return $buyer;
        });
    }

    /** Tindak lanjut admin: status, data kontak tambahan, catatan internal. */
    public function updateFollowUp(ContactMessage $lead, array $data, User $user): void
    {
        $lead->update($data + ['handled_by' => $user->id, 'is_read' => true]);
    }

    public function markAsSpam(ContactMessage $lead, User $user): void
    {
        $lead->update(['status' => LeadStatus::Spam, 'handled_by' => $user->id]);
    }
}
