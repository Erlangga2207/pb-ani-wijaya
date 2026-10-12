@extends('admin.layouts.app')

@section('judul', 'Pembeli')

@section('konten')
    <div class="judul-halaman">
        <h1>Pembeli</h1>
        <a href="{{ route('admin.buyers.create') }}" class="btn btn-primary">Tambah pembeli</a>
    </div>

    <x-admin::search placeholder="Cari nama, no. HP, kota">
        <select name="type" class="form-select" style="max-width:180px" onchange="this.form.submit()" aria-label="Tipe">
            <option value="">Semua tipe</option>
            @foreach (App\Enums\BuyerType::cases() as $type)
                <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->getLabel() }}</option>
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
                    <th>Tipe</th>
                    <th>Kota</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($buyers as $buyer)
                    <tr class="baris-link">
                        <td class="kartu-judul">
                            <a href="{{ route('admin.buyers.edit', $buyer) }}" class="stretched-link text-reset text-decoration-none">{{ $buyer->name }}</a>
                        </td>
                        <td data-label="HP">{{ $buyer->phone ?: '-' }}</td>
                        <td data-label="Tipe"><x-admin::pill :label="$buyer->type->getLabel()" :warna="$buyer->type->getColor()" /></td>
                        <td data-label="Kota">{{ $buyer->city ?: '-' }}</td>
                        <td class="kartu-status"><x-admin::pill :label="$buyer->is_active ? 'Aktif' : 'Nonaktif'" :warna="$buyer->is_active ? 'success' : 'gray'" /></td>
                        <td class="text-end text-nowrap kartu-aksi">
                            <a href="{{ route('admin.buyers.edit', $buyer) }}" class="btn btn-garis btn-sm">Ubah</a>
                            @if (auth()->user()->isOwnerOrAbove())
                                <x-admin::delete-button :action="route('admin.buyers.destroy', $buyer)" :confirm="'Hapus pembeli '.$buyer->name.'?'" />
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-ink-2 py-4 kartu-kosong">Belum ada pembeli.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $buyers->links() }}
@endsection
