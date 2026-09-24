<?php

namespace App\Policies;

use App\Models\User;

// Menu yang hanya untuk owner ke atas (CMS website, data lama, dsb).
class OwnerOnlyPolicy
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

    public function update(User $user, $record): bool
    {
        return $user->isOwnerOrAbove();
    }

    public function delete(User $user, $record): bool
    {
        return $user->isOwnerOrAbove();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isOwnerOrAbove();
    }

    public function reorder(User $user): bool
    {
        return $user->isOwnerOrAbove();
    }
}
