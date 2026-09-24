<?php

namespace App\Policies;

use App\Models\User;

// Stock opname hanya owner ke atas (membuat, melihat, void).
class StockAdjustmentPolicy extends TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isOwnerOrAbove();
    }

    public function view(User $user, $record): bool
    {
        return $user->isOwnerOrAbove();
    }

    public function create(User $user): bool
    {
        return $user->isOwnerOrAbove();
    }
}
