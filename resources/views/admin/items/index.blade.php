@extends('admin.layouts.app')

@section('judul', 'Barang')

@section('konten')
    <div class="judul-halaman">
        <h1>Barang</h1>
        @can('create', App\Models\Item::class)
            <a href="{{ route('admin.items.create') }}" class="btn btn-primary">Tambah barang</a>
        @endcan
    </div>

    <x-admin::search placeholder="Cari kode atau nama">
        <select name="type" class="form-select" style="max-width:200px" onchange="this.form.submit()" aria-label="Tipe">
            <option value="">Semua tipe</option>
            @foreach (App\Enums\ItemType::cases() as $type)
                <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->getLabel() }}</option>
            @endforeach
        </select>
    </x-admin::search>

    <x-admin::panel>
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th class="angka">Stok</th>
                    <th>Status stok</th>
                    @if ($showCost) <th class="angka">Biaya rata-rata/kg</th> @endif
                </tr>
                </thead>
                <tbody>
                @forelse ($items as $item)
                    @php
                        [$stockLabel, $stockColor] = match (true) {
                            (float) $item->stock_kg <= 0 => ['Habis', 'danger'],
                            $item->isLowStock() => ['Menipis', 'warning'],
                            default => ['Aman', 'success'],
                        };
                    @endphp
                    <tr class="baris-link">
                        <td data-label="Kode">{{ $item->code }}</td>
                        <td class="kartu-judul">
                            <a href="{{ route('admin.items.show', $item) }}" class="stretched-link text-reset text-decoration-none">{{ $item->name }}</a>
                            @unless ($item->is_active) <span class="small text-ink-2 fw-normal">(nonaktif)</span> @endunless
                        </td>
                        <td data-label="Tipe">{{ $item->type->getLabel() }}</td>
                        <td data-label="Stok" class="angka text-nowrap">
                            @angka($item->stock_kg) kg
                            <div class="small text-ink-2">≈ @angka($item->stockInSacks()) karung</div>
                        </td>
                        <td class="kartu-status"><x-admin::pill :label="$stockLabel" :warna="$stockColor" /></td>
                        @if ($showCost) <td data-label="Biaya/kg" class="angka text-nowrap">@rupiah($item->avg_cost_per_kg)</td> @endif
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-ink-2 py-4 kartu-kosong">Belum ada barang.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $items->links() }}
@endsection
