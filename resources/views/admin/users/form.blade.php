@extends('admin.layouts.app')

@section('judul', $user->exists ? 'Ubah pengguna' : 'Tambah pengguna')

@section('konten')
    <div class="judul-halaman">
        <h1>{{ $user->exists ? 'Ubah pengguna' : 'Tambah pengguna' }}</h1>
    </div>

    <form method="POST" style="max-width:560px"
          action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        <x-admin::panel judul="Informasi akun">
            <x-admin::field label="Nama lengkap" name="name" :value="$user->name" maxlength="255" wajib />
            <x-admin::field label="Alamat email" name="email" type="email" :value="$user->email" maxlength="255" autocomplete="off" wajib />
            <x-admin::field label="Password" name="password" type="password" minlength="8" autocomplete="new-password" :wajib="! $user->exists"
                            :hint="$user->exists ? 'Kosongkan jika tidak ingin mengubah password.' : 'Minimal 8 karakter.'" />
            <x-admin::field label="Konfirmasi password" name="password_confirmation" type="password" autocomplete="new-password" :wajib="! $user->exists" />
        </x-admin::panel>

        <x-admin::panel judul="Role & status">
            <x-admin::field label="Role" name="role" hint="Owner = kelola semua data & laporan keuangan. Staf = input transaksi & lihat stok." wajib>
                <select id="role" name="role" @class(['form-select', 'is-invalid' => $errors->has('role')])>
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}" @selected(old('role', $user->roles->first()?->name ?? 'staff') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </x-admin::field>
            @if ($isSelf)
                <input type="hidden" name="is_active" value="1">
                <p class="small text-ink-2 mb-0">Akun Anda sendiri tidak bisa dinonaktifkan.</p>
            @else
                <x-admin::active-check :checked="$user->is_active" label="Akun aktif" hint="Akun nonaktif tidak bisa login." />
            @endif
        </x-admin::panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection
