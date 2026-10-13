@extends('admin.layouts.app')

@section('judul', 'Petani / Pemasok')

@section('konten')
    <div class="judul-halaman">
        <h1>Petani / Pemasok</h1>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">Tambah petani</a>
    </div>

    <x-admin::search placeholder="Cari nama atau no. HP">
        <select name="village" class="form-select" style="max-width:200px" onchange="this.form.submit()" aria-label="Desa">
            <option value="">Semua desa</option>
            @foreach ($villages as $village)
                <option value="{{ $village }}" @selected(request('village') === $village)>{{ $village }}</option>
            @endforeach
        </select>
        <x-admin::status-filter />
    </x-admin::search>

    <x-admin::panel>
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th>Nama</th>
                    <th>No. HP</th>
                    <th>Desa</th>
                    <th>Kecamatan</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($suppliers as $supplier)
                    <tr class="baris-link">
                        <td class="kartu-judul">
                            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="stretched-link text-reset text-decoration-none">{{ $supplier->name }}</a>
                        </td>
                        <td data-label="HP">{{ $supplier->phone ?: '-' }}</td>
                        <td data-label="Desa">{{ $supplier->village ?: '-' }}</td>
                        <td data-label="Kec.">{{ $supplier->district ?: '-' }}</td>
                        <td class="kartu-status"><x-admin::pill :label="$supplier->is_active ? 'Aktif' : 'Nonaktif'" :warna="$supplier->is_active ? 'success' : 'gray'" /></td>
                        <td class="text-end text-nowrap kartu-aksi">
                            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-garis btn-sm">Ubah</a>
                            @if (auth()->user()->isOwnerOrAbove())
                                <x-admin::delete-button :action="route('admin.suppliers.destroy', $supplier)" :confirm="'Hapus petani '.$supplier->name.'?'" />
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-ink-2 py-4 kartu-kosong">Belum ada petani.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $suppliers->links() }}
@endsection
