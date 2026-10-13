@extends('admin.layouts.app')

@section('judul', $adjustment->number)

@section('konten')
    <div class="judul-halaman">
        <div>
            <a href="{{ route('admin.stock-adjustments.index') }}" class="small">← Stock opname</a>
            <h1>{{ $adjustment->number }}</h1>
        </div>
        @if (! $adjustment->isVoid() && auth()->user()->can('void', $adjustment))
            <x-admin::void-modal :action="route('admin.stock-adjustments.void', $adjustment)" judul="Batalkan opname {{ $adjustment->number }}" />
        @endif
    </div>

    <x-admin::void-info :document="$adjustment" />

    <x-admin::panel judul="Stock opname">
        <dl class="rincian">
            <div><dt>Tanggal</dt><dd>{{ $adjustment->adjustment_date->translatedFormat('j F Y') }}</dd></div>
            <div><dt>Status</dt><dd><x-admin::pill :label="$adjustment->status->getLabel()" :warna="$adjustment->status->getColor()" /></dd></div>
            <div><dt>Barang</dt><dd>{{ $adjustment->item->name }}</dd></div>
            <div><dt>Stok sistem</dt><dd>@angka($adjustment->system_kg) kg</dd></div>
            <div><dt>Stok fisik</dt><dd>@angka($adjustment->physical_kg) kg</dd></div>
            <div>
                <dt>Selisih</dt>
                <dd @class(['fw-bold', 'teks-merah' => (float) $adjustment->difference_kg < 0])>{{ (float) $adjustment->difference_kg > 0 ? '+' : '' }}@angka($adjustment->difference_kg) kg</dd>
            </div>
            <div><dt>Dicatat oleh</dt><dd>{{ $adjustment->user?->name ?? '-' }}</dd></div>
            <div><dt>Waktu input</dt><dd>{{ $adjustment->created_at->translatedFormat('j M Y H:i') }}</dd></div>
        </dl>
        <div class="mt-3"><strong class="small text-ink-2 text-uppercase">Alasan</strong><p class="mb-0">{{ $adjustment->reason }}</p></div>
    </x-admin::panel>
@endsection
