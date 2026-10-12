<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

// Membuat & mengubah akun panel. Satu user = satu role.
class UserService
{
    public function create(array $data, User $actor): User
    {
        $this->ensureCanAssign($data['role'], $actor);

        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'is_active' => $data['is_active'] ?? true,
            ]);
            $user->syncRoles([$data['role']]);

            return $user;
        });
    }

    public function update(User $user, array $data, User $actor): User
    {
        $this->ensureCanAssign($data['role'], $actor);

        $attributes = [
            'name' => $data['name'],
            'email' => $data['email'],
            // Tidak boleh menonaktifkan akun sendiri (bisa terkunci dari panel).
            'is_active' => $user->is($actor) ? true : ($data['is_active'] ?? $user->is_active),
        ];
        if (filled($data['password'] ?? null)) {
            $attributes['password'] = $data['password'];
        }

        return DB::transaction(function () use ($user, $attributes, $data) {
            $user->update($attributes);
            $user->syncRoles([$data['role']]);

            return $user;
        });
    }

    private function ensureCanAssign(string $role, User $actor): void
    {
        if (! in_array($role, $actor->assignableRoles(), true)) {
            throw new AuthorizationException('Anda tidak boleh memberikan peran ini.');
        }
    }
}
