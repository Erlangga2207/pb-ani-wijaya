@extends('admin.layouts.app')

@section('judul', 'Dashboard')

@php
    $kg = fn ($v) => number_format((float) $v, 0, ',', '.').' kg';
    $rp = fn ($v) => 'Rp '.number_format((float) $v, 0, ',', '.');
    // Halaman detail transaksi pindah ke /panel di Fase 3; sampai itu tautan ke panel lama.
    $docUrl = fn (string $resource, $record) => Route::has("admin.$resource.show")
        ? route("admin.$resource.show", $record)
        : route("filament.admin.resources.$resource.view", ['record' => $record]);
@endphp

@section('konten')
    <div class="judul-halaman">
        <div>
            <h1>Halo, {{ auth()->user()->name }}</h1>
            <div class="text-ink-2">{{ today()->translatedFormat('l, j F Y') }}</div>
        </div>
    </div>

    <h2 class="h6 fw-bold text-ink-2 text-uppercase mb-2">Stok saat ini</h2>
    <div class="row g-3 mb-4">
        @foreach ($items as $item)
            <div class="col-6 col-lg-3">
                <x-admin::tile :label="$item->name" :nilai="$kg($item->stock_kg)"
                               :sub="'≈ '.number_format($item->stockInSacks(), 1, ',', '.').' karung'.($item->isLowStock() ? ' · di bawah minimum' : '')"
                               :nada="$item->isLowStock() ? 'merah' : null" />
            </div>
        @endforeach
    </div>

    <h2 class="h6 fw-bold text-ink-2 text-uppercase mb-2">Aktivitas hari ini</h2>
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4"><x-admin::tile label="Gabah dibeli" :nilai="$kg($today['bought'])" /></div>
        <div class="col-12 col-sm-4"><x-admin::tile label="Gabah digiling" :nilai="$kg($today['milled'])" /></div>
        <div class="col-12 col-sm-4"><x-admin::tile label="Beras terjual" :nilai="$kg($today['sold'])" /></div>
    </div>

    @if ($finance)
        <h2 class="h6 fw-bold text-ink-2 text-uppercase mb-2">Keuangan bulan ini</h2>
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg"><x-admin::tile label="Omzet" :nilai="$rp($finance['revenue'])" /></div>
            <div class="col-6 col-lg"><x-admin::tile label="Laba Kotor" :nilai="$rp($finance['gross_profit'])" /></div>
            <div class="col-6 col-lg"><x-admin::tile label="Laba Bersih" :nilai="$rp($finance['net_profit'])" :nada="$finance['net_profit'] < 0 ? 'merah' : 'hijau'" /></div>
            <div class="col-6 col-lg"><x-admin::tile label="Piutang Pembeli" :nilai="$rp($finance['receivables'])" nada="amber" /></div>
            <div class="col-12 col-lg"><x-admin::tile label="Utang ke Petani" :nilai="$rp($finance['payables'])" nada="amber" /></div>
        </div>
    @endif

    <div class="row g-4">
        <div @class(['col-12', 'col-lg-6' => $salesTrend])>
            <x-admin::panel judul="Rendemen per Batch (30 hari)">
                @if ($yieldTrend['values'])
                    <canvas id="grafikRendemen" height="220" aria-label="Grafik rendemen per batch" role="img"></canvas>
                @else
                    <p class="text-ink-2 mb-0">Belum ada produksi dalam 30 hari terakhir.</p>
                @endif
            </x-admin::panel>
        </div>
        @if ($salesTrend)
            <div class="col-12 col-lg-6">
                <x-admin::panel judul="Omzet 12 Bulan Terakhir">
                    <canvas id="grafikOmzet" height="220" aria-label="Grafik omzet 12 bulan" role="img"></canvas>
                </x-admin::panel>
            </div>
        @endif
    </div>

    @if ($overdue)
        <x-admin::panel judul="Lewat Jatuh Tempo">
            <div class="row g-4">
                @foreach (['Piutang Pembeli' => ['sales', $overdue['receivables'], 'buyer'], 'Utang ke Petani' => ['purchases', $overdue['payables'], 'supplier']] as $title => [$resource, $docs, $party])
                    <div class="col-12 col-md-6">
                        <h3 class="h6 fw-bold">{{ $title }}</h3>
                        @forelse ($docs as $doc)
                            <a href="{{ $docUrl($resource, $doc) }}" class="daftar-baris">
                                <span>
                                    {{ $doc->number }} · {{ $doc->{$party}->name }}<br>
                                    <span class="small teks-merah">Jatuh tempo {{ $doc->due_date->translatedFormat('j M Y') }}</span>
                                </span>
                                <strong class="angka text-nowrap">{{ $rp($doc->remainingAmount()) }}</strong>
                            </a>
                        @empty
                            <p class="text-ink-2 small mb-0">Tidak ada yang lewat jatuh tempo.</p>
                        @endforelse
                    </div>
                @endforeach
            </div>
        </x-admin::panel>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"
            integrity="sha384-vsrfeLOOY6KuIYKDlmVH5UiBmgIdB1oEf7p01YgWHuqmOHfZr374+odEv96n9tNC" crossorigin="anonymous"></script>
    <script>
        Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
        const merah = '#BA0013', emas = '#E8C404';

        const rendemen = @json($yieldTrend);
        const elRendemen = document.getElementById('grafikRendemen');
        if (elRendemen) {
            const batas = (nilai) => rendemen.labels.map(() => nilai);
            new Chart(elRendemen, {
                type: 'line',
                data: {
                    labels: rendemen.labels,
                    datasets: [
                        { label: 'Rendemen (%)', data: rendemen.values, borderColor: merah, backgroundColor: merah },
                        { label: 'Batas bawah', data: batas(@json((float) config('production.yield_min'))), borderColor: emas, borderDash: [6, 4], pointRadius: 0 },
                        { label: 'Batas atas', data: batas(@json((float) config('production.yield_max'))), borderColor: emas, borderDash: [6, 4], pointRadius: 0 },
                    ],
                },
            });
        }

        @if ($salesTrend)
            const omzet = @json($salesTrend);
            new Chart(document.getElementById('grafikOmzet'), {
                type: 'bar',
                data: { labels: omzet.labels, datasets: [{ label: 'Omzet (Rp)', data: omzet.values, backgroundColor: merah }] },
                options: { scales: { y: { ticks: { callback: (v) => 'Rp ' + v.toLocaleString('id-ID') } } } },
            });
        @endif
    </script>
@endpush
