<?php

namespace App\Policies;

use App\Models\User;

// Biaya operasional adalah data keuangan: hanya owner ke atas.
class ExpensePolicy extends TransactionPolicy
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
