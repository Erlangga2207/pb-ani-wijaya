<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class BuyerPolicy extends MasterDataPolicy
{
    // Pembeli yang sudah punya penjualan tidak dihapus (jejak transaksi), cukup dinonaktifkan.
    public function delete(User $user, $buyer): bool|Response
    {
        if (! parent::delete($user, $buyer)) {
            return false;
        }

        return $buyer->sales()->exists()
            ? Response::deny('Pembeli ini sudah dipakai di transaksi penjualan, jadi tidak bisa dihapus. Nonaktifkan saja supaya tidak muncul lagi di pilihan.')
            : true;
    }
}
