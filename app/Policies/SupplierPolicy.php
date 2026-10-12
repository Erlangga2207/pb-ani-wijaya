<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class SupplierPolicy extends MasterDataPolicy
{
    // Petani yang sudah punya pembelian tidak dihapus (jejak transaksi), cukup dinonaktifkan.
    public function delete(User $user, $supplier): bool|Response
    {
        if (! parent::delete($user, $supplier)) {
            return false;
        }

        return $supplier->purchases()->exists()
            ? Response::deny('Petani ini sudah dipakai di transaksi pembelian, jadi tidak bisa dihapus. Nonaktifkan saja supaya tidak muncul lagi di pilihan.')
            : true;
    }
}
