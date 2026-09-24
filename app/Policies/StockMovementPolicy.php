<?php

namespace App\Policies;

use App\Models\User;

// Kartu stok read-only: semua boleh lihat, tidak ada yang boleh mengubah.
class StockMovementPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, $record): bool
    {
        return true;
    }
}
