{{-- Tombol + modal "Catat Pembayaran" untuk pembelian (utang) atau penjualan (piutang). --}}
@props(['action', 'remaining', 'id' => 'modalBayar', 'small' => false])

<button type="button" @class(['btn btn-success', 'btn-sm' => $small]) data-bs-toggle="modal" data-bs-target="#{{ $id }}">Catat pembayaran</button>

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Judul" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ $action }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="{{ $id }}Judul">Catat pembayaran</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label for="{{ $id }}Tanggal" class="form-label">Tanggal bayar</label>
                        <input type="date" id="{{ $id }}Tanggal" name="payment_date" value="{{ today()->toDateString() }}" max="{{ today()->toDateString() }}" class="form-control" required>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label for="{{ $id }}Metode" class="form-label">Metode</label>
                        <select id="{{ $id }}Metode" name="method" class="form-select" required>
                            @foreach (App\Enums\PaymentMethod::cases() as $method)
                                <option value="{{ $method->value }}">{{ $method->getLabel() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <label for="{{ $id }}Jumlah" class="form-label">Jumlah (Rp)</label>
                <input type="number" id="{{ $id }}Jumlah" name="amount" value="{{ $remaining }}" min="0.01" max="{{ $remaining }}" step="0.01" class="form-control" required>
                <div class="form-text mb-3">Sisa tagihan: @rupiah($remaining)</div>
                <label for="{{ $id }}Catatan" class="form-label">Catatan <span class="text-ink-2 fw-normal small">(boleh kosong)</span></label>
                <textarea id="{{ $id }}Catatan" name="notes" rows="2" maxlength="1000" class="form-control"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-polos" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-success">Simpan pembayaran</button>
            </div>
        </form>
    </div>
</div>
