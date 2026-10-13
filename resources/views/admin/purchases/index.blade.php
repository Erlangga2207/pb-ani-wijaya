@extends('admin.layouts.app')

@section('judul', 'Pembelian Gabah')

@section('konten')
    <div class="judul-halaman">
        <h1>Pembelian Gabah</h1>
        <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary">Catat pembelian</a>
    </div>

    <x-admin::search placeholder="Cari nomor">
        <x-admin::date-range />
        <select name="supplier" class="form-select" style="max-width:200px" onchange="this.form.submit()" aria-label="Petani">
            <option value="">Semua petani</option>
            @foreach ($suppliers as $id => $name)
                <option value="{{ $id }}" @selected((string) request('supplier') === (string) $id)>{{ $name }}</option>
            @endforeach
        </select>
        <select name="village" class="form-select" style="max-width:180px" onchange="this.form.submit()" aria-label="Desa">
            <option value="">Semua desa</option>
            @foreach ($villages as $village)
                <option value="{{ $village }}" @selected(request('village') === $village)>{{ $village }}</option>
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
            <span class="small text-ink-2">Total tercatat: <strong class="angka text-body">@angka($totals->kg) kg · @rupiah($totals->total)</strong></span>
        </x-slot:aksi>
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>Petani</th>
                    <th class="angka">Berat</th>
                    <th class="angka">Harga/kg</th>
                    <th class="angka">Total</th>
                    <th>Status bayar</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($purchases as $purchase)
                    <tr class="baris-link">
                        <td class="kartu-judul">
                            <a href="{{ route('admin.purchases.show', $purchase) }}" class="stretched-link text-reset text-decoration-none">{{ $purchase->number }}</a>
                        </td>
                        <td data-label="Tanggal" class="text-nowrap">{{ $purchase->purchase_date->translatedFormat('j M Y') }}</td>
                        <td data-label="Petani">
                            {{ $purchase->supplier->name }}
                            @if ($purchase->supplier->village) <div class="small text-ink-2">{{ $purchase->supplier->village }}</div> @endif
                        </td>
                        <td data-label="Berat" class="angka text-nowrap">@angka($purchase->quantity_kg) kg</td>
                        <td data-label="Harga/kg" class="angka text-nowrap">@rupiah($purchase->price_per_kg)</td>
                        <td data-label="Total" class="angka text-nowrap">@rupiah($purchase->total)</td>
                        <td data-label="Bayar"><x-admin::pill :label="$purchase->payment_status->getLabel()" :warna="$purchase->payment_status->getColor()" /></td>
                        <td class="kartu-status"><x-admin::pill :label="$purchase->status->getLabel()" :warna="$purchase->status->getColor()" /></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-ink-2 py-4 kartu-kosong">Belum ada pembelian.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $purchases->links() }}
@endsection
