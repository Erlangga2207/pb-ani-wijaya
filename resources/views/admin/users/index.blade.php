@extends('admin.layouts.app')

@section('judul', 'Pengguna')

@section('konten')
    <div class="judul-halaman">
        <h1>Pengguna</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Tambah pengguna</a>
    </div>

    <x-admin::search placeholder="Cari nama atau email">
        <x-admin::status-filter />
    </x-admin::search>

    <x-admin::panel>
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @php $roleColors = ['super_admin' => 'danger', 'owner' => 'warning', 'staff' => 'info']; @endphp
                @forelse ($users as $user)
                    @php $role = $user->roles->first()?->name; @endphp
                    <tr>
                        <td class="kartu-judul">{{ $user->name }}</td>
                        <td data-label="Email">{{ $user->email }}</td>
                        <td data-label="Role"><x-admin::pill :label="$roleLabels[$role] ?? '-'" :warna="$roleColors[$role] ?? 'gray'" /></td>
                        <td class="kartu-status"><x-admin::pill :label="$user->is_active ? 'Aktif' : 'Nonaktif'" :warna="$user->is_active ? 'success' : 'gray'" /></td>
                        <td class="text-end text-nowrap kartu-aksi">
                            @can('update', $user)
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-garis btn-sm">Ubah</a>
                                @unless ($user->is(auth()->user()))
                                    <x-admin::delete-button :action="route('admin.users.destroy', $user)" :confirm="'Hapus pengguna '.$user->name.'?'" />
                                @endunless
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-ink-2 py-4 kartu-kosong">Belum ada pengguna.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $users->links() }}
@endsection
