@extends('admin.layouts.app')

@section('judul', 'Laporan '.$tabs[$tab])

@section('konten')
    <div class="judul-halaman">
        <div>
            <h1>Laporan</h1>
            <div class="text-ink-2">{{ $from->translatedFormat('j M Y') }} – {{ $to->translatedFormat('j M Y') }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ $printUrl }}" target="_blank" rel="noopener" class="btn btn-garis">Cetak</a>
            <a href="{{ $csvUrl }}" class="btn btn-primary">Unduh CSV</a>
        </div>
    </div>

    <form method="GET" class="d-flex flex-wrap gap-2 mb-3" id="formPeriode">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <select name="preset" class="form-select" style="max-width:200px" aria-label="Periode" onchange="this.form.submit()">
            @foreach (App\Services\ReportService::PRESETS as $key => $label)
                <option value="{{ $key }}" @selected($preset === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @if ($preset === 'custom')
            <input type="date" name="from" value="{{ $from->toDateString() }}" class="form-control" style="max-width:170px" aria-label="Dari tanggal">
            <input type="date" name="to" value="{{ $to->toDateString() }}" class="form-control" style="max-width:170px" aria-label="Sampai tanggal">
            <button type="submit" class="btn btn-garis">Tampilkan</button>
        @endif
    </form>

    <nav class="tab-garis" aria-label="Jenis laporan">
        @foreach ($tabs as $key => $label)
            <a href="{{ route('admin.reports.index', ['tab' => $key] + $period) }}" @class(['aktif' => $tab === $key]) @if ($tab === $key) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </nav>

    <x-admin::panel>
        <div class="table-responsive">
            @include('reports.sections', ['sections' => $sections])
        </div>
    </x-admin::panel>
@endsection
