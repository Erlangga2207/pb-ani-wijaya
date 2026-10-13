@extends('admin.layouts.app')

@section('judul', 'Biaya Operasional')

@section('konten')
    <div class="judul-halaman">
        <h1>Biaya Operasional</h1>
        <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary">Catat biaya</a>
    </div>

    <x-admin::search placeholder="Cari nomor atau keterangan">
        <x-admin::date-range />
        <select name="category" class="form-select" style="max-width:190px" onchange="this.form.submit()" aria-label="Kategori">
            <option value="">Semua kategori</option>
            @foreach (App\Enums\ExpenseCategory::cases() as $category)
                <option value="{{ $category->value }}" @selected(request('category') === $category->value)>{{ $category->getLabel() }}</option>
            @endforeach
        </select>
        <select name="status" class="form-select" style="max-width:160px" onchange="this.form.submit()" aria-label="Status dokumen">
            <option value="">Semua dokumen</option>
            @foreach (App\Enums\DocumentStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->getLabel() }}</option>
            @endforeach
        </select>
    </x-admin::search>

    <x-admin::panel>
        <x-slot:aksi>
            <span class="small text-ink-2">Total tercatat: <strong class="angka text-body">@rupiah($total)</strong></span>
        </x-slot:aksi>
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th class="angka">Jumlah</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($expenses as $expense)
                    <tr class="baris-link">
                        <td class="kartu-judul">
                            <a href="{{ route('admin.expenses.show', $expense) }}" class="stretched-link text-reset text-decoration-none">{{ $expense->number }}</a>
                        </td>
                        <td data-label="Tanggal" class="text-nowrap">{{ $expense->expense_date->translatedFormat('j M Y') }}</td>
                        <td data-label="Kategori">{{ $expense->category->getLabel() }}</td>
                        <td data-label="Ket.">{{ Str::limit($expense->description, 40) }}</td>
                        <td data-label="Jumlah" class="angka text-nowrap">@rupiah($expense->amount)</td>
                        <td class="kartu-status"><x-admin::pill :label="$expense->status->getLabel()" :warna="$expense->status->getColor()" /></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-ink-2 py-4 kartu-kosong">Belum ada biaya operasional.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $expenses->links() }}
@endsection
