@extends('admin.layouts.app')

@section('judul', $expense->number)

@section('konten')
    <div class="judul-halaman">
        <div>
            <a href="{{ route('admin.expenses.index') }}" class="small">← Biaya operasional</a>
            <h1>{{ $expense->number }}</h1>
        </div>
        @if (! $expense->isVoid() && auth()->user()->can('void', $expense))
            <x-admin::void-modal :action="route('admin.expenses.void', $expense)" judul="Batalkan biaya {{ $expense->number }}" />
        @endif
    </div>

    <x-admin::void-info :document="$expense" />

    <x-admin::panel judul="Biaya operasional">
        <dl class="rincian">
            <div><dt>Tanggal</dt><dd>{{ $expense->expense_date->translatedFormat('j F Y') }}</dd></div>
            <div><dt>Kategori</dt><dd>{{ $expense->category->getLabel() }}</dd></div>
            <div><dt>Status</dt><dd><x-admin::pill :label="$expense->status->getLabel()" :warna="$expense->status->getColor()" /></dd></div>
            <div><dt>Jumlah</dt><dd class="fw-bold">@rupiah($expense->amount)</dd></div>
            <div><dt>Metode</dt><dd>{{ $expense->method?->getLabel() ?? '-' }}</dd></div>
            <div><dt>Dicatat oleh</dt><dd>{{ $expense->user?->name ?? '-' }}</dd></div>
        </dl>
        <div class="mt-3"><strong class="small text-ink-2 text-uppercase">Keterangan</strong><p class="mb-0">{{ $expense->description }}</p></div>
    </x-admin::panel>
@endsection
