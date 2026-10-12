@extends('admin.layouts.app')

@section('judul', 'Utang ke Petani')

@section('konten')
    <div class="judul-halaman">
        <h1>Utang ke Petani</h1>
    </div>

    <x-admin::search placeholder="Cari nomor atau petani">
        <div class="form-check align-self-center">
            <input type="checkbox" id="overdue" name="overdue" value="1" class="form-check-input" @checked(request()->boolean('overdue')) onchange="this.form.submit()">
            <label for="overdue" class="form-check-label">Lewat jatuh tempo saja</label>
        </div>
    </x-admin::search>

    <x-admin::panel>
        <x-slot:aksi>
            <span class="small text-ink-2">Total utang: <strong class="angka text-body">@rupiah($remaining)</strong></span>
        </x-slot:aksi>
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Petani</th>
                    <th class="angka">Total</th>
                    <th class="angka">Dibayar</th>
                    <th class="angka">Sisa utang</th>
                    <th>Jatuh tempo</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($documents as $purchase)
                    <tr class="baris-link">
                        <td class="kartu-judul">
                            <a href="{{ route('admin.purchases.show', $purchase) }}" class="stretched-link text-reset text-decoration-none">{{ $purchase->number }}</a>
                            <div class="small text-ink-2 fw-normal">{{ $purchase->purchase_date->translatedFormat('j M Y') }}</div>
                        </td>
                        <td data-label="Petani">
                            {{ $purchase->supplier->name }}
                            @if ($purchase->supplier->village) <div class="small text-ink-2">{{ $purchase->supplier->village }}</div> @endif
                        </td>
                        <td data-label="Total" class="angka text-nowrap">@rupiah($purchase->total)</td>
                        <td data-label="Dibayar" class="angka text-nowrap">@rupiah($purchase->paid_amount)</td>
                        <td data-label="Sisa" class="angka text-nowrap fw-bold">@rupiah($purchase->remainingAmount())</td>
                        <td data-label="Jatuh tempo" @class(['text-nowrap', 'teks-merah' => $purchase->isOverdue()])>
                            {{ $purchase->due_date?->translatedFormat('j M Y') ?? '-' }}
                            @if ($purchase->isOverdue()) <div class="small">Lewat jatuh tempo</div> @endif
                        </td>
                        <td class="kartu-status"><x-admin::pill :label="$purchase->payment_status->getLabel()" :warna="$purchase->payment_status->getColor()" /></td>
                        <td class="text-end kartu-aksi">
                            <x-admin::payment-modal :action="route('admin.purchases.payments.store', $purchase)" :remaining="$purchase->remainingAmount()"
                                                    :id="'bayar'.$purchase->id" small />
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-ink-2 py-4 kartu-kosong">Tidak ada utang ke petani.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $documents->links() }}
@endsection
