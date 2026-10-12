@extends('admin.layouts.app')

@section('judul', 'Piutang Pembeli')

@php $companyName = $setting('company_name', 'PB. Ani Wijaya'); @endphp

@section('konten')
    <div class="judul-halaman">
        <h1>Piutang Pembeli</h1>
    </div>

    <x-admin::search placeholder="Cari nomor atau pembeli">
        <div class="form-check align-self-center">
            <input type="checkbox" id="overdue" name="overdue" value="1" class="form-check-input" @checked(request()->boolean('overdue')) onchange="this.form.submit()">
            <label for="overdue" class="form-check-label">Lewat jatuh tempo saja</label>
        </div>
    </x-admin::search>

    <x-admin::panel>
        <x-slot:aksi>
            <span class="small text-ink-2">Total piutang: <strong class="angka text-body">@rupiah($remaining)</strong></span>
        </x-slot:aksi>
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Pembeli</th>
                    <th class="angka">Total</th>
                    <th class="angka">Dibayar</th>
                    <th class="angka">Sisa tagihan</th>
                    <th>Jatuh tempo</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($documents as $sale)
                    @php
                        $reminder = $sale->whatsappUrl(sprintf(
                            "Yth. %s,\nkami mengingatkan tagihan nota %s tanggal %s sebesar Rp %s%s.\nTerima kasih.\n- %s",
                            $sale->buyer->name,
                            $sale->number,
                            $sale->sale_date->translatedFormat('d F Y'),
                            number_format($sale->remainingAmount(), 0, ',', '.'),
                            $sale->due_date ? ' (jatuh tempo '.$sale->due_date->translatedFormat('d F Y').')' : '',
                            $companyName,
                        ));
                    @endphp
                    <tr class="baris-link">
                        <td class="kartu-judul">
                            <a href="{{ route('admin.sales.show', $sale) }}" class="stretched-link text-reset text-decoration-none">{{ $sale->number }}</a>
                            <div class="small text-ink-2 fw-normal">{{ $sale->sale_date->translatedFormat('j M Y') }}</div>
                        </td>
                        <td data-label="Pembeli">
                            {{ $sale->buyer->name }}
                            @if ($sale->buyer->city) <div class="small text-ink-2">{{ $sale->buyer->city }}</div> @endif
                        </td>
                        <td data-label="Total" class="angka text-nowrap">@rupiah($sale->total)</td>
                        <td data-label="Dibayar" class="angka text-nowrap">@rupiah($sale->paid_amount)</td>
                        <td data-label="Sisa" class="angka text-nowrap fw-bold">@rupiah($sale->remainingAmount())</td>
                        <td data-label="Jatuh tempo" @class(['text-nowrap', 'teks-merah' => $sale->isOverdue()])>
                            {{ $sale->due_date?->translatedFormat('j M Y') ?? '-' }}
                            @if ($sale->isOverdue()) <div class="small">Lewat jatuh tempo</div> @endif
                        </td>
                        <td class="kartu-status"><x-admin::pill :label="$sale->payment_status->getLabel()" :warna="$sale->payment_status->getColor()" /></td>
                        <td class="text-end text-nowrap kartu-aksi">
                            @if ($reminder)
                                <a href="{{ $reminder }}" target="_blank" rel="noopener" class="btn btn-garis btn-sm">Ingatkan via WA</a>
                            @endif
                            <x-admin::payment-modal :action="route('admin.sales.payments.store', $sale)" :remaining="$sale->remainingAmount()"
                                                    :id="'bayar'.$sale->id" small />
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-ink-2 py-4 kartu-kosong">Tidak ada piutang pembeli.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $documents->links() }}
@endsection
