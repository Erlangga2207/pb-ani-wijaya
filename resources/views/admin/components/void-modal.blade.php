{{-- Tombol + modal "Batalkan" dokumen. Dokumen tidak dihapus: service membuat mutasi balik. Alasan wajib. --}}
@props(['action', 'id' => 'modalBatal', 'label' => 'Batalkan', 'judul' => 'Batalkan dokumen', 'small' => false])

<button type="button" @class(['btn btn-outline-danger', 'btn-sm' => $small]) data-bs-toggle="modal" data-bs-target="#{{ $id }}">{{ $label }}</button>

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Judul" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ $action }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="{{ $id }}Judul">{{ $judul }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="text-ink-2 small">Dokumen tidak dihapus, hanya ditandai dibatalkan. Stok & pembayaran dikembalikan lewat mutasi balik.</p>
                <label for="{{ $id }}Alasan" class="form-label">Alasan pembatalan</label>
                <textarea id="{{ $id }}Alasan" name="reason" rows="3" maxlength="1000" class="form-control" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-polos" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-danger">Ya, batalkan</button>
            </div>
        </form>
    </div>
</div>
