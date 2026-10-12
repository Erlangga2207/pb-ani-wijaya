@extends('admin.layouts.app')

@section('judul', 'Penjualan')

@section('konten')
    <div class="judul-halaman">
        <h1>Penjualan</h1>
        <a href="{{ route('admin.sales.create') }}" class="btn btn-primary">Catat penjualan</a>
    </div>

    <x-admin::search placeholder="Cari nomor nota">
        <x-admin::date-range />
        <select name="buyer" class="form-select" style="max-width:200px" onchange="this.form.submit()" aria-label="Pembeli">
            <option value="">Semua pembeli</option>
            @foreach ($buyers as $id => $name)
                <option value="{{ $id }}" @selected((string) request('buyer') === (string) $id)>{{ $name }}</option>
            @endforeach
        </select>
        <select name="payment_status" class="form-select" style="max-width:170px" onchange="this.form.submit()" aria-label="Status bayar">
            <option value="">Semua status bayar</option>
            @foreach (App\Enums\PaymentStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('payment_status') === $status->value)>{{ $status->getLabel() }}</option>
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
        <x-slot:aksi>
            <span class="small text-ink-2">Total tercatat: <strong class="angka text-body">@rupiah($total)</strong></span>
        </x-slot:aksi>
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>Pembeli</th>
                    <th class="angka">Berat</th>
                    <th class="angka">Total</th>
                    <th>Status bayar</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($sales as $sale)
                    <tr class="baris-link">
                        <td class="kartu-judul">
                            <a href="{{ route('admin.sales.show', $sale) }}" class="stretched-link text-reset text-decoration-none">{{ $sale->number }}</a>
                        </td>
                        <td data-label="Tanggal" class="text-nowrap">{{ $sale->sale_date->translatedFormat('j M Y') }}</td>
                        <td data-label="Pembeli">
                            {{ $sale->buyer->name }}
                            @if ($sale->buyer->city) <div class="small text-ink-2">{{ $sale->buyer->city }}</div> @endif
                        </td>
                        <td data-label="Berat" class="angka text-nowrap">@angka($sale->items_sum_quantity_kg) kg</td>
                        <td data-label="Total" class="angka text-nowrap">@rupiah($sale->total)</td>
                        <td data-label="Bayar"><x-admin::pill :label="$sale->payment_status->getLabel()" :warna="$sale->payment_status->getColor()" /></td>
                        <td class="kartu-status"><x-admin::pill :label="$sale->status->getLabel()" :warna="$sale->status->getColor()" /></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-ink-2 py-4 kartu-kosong">Belum ada penjualan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $sales->links() }}
@endsection
