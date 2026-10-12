{{-- Riwayat pembayaran sebuah pembelian/penjualan, dengan tombol batalkan per pembayaran (owner). --}}
@props(['payments'])

<x-admin::panel judul="Pembayaran">
    <div class="table-responsive">
        <table class="table tabel-kartu">
            <thead>
            <tr>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th class="angka">Jumlah</th>
                <th>Metode</th>
                <th>Dicatat oleh</th>
                <th>Status</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($payments as $payment)
                <tr>
                    <td class="kartu-judul">{{ $payment->number }}</td>
                    <td data-label="Tanggal">{{ $payment->payment_date->translatedFormat('j M Y') }}</td>
                    <td data-label="Jumlah" class="angka text-nowrap">@rupiah($payment->amount)</td>
                    <td data-label="Metode">{{ $payment->method?->getLabel() ?? '-' }}</td>
                    <td data-label="Oleh">
                        {{ $payment->user?->name ?? '-' }}
                        @if ($payment->notes) <div class="small text-ink-2">{{ $payment->notes }}</div> @endif
                        @if ($payment->isVoid()) <div class="small teks-merah">Batal: {{ $payment->void_reason }}</div> @endif
                    </td>
                    <td class="kartu-status"><x-admin::pill :label="$payment->status->getLabel()" :warna="$payment->status->getColor()" /></td>
                    <td class="text-end kartu-aksi">
                        @if (! $payment->isVoid() && auth()->user()->can('void', $payment))
                            <x-admin::void-modal :action="route('admin.payments.void', $payment)" :id="'batalBayar'.$payment->id"
                                                 label="Batalkan" judul="Batalkan pembayaran {{ $payment->number }}" small />
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-ink-2 py-4 kartu-kosong">Belum ada pembayaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</x-admin::panel>
