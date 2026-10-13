@extends('admin.layouts.app')

@section('judul', 'Kartu Stok')

@section('konten')
    <div class="judul-halaman">
        <h1>Kartu Stok</h1>
    </div>

    <x-admin::search placeholder="Cari keterangan">
        <select name="item" class="form-select" style="max-width:200px" onchange="this.form.submit()" aria-label="Barang">
            <option value="">Semua barang</option>
            @foreach ($items as $id => $name)
                <option value="{{ $id }}" @selected((string) request('item') === (string) $id)>{{ $name }}</option>
            @endforeach
        </select>
        <select name="direction" class="form-select" style="max-width:150px" onchange="this.form.submit()" aria-label="Arah">
            <option value="">Masuk & keluar</option>
            <option value="in" @selected(request('direction') === 'in')>Masuk</option>
            <option value="out" @selected(request('direction') === 'out')>Keluar</option>
        </select>
        <x-admin::date-range />
    </x-admin::search>

    <x-admin::panel>
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Barang</th>
                    <th>Arah</th>
                    <th class="angka">Jumlah</th>
                    <th class="angka">Saldo</th>
                    @if ($showCost) <th class="angka">Biaya/kg</th> @endif
                    <th>Dokumen</th>
                    <th>Keterangan</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($movements as $m)
                    <tr>
                        <td class="kartu-judul text-nowrap">{{ $m->movement_date->translatedFormat('j M Y') }}</td>
                        <td data-label="Barang">{{ $m->item->name }}</td>
                        <td class="kartu-status">
                            <x-admin::pill :label="$m->direction === 'in' ? 'Masuk' : 'Keluar'" :warna="$m->direction === 'in' ? 'success' : 'danger'" />
                        </td>
                        <td data-label="Jumlah" class="angka text-nowrap">{{ $m->direction === 'in' ? '+' : '−' }}@angka($m->quantity_kg) kg</td>
                        <td data-label="Saldo" class="angka text-nowrap fw-semibold">@angka($m->balance_after_kg) kg</td>
                        @if ($showCost) <td data-label="Biaya/kg" class="angka text-nowrap">Rp {{ number_format((float) $m->unit_cost, 2, ',', '.') }}</td> @endif
                        <td data-label="Dokumen"><x-admin::doc-link :movement="$m" /></td>
                        <td data-label="Ket.">
                            {{ $m->description ?? '-' }}
                            @if ($m->user) <div class="small text-ink-2">oleh {{ $m->user->name }}</div> @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-ink-2 py-4 kartu-kosong">Belum ada mutasi stok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $movements->links() }}
@endsection
