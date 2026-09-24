@extends('print.layout')

@section('title', 'Bukti Pembelian '.$purchase->number)

@section('watermark')
    @if ($purchase->isVoid())
        <div class="void-mark">DIBATALKAN</div>
    @endif
@endsection

@section('doc')
    <h2>BUKTI PEMBELIAN</h2>
    <p><strong>{{ $purchase->number }}</strong></p>
    <p>{{ $purchase->purchase_date->translatedFormat('d F Y') }}</p>
@endsection

@section('content')
    <table class="meta">
        <tr><td>Petani</td><td><strong>{{ $purchase->supplier->name }}</strong></td></tr>
        <tr><td>Desa</td><td>{{ collect([$purchase->supplier->village, $purchase->supplier->district])->filter()->join(', ') ?: '-' }}</td></tr>
        <tr><td>No. HP</td><td>{{ $purchase->supplier->phone ?: '-' }}</td></tr>
    </table>

    <table class="lines" style="margin-top: 12px">
        <thead>
            <tr><th>Barang</th><th class="r">Jumlah</th><th class="r">Berat</th><th class="r">Harga/kg</th><th class="r">Subtotal</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $purchase->item->name }}@if ($purchase->moisture_percent !== null)<br><span class="muted">Kadar air {{ $purchase->moisture_percent }}%</span>@endif</td>
                <td class="r">{{ number_format($purchase->input_quantity, 2, ',', '.') }} {{ $purchase->input_unit->getLabel() }}</td>
                <td class="r">{{ number_format($purchase->quantity_kg, 2, ',', '.') }} kg</td>
                <td class="r">Rp {{ number_format($purchase->price_per_kg, 0, ',', '.') }}</td>
                <td class="r">Rp {{ number_format($purchase->subtotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td class="r">Rp {{ number_format($purchase->subtotal, 0, ',', '.') }}</td></tr>
        @if ((float) $purchase->other_cost > 0)
            <tr><td>Ongkos lain</td><td class="r">Rp {{ number_format($purchase->other_cost, 0, ',', '.') }}</td></tr>
        @endif
        <tr class="grand"><td>Total</td><td class="r">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td></tr>
        <tr><td>Dibayar</td><td class="r">Rp {{ number_format($purchase->paid_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Sisa</td><td class="r">Rp {{ number_format($purchase->remainingAmount(), 0, ',', '.') }}</td></tr>
        <tr><td>Status</td><td class="r">{{ $purchase->payment_status->getLabel() }}</td></tr>
        @if ($purchase->due_date)
            <tr><td>Jatuh tempo</td><td class="r">{{ $purchase->due_date->translatedFormat('d F Y') }}</td></tr>
        @endif
    </table>

    <div class="sign">
        <div>Petani<div class="line">{{ $purchase->supplier->name }}</div></div>
        <div>Penerima<div class="line">{{ $purchase->user?->name }}</div></div>
    </div>
@endsection
