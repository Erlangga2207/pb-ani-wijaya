@extends('admin.layouts.app')

@section('judul', $production->number)

@section('konten')
    <div class="judul-halaman">
        <div>
            <a href="{{ route('admin.productions.index') }}" class="small">← Produksi</a>
            <h1>{{ $production->number }}</h1>
        </div>
        @if (! $production->isVoid() && auth()->user()->can('void', $production))
            <x-admin::void-modal :action="route('admin.productions.void', $production)" judul="Batalkan produksi {{ $production->number }}" />
        @endif
    </div>

    <x-admin::void-info :document="$production" />

    <x-admin::panel judul="Produksi">
        <dl class="rincian">
            <div><dt>Tanggal</dt><dd>{{ $production->production_date->translatedFormat('j F Y') }}</dd></div>
            <div><dt>Status</dt><dd><x-admin::pill :label="$production->status->getLabel()" :warna="$production->status->getColor()" /></dd></div>
            <div><dt>Gabah</dt><dd>{{ $production->inputItem->name }}</dd></div>
            <div><dt>Berat gabah</dt><dd class="fw-semibold">@angka($production->input_kg) kg</dd></div>
            <div>
                <dt>Rendemen</dt>
                <dd><x-admin::pill :label="number_format((float) $production->yield_percent, 2, ',', '.').' %'" :warna="$production->isYieldOutOfRange() ? 'warning' : 'success'" /></dd>
            </div>
            <div><dt>Susut</dt><dd>@angka($production->shrinkage_kg) kg</dd></div>
            <div><dt>Mesin macet</dt><dd>{{ $production->machine_downtime_minutes !== null ? $production->machine_downtime_minutes.' menit' : '-' }}</dd></div>
            @if ($showCost)
                <div><dt>Biaya gabah / kg</dt><dd>Rp {{ number_format((float) $production->input_cost_per_kg, 2, ',', '.') }}</dd></div>
                <div><dt>Total biaya gabah</dt><dd>@rupiah($production->input_total_cost)</dd></div>
            @endif
            <div><dt>Dicatat oleh</dt><dd>{{ $production->user?->name ?? '-' }}</dd></div>
            <div><dt>Catatan</dt><dd>{{ $production->notes ?: '-' }}</dd></div>
        </dl>
    </x-admin::panel>

    <x-admin::panel judul="Hasil giling">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Barang</th>
                    <th class="angka">Berat</th>
                    @if ($showCost) <th class="angka">HPP / kg</th> @endif
                </tr>
                </thead>
                <tbody>
                @foreach ($production->outputs as $output)
                    <tr>
                        <td>{{ $output->item->name }}</td>
                        <td class="angka text-nowrap">@angka($output->quantity_kg) kg</td>
                        @if ($showCost) <td class="angka text-nowrap">Rp {{ number_format((float) $output->unit_cost, 2, ',', '.') }}</td> @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </x-admin::panel>
@endsection
