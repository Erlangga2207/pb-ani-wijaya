<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;
use Illuminate\Auth\Access\Response;

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
    public function delete(User $user, Item $item): bool|Response
    {
        if (! $user->isOwnerOrAbove()) {
            return false;
        }

        return $item->stockMovements()->exists()
            ? Response::deny('Barang ini sudah punya riwayat stok, jadi tidak bisa dihapus. Nonaktifkan saja supaya tidak muncul lagi di pilihan.')
            : true;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
