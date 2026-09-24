<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    public const ROLES = ['super_admin', 'owner', 'staff'];

    public const TRANSACTION_TABLES = ['stock_transactions', 'stock_adjustments', 'purchases', 'payments', 'productions'];

    protected $attributes = ['is_active' => true];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->hasAnyRole(self::ROLES);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    // Owner ke atas boleh melihat data keuangan (HPP, laba, biaya) & melakukan void.
    public function isOwnerOrAbove(): bool
    {
        return $this->hasAnyRole(['super_admin', 'owner']);
    }

    // Role yang boleh diberikan user ini ke user lain.
    public function assignableRoles(): array
    {
        return $this->isSuperAdmin() ? self::ROLES : ['owner', 'staff'];
    }

    // User yang pernah mencatat transaksi tidak dihapus, cukup dinonaktifkan (jejak audit).
    public function hasRecordedTransactions(): bool
    {
        foreach (self::TRANSACTION_TABLES as $table) {
            if (DB::table($table)->where('user_id', $this->id)->exists()) {
                return true;
            }
        }

        return false;
    }
}
