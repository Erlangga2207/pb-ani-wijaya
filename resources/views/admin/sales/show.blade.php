@extends('admin.layouts.app')

@section('judul', $sale->number)

@section('konten')
    <div class="judul-halaman">
        <div>
            <a href="{{ route('admin.sales.index') }}" class="small">← Penjualan</a>
            <h1>{{ $sale->number }}</h1>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('sales.invoice', $sale) }}" target="_blank" rel="noopener" class="btn btn-garis">Cetak nota</a>
            @if (! $sale->isVoid() && $sale->payment_status !== App\Enums\PaymentStatus::Paid)
                <x-admin::payment-modal :action="route('admin.sales.payments.store', $sale)" :remaining="$sale->remainingAmount()" />
            @endif
            @if (! $sale->isVoid() && auth()->user()->can('void', $sale))
                <x-admin::void-modal :action="route('admin.sales.void', $sale)" judul="Batalkan penjualan {{ $sale->number }}" />
            @endif
        </div>
    </div>

    <x-admin::void-info :document="$sale" />

    <x-admin::panel judul="Penjualan">
        <dl class="rincian">
            <div><dt>Tanggal</dt><dd>{{ $sale->sale_date->translatedFormat('j F Y') }}</dd></div>
            <div><dt>Status</dt><dd><x-admin::pill :label="$sale->status->getLabel()" :warna="$sale->status->getColor()" /></dd></div>
            <div><dt>Pembeli</dt><dd>{{ $sale->buyer->name }}</dd></div>
            <div><dt>Kota</dt><dd>{{ $sale->buyer->city ?: '-' }}</dd></div>
            <div><dt>HP</dt><dd>{{ $sale->buyer->phone ?: '-' }}</dd></div>
            <div><dt>Dicatat oleh</dt><dd>{{ $sale->user?->name ?? '-' }}</dd></div>
            <div><dt>Catatan</dt><dd>{{ $sale->notes ?: '-' }}</dd></div>
        </dl>
    </x-admin::panel>

    <x-admin::panel judul="Barang">
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th class="angka">Berat</th>
                    <th class="angka">Harga / kg</th>
                    <th class="angka">Total</th>
                    @if ($showCost) <th class="angka">HPP / kg</th> @endif
                </tr>
                </thead>
                <tbody>
                @foreach ($sale->items as $line)
                    <tr>
                        <td class="kartu-judul">{{ $line->item->name }}</td>
                        <td data-label="Jumlah">@angka($line->input_quantity) {{ $line->input_unit->getLabel() }}</td>
                        <td data-label="Berat" class="angka text-nowrap">@angka($line->quantity_kg) kg</td>
                        <td data-label="Harga/kg" class="angka text-nowrap">@rupiah($line->price_per_kg)</td>
                        <td data-label="Total" class="angka text-nowrap fw-semibold">@rupiah($line->line_total)</td>
                        @if ($showCost) <td data-label="HPP/kg" class="angka text-nowrap">Rp {{ number_format((float) $line->cogs_per_kg, 2, ',', '.') }}</td> @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    <x-admin::panel judul="Tagihan">
        <dl class="rincian">
            <div><dt>Subtotal</dt><dd>@rupiah($sale->subtotal)</dd></div>
            <div><dt>Diskon</dt><dd>@rupiah($sale->discount)</dd></div>
            <div><dt>Ongkir</dt><dd>@rupiah($sale->shipping_cost)</dd></div>
            <div><dt>Total</dt><dd class="fw-bold">@rupiah($sale->total)</dd></div>
            <div><dt>Status bayar</dt><dd><x-admin::pill :label="$sale->payment_status->getLabel()" :warna="$sale->payment_status->getColor()" /></dd></div>
            <div><dt>Dibayar</dt><dd>@rupiah($sale->paid_amount)</dd></div>
            <div><dt>Sisa piutang</dt><dd class="fw-bold">@rupiah($sale->remainingAmount())</dd></div>
            <div><dt>Jatuh tempo</dt><dd @class(['teks-merah' => $sale->isOverdue()])>{{ $sale->due_date?->translatedFormat('j M Y') ?? '-' }}</dd></div>
            @if ($showCost)
                <div><dt>HPP</dt><dd>Rp {{ number_format((float) $sale->cogs_total, 2, ',', '.') }}</dd></div>
                <div><dt>Laba kotor</dt><dd>Rp {{ number_format($sale->grossProfit(), 2, ',', '.') }}</dd></div>
            @endif
        </dl>
    </x-admin::panel>

    <x-admin::payments-table :payments="$sale->payments" />
@endsection
