<?php

namespace App\Policies;

use App\Models\User;

// Master data yang juga diisi staff saat input transaksi (petani/pembeli baru).
// Menghapus hanya owner ke atas.
class MasterDataPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, $record): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, $record): bool
    {
        return true;
    }

    public function delete(User $user, $record): bool
    {
        return $user->isOwnerOrAbove();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isOwnerOrAbove();
    }
}
