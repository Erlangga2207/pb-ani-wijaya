@extends('admin.layouts.app')

@section('judul', $item->name)

@section('konten')
    <div class="judul-halaman">
        <div>
            <a href="{{ route('admin.items.index') }}" class="small">← Barang</a>
            <h1>{{ $item->name }}</h1>
        </div>
        <div class="d-flex gap-2">
            @can('update', $item)
                <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-garis">Ubah</a>
            @endcan
            @if (auth()->user()->isOwnerOrAbove())
                <form method="POST" action="{{ route('admin.items.destroy', $item) }}" onsubmit="return confirm(@js('Hapus barang '.$item->code.'?'))">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-polos">Hapus</button>
                </form>
            @endif
        </div>
    </div>

    <x-admin::panel judul="Barang">
        <dl class="rincian">
            <div><dt>Kode</dt><dd>{{ $item->code }}</dd></div>
            <div><dt>Tipe</dt><dd><x-admin::pill :label="$item->type->getLabel()" :warna="$item->type->getColor()" /></dd></div>
            <div><dt>Produk katalog</dt><dd>{{ $item->product?->name ?? '-' }}</dd></div>
            <div><dt>Berat per karung</dt><dd>@angka($item->weight_per_sack_kg) kg</dd></div>
            <div><dt>Status</dt><dd>{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</dd></div>
        </dl>
    </x-admin::panel>

    <x-admin::panel judul="Stok">
        <dl class="rincian">
            <div>
                <dt>Stok saat ini</dt>
                <dd @class(['teks-merah' => $item->isLowStock()])>@angka($item->stock_kg) kg <span class="small text-ink-2">≈ @angka($item->stockInSacks()) karung</span></dd>
            </div>
            <div><dt>Minimum</dt><dd>{{ $item->min_stock_kg !== null ? number_format((float) $item->min_stock_kg, 2, ',', '.').' kg' : '-' }}</dd></div>
            @if ($showCost)
                <div><dt>Biaya rata-rata / kg</dt><dd>@rupiah($item->avg_cost_per_kg)</dd></div>
            @endif
            <div><dt>Harga pasar terbaru / kg</dt><dd>{{ $item->default_price_per_kg !== null ? 'Rp '.number_format((float) $item->default_price_per_kg, 0, ',', '.') : '-' }}</dd></div>
        </dl>
    </x-admin::panel>

    <x-admin::panel judul="Riwayat stok">
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Dokumen</th>
                    <th>Arah</th>
                    <th class="angka">Jumlah</th>
                    <th class="angka">Saldo</th>
                    @if ($showCost) <th class="angka">Biaya/kg</th> @endif
                    <th>Keterangan</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($movements as $m)
                    <tr>
                        <td class="kartu-judul text-nowrap">{{ $m->movement_date->translatedFormat('j M Y') }}</td>
                        <td data-label="Dokumen">{{ $m->source?->number ?? class_basename($m->source_type) }}</td>
                        <td class="kartu-status">
                            <x-admin::pill :label="$m->direction === 'in' ? 'Masuk' : 'Keluar'" :warna="$m->direction === 'in' ? 'success' : 'danger'" />
                        </td>
                        <td data-label="Jumlah" class="angka text-nowrap">{{ $m->direction === 'in' ? '+' : '−' }}@angka($m->quantity_kg) kg</td>
                        <td data-label="Saldo" class="angka text-nowrap fw-semibold">@angka($m->balance_after_kg) kg</td>
                        @if ($showCost) <td data-label="Biaya/kg" class="angka text-nowrap">@rupiah($m->unit_cost)</td> @endif
                        <td data-label="Ket.">{{ $m->description ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-ink-2 py-4 kartu-kosong">Belum ada mutasi stok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $movements->links() }}
@endsection
