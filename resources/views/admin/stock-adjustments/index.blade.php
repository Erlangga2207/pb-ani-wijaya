@extends('admin.layouts.app')

@section('judul', 'Stock Opname')

@section('konten')
    <div class="judul-halaman">
        <h1>Stock Opname</h1>
        <a href="{{ route('admin.stock-adjustments.create') }}" class="btn btn-primary">Catat opname</a>
    </div>

    <x-admin::search placeholder="Cari nomor">
        <select name="item" class="form-select" style="max-width:200px" onchange="this.form.submit()" aria-label="Barang">
            <option value="">Semua barang</option>
            @foreach ($items as $id => $name)
                <option value="{{ $id }}" @selected((string) request('item') === (string) $id)>{{ $name }}</option>
            @endforeach
        </select>
        <select name="status" class="form-select" style="max-width:160px" onchange="this.form.submit()" aria-label="Status dokumen">
            <option value="">Semua dokumen</option>
            @foreach (App\Enums\DocumentStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->getLabel() }}</option>
            @endforeach
        </select>
    </x-admin::search>

    <x-admin::panel>
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>Barang</th>
                    <th class="angka">Sistem</th>
                    <th class="angka">Fisik</th>
                    <th class="angka">Selisih</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($adjustments as $adjustment)
                    <tr class="baris-link">
                        <td class="kartu-judul">
                            <a href="{{ route('admin.stock-adjustments.show', $adjustment) }}" class="stretched-link text-reset text-decoration-none">{{ $adjustment->number }}</a>
                        </td>
                        <td data-label="Tanggal" class="text-nowrap">{{ $adjustment->adjustment_date->translatedFormat('j M Y') }}</td>
                        <td data-label="Barang">{{ $adjustment->item->name }}</td>
                        <td data-label="Sistem" class="angka text-nowrap">@angka($adjustment->system_kg) kg</td>
                        <td data-label="Fisik" class="angka text-nowrap">@angka($adjustment->physical_kg) kg</td>
                        <td data-label="Selisih" @class(['angka text-nowrap fw-semibold', 'teks-merah' => (float) $adjustment->difference_kg < 0])>
                            {{ (float) $adjustment->difference_kg > 0 ? '+' : '' }}@angka($adjustment->difference_kg) kg
                        </td>
                        <td class="kartu-status"><x-admin::pill :label="$adjustment->status->getLabel()" :warna="$adjustment->status->getColor()" /></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-ink-2 py-4 kartu-kosong">Belum ada stock opname.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $adjustments->links() }}
@endsection
