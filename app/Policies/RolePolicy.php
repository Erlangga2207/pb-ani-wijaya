<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

// Hanya super_admin yang boleh mengubah role/permission.
// Kalau owner bisa, semua pembatasan untuk staff tidak berarti.
class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Role $role): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Role $role): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->isSuperAdmin() && $role->name !== 'super_admin';
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
