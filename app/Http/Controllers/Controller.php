<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

abstract class Controller
{
    // Untuk aksi hapus: tidak berhak → 403; ditolak karena data sudah dipakai → kembalikan pesannya.
    protected function deletionBlockedReason(Model $record): ?string
    {
        $check = Gate::inspect('delete', $record);

        abort_if($check->denied() && ! $check->message(), 403);

        return $check->denied() ? $check->message() : null;
    }
}
