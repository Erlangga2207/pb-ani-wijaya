@extends('admin.layouts.app')

@section('judul', 'Produksi / Giling')

@section('konten')
    <div class="judul-halaman">
        <h1>Produksi / Giling</h1>
        <a href="{{ route('admin.productions.create') }}" class="btn btn-primary">Catat produksi</a>
    </div>

    <x-admin::search placeholder="Cari nomor">
        <x-admin::date-range />
        <select name="status" class="form-select" style="max-width:160px" onchange="this.form.submit()" aria-label="Status dokumen">
            <option value="">Semua dokumen</option>
            @foreach (App\Enums\DocumentStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->getLabel() }}</option>
            @endforeach
        </select>
    </x-admin::search>

    <x-admin::panel>
        <x-slot:aksi>
            <span class="small text-ink-2">
                Total tercatat: <strong class="angka text-body">gabah @angka($totals->input_kg) kg · beras @angka($totals->output_kg) kg</strong>
                · rendemen rata-rata tertimbang <strong class="angka text-body">{{ $weightedYield !== null ? number_format($weightedYield, 2, ',', '.').' %' : '-' }}</strong>
            </span>
        </x-slot:aksi>
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th class="angka">Gabah</th>
                    <th class="angka">Beras</th>
                    <th>Rendemen</th>
                    <th class="angka">Susut</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($productions as $production)
                    <tr class="baris-link">
                        <td class="kartu-judul">
                            <a href="{{ route('admin.productions.show', $production) }}" class="stretched-link text-reset text-decoration-none">{{ $production->number }}</a>
                        </td>
                        <td data-label="Tanggal" class="text-nowrap">{{ $production->production_date->translatedFormat('j M Y') }}</td>
                        <td data-label="Gabah" class="angka text-nowrap">@angka($production->input_kg) kg</td>
                        <td data-label="Beras" class="angka text-nowrap">@angka($production->main_output_kg) kg</td>
                        <td data-label="Rendemen">
                            <x-admin::pill :label="number_format((float) $production->yield_percent, 2, ',', '.').' %'" :warna="$production->isYieldOutOfRange() ? 'warning' : 'success'" />
                        </td>
                        <td data-label="Susut" class="angka text-nowrap">@angka($production->shrinkage_kg) kg</td>
                        <td class="kartu-status"><x-admin::pill :label="$production->status->getLabel()" :warna="$production->status->getColor()" /></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-ink-2 py-4 kartu-kosong">Belum ada produksi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $productions->links() }}
@endsection
