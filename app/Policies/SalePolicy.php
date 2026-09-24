<?php

namespace App\Policies;

use App\Models\User;

class SalePolicy extends TransactionPolicy
{
    // Nota hanya bisa dibuka oleh pembuatnya dan owner ke atas.
    public function view(User $user, $sale): bool
    {
        return $user->isOwnerOrAbove() || $sale->user_id === $user->id;
    }
}
