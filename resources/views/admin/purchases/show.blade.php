@extends('admin.layouts.app')

@section('judul', $purchase->number)

@section('konten')
    <div class="judul-halaman">
        <div>
            <a href="{{ route('admin.purchases.index') }}" class="small">← Pembelian gabah</a>
            <h1>{{ $purchase->number }}</h1>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if (! $purchase->isVoid() && $purchase->payment_status !== App\Enums\PaymentStatus::Paid)
                <x-admin::payment-modal :action="route('admin.purchases.payments.store', $purchase)" :remaining="$purchase->remainingAmount()" />
            @endif
            <a href="{{ route('purchases.receipt', $purchase) }}" target="_blank" rel="noopener" class="btn btn-garis">Cetak bukti</a>
            @if (! $purchase->isVoid() && auth()->user()->can('void', $purchase))
                <x-admin::void-modal :action="route('admin.purchases.void', $purchase)" judul="Batalkan pembelian {{ $purchase->number }}" />
            @endif
        </div>
    </div>

    <x-admin::void-info :document="$purchase" />

    <x-admin::panel judul="Pembelian">
        <dl class="rincian">
            <div><dt>Tanggal</dt><dd>{{ $purchase->purchase_date->translatedFormat('j F Y') }}</dd></div>
            <div><dt>Status</dt><dd><x-admin::pill :label="$purchase->status->getLabel()" :warna="$purchase->status->getColor()" /></dd></div>
            <div><dt>Petani</dt><dd>{{ $purchase->supplier->name }}</dd></div>
            <div><dt>Desa</dt><dd>{{ $purchase->supplier->village ?: '-' }}</dd></div>
            <div><dt>Barang</dt><dd>{{ $purchase->item->name }}</dd></div>
            <div><dt>Jumlah input</dt><dd>@angka($purchase->input_quantity) {{ $purchase->input_unit->getLabel() }}</dd></div>
            <div><dt>Berat</dt><dd class="fw-semibold">@angka($purchase->quantity_kg) kg</dd></div>
            <div><dt>Kadar air</dt><dd>{{ $purchase->moisture_percent !== null ? number_format((float) $purchase->moisture_percent, 2, ',', '.').' %' : '-' }}</dd></div>
            <div><dt>Harga / kg</dt><dd>@rupiah($purchase->price_per_kg)</dd></div>
            <div><dt>Dicatat oleh</dt><dd>{{ $purchase->user?->name ?? '-' }}</dd></div>
            <div><dt>Catatan</dt><dd>{{ $purchase->notes ?: '-' }}</dd></div>
        </dl>
    </x-admin::panel>

    <x-admin::panel judul="Tagihan">
        <dl class="rincian">
            <div><dt>Subtotal</dt><dd>@rupiah($purchase->subtotal)</dd></div>
            <div><dt>Ongkos lain</dt><dd>@rupiah($purchase->other_cost)</dd></div>
            <div><dt>Total</dt><dd class="fw-bold">@rupiah($purchase->total)</dd></div>
            <div><dt>Status bayar</dt><dd><x-admin::pill :label="$purchase->payment_status->getLabel()" :warna="$purchase->payment_status->getColor()" /></dd></div>
            <div><dt>Dibayar</dt><dd>@rupiah($purchase->paid_amount)</dd></div>
            <div><dt>Sisa utang</dt><dd class="fw-bold">@rupiah($purchase->remainingAmount())</dd></div>
            <div>
                <dt>Jatuh tempo</dt>
                <dd @class(['teks-merah' => $purchase->isOverdue()])>{{ $purchase->due_date?->translatedFormat('j M Y') ?? '-' }}</dd>
            </div>
        </dl>
    </x-admin::panel>

    <x-admin::payments-table :payments="$purchase->payments" />
@endsection
