<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public const ROLE_LABELS = [
        'super_admin' => 'Super Admin',
        'owner' => 'Pemilik (Owner)',
        'staff' => 'Staff',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun')
                    ->description('Data login yang digunakan untuk masuk ke sistem.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(table: User::class, column: 'email', ignoreRecord: true),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->helperText(fn (string $operation): string => $operation === 'create'
                                ? 'Minimal 8 karakter.'
                                : 'Kosongkan jika tidak ingin mengubah password.'
                            ),

                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->same('password')
                            ->dehydrated(false)
                            ->visible(fn (string $operation): bool => $operation === 'create'),
                    ]),

                Section::make('Role & Status')
                    ->description('Tentukan peran pengguna di sistem admin.')
                    ->schema([
                        Select::make('role')
                            ->label('Role')
                            ->options(fn () => array_intersect_key(
                                self::ROLE_LABELS,
                                array_flip(auth()->user()->assignableRoles())
                            ))
                            ->required()
                            ->native(false)
                            ->helperText('Owner = kelola semua data & laporan keuangan | Staff = input transaksi & lihat stok'),

                        Toggle::make('is_active')
                            ->label('Akun Aktif')
                            ->helperText('Akun nonaktif tidak bisa login.')
                            ->default(true)
                            ->disabled(fn (?User $record): bool => $record?->is(auth()->user()) ?? false),
                    ]),
            ]);
    }
}
