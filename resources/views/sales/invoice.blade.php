@extends('print.layout')

@section('title', 'Nota '.$sale->number)

@php
    $rp = fn ($v) => 'Rp '.number_format((float) $v, 0, ',', '.');
    $waText = collect([
        $setting('company_name', 'PB. Ani Wijaya').' - Nota '.$sale->number,
        'Tanggal: '.$sale->sale_date->translatedFormat('d F Y'),
        ...$sale->items->map(fn ($line) => '- '.$line->item->name.' '.number_format((float) $line->quantity_kg, 2, ',', '.').' kg = '.$rp($line->line_total)),
        'Total: '.$rp($sale->total),
        'Dibayar: '.$rp($sale->paid_amount),
        'Sisa: '.$rp($sale->remainingAmount()),
        'Terima kasih.',
    ])->join("\n");
    $waUrl = $sale->whatsappUrl($waText);
@endphp

@section('toolbar')
    @if ($waUrl)
        <a class="btn green" href="{{ $waUrl }}" target="_blank" rel="noopener">Kirim via WhatsApp</a>
    @endif
@endsection

@section('watermark')
    @if ($sale->isVoid())
        <div class="void-mark">DIBATALKAN</div>
    @endif
@endsection

@section('doc')
    <h2>NOTA PENJUALAN</h2>
    <p><strong>{{ $sale->number }}</strong></p>
    <p>{{ $sale->sale_date->translatedFormat('d F Y') }}</p>
@endsection

@section('content')
    <table class="meta">
        <tr><td>Kepada</td><td><strong>{{ $sale->buyer->name }}</strong></td></tr>
        <tr><td>Alamat</td><td>{{ collect([$sale->buyer->address, $sale->buyer->city])->filter()->join(', ') ?: '-' }}</td></tr>
        <tr><td>No. HP</td><td>{{ $sale->buyer->phone ?: '-' }}</td></tr>
    </table>

    <table class="lines" style="margin-top: 12px">
        <thead>
            <tr><th>Barang</th><th class="r">Jumlah</th><th class="r">Berat</th><th class="r">Harga/kg</th><th class="r">Total</th></tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $line)
                <tr>
                    <td>{{ $line->item->name }}</td>
                    <td class="r">{{ number_format((float) $line->input_quantity, 2, ',', '.') }} {{ $line->input_unit->getLabel() }}</td>
                    <td class="r">{{ number_format((float) $line->quantity_kg, 2, ',', '.') }} kg</td>
                    <td class="r">{{ $rp($line->price_per_kg) }}</td>
                    <td class="r">{{ $rp($line->line_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td class="r">{{ $rp($sale->subtotal) }}</td></tr>
        @if ((float) $sale->discount > 0)
            <tr><td>Diskon</td><td class="r">- {{ $rp($sale->discount) }}</td></tr>
        @endif
        @if ((float) $sale->shipping_cost > 0)
            <tr><td>Ongkir</td><td class="r">{{ $rp($sale->shipping_cost) }}</td></tr>
        @endif
        <tr class="grand"><td>Total</td><td class="r">{{ $rp($sale->total) }}</td></tr>
        <tr><td>Dibayar</td><td class="r">{{ $rp($sale->paid_amount) }}</td></tr>
        <tr><td>Sisa</td><td class="r">{{ $rp($sale->remainingAmount()) }}</td></tr>
        <tr><td>Status</td><td class="r">{{ $sale->payment_status->getLabel() }}</td></tr>
        @if ($sale->due_date)
            <tr><td>Jatuh tempo</td><td class="r">{{ $sale->due_date->translatedFormat('d F Y') }}</td></tr>
        @endif
    </table>

    <div class="sign">
        <div>Penerima<div class="line">{{ $sale->buyer->name }}</div></div>
        <div>Hormat kami<div class="line">{{ $sale->user?->name }}</div></div>
    </div>
@endsection
