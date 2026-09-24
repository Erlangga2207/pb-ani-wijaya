<?php

namespace App\Policies;

use App\Models\User;

// Dokumen transaksi: semua role boleh input & lihat; tidak ada edit/hapus; void hanya owner ke atas.
class TransactionPolicy
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
        return false;
    }

    public function delete(User $user, $record): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function void(User $user, $record): bool
    {
        return $user->isOwnerOrAbove();
    }
}
