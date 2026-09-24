<?php

namespace App\Policies;

use App\Models\User;

// Data lama (sebelum v2): hanya bisa dilihat owner, read-only.
class StockTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isOwnerOrAbove();
    }

    public function view(User $user, $record): bool
    {
        return $user->isOwnerOrAbove();
    }
}
