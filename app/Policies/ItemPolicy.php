<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Item $item): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isOwnerOrAbove();
    }

    public function update(User $user, Item $item): bool
    {
        return $user->isOwnerOrAbove();
    }

    // Barang yang sudah punya mutasi tidak bisa dihapus (jejak buku besar).
    public function delete(User $user, Item $item): bool
    {
        return $user->isOwnerOrAbove() && ! $item->stockMovements()->exists();
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
