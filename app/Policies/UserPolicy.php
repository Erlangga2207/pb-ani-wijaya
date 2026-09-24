<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isOwnerOrAbove();
    }

    public function view(User $user, User $target): bool
    {
        return $user->isOwnerOrAbove();
    }

    public function create(User $user): bool
    {
        return $user->isOwnerOrAbove();
    }

    // Owner tidak boleh menyentuh akun super_admin.
    public function update(User $user, User $target): bool
    {
        return $user->isOwnerOrAbove()
            && ($user->isSuperAdmin() || ! $target->isSuperAdmin());
    }

    public function delete(User $user, User $target): bool
    {
        return $this->update($user, $target)
            && $user->isNot($target)
            && ! $target->hasRecordedTransactions();
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
