@extends('admin.layouts.app')

@section('judul', 'Catat stock opname')

@section('konten')
    <div class="judul-halaman">
        <h1>Catat stock opname</h1>
    </div>

    <form method="POST" action="{{ route('admin.stock-adjustments.store') }}" id="formOpname" style="max-width:640px">
        @csrf

        <x-admin::panel>
            <p class="text-ink-2 small">Catat hasil hitung fisik. Selisih dengan stok sistem otomatis menjadi mutasi masuk/keluar.</p>
            <div class="row">
                <x-admin::field class="col-sm-5" label="Tanggal" name="adjustment_date" type="date" :value="today()->toDateString()" :max="today()->toDateString()" wajib />
                <x-admin::field class="col-sm-7" label="Barang" name="item_id" wajib>
                    <select id="item_id" name="item_id" @class(['form-select', 'is-invalid' => $errors->has('item_id')])>
                        <option value="">— pilih barang —</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" data-stok="{{ $item->stock_kg }}" @selected((string) old('item_id') === (string) $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </x-admin::field>
            </div>
            <div class="row">
                <div class="col-4 mb-3">
                    <div class="form-label">Stok sistem</div>
                    <div class="fs-5 angka"><span id="stokSistem">0</span> kg</div>
                </div>
                <x-admin::field class="col-4" label="Stok fisik (kg)" name="physical_kg" type="number" step="0.01" min="0" wajib />
                <div class="col-4 mb-3">
                    <div class="form-label">Selisih</div>
                    <div class="fs-5 fw-bold angka"><span id="selisih">0</span> kg</div>
                </div>
            </div>
            <x-admin::field label="Alasan / penjelasan selisih" name="reason" wajib>
                <textarea id="reason" name="reason" rows="3" maxlength="1000" required @class(['form-control', 'is-invalid' => $errors->has('reason')])>{{ old('reason') }}</textarea>
            </x-admin::field>
        </x-admin::panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan opname</button>
            <a href="{{ route('admin.stock-adjustments.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        // Pratinjau saja; stok sistem final dibaca ulang (dengan lock) di StockAdjustmentService.
        (() => {
            const f = document.getElementById('formOpname');
            const angka = (v) => parseFloat(v) || 0;
            const format = (v) => v.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            function hitung() {
                const sistem = angka(f.item_id.selectedOptions[0]?.dataset.stok);
                const selisih = f.physical_kg.value === '' ? 0 : angka(f.physical_kg.value) - sistem;
                document.getElementById('stokSistem').textContent = format(sistem);
                const el = document.getElementById('selisih');
                el.textContent = (selisih > 0 ? '+' : '') + format(selisih);
                el.classList.toggle('teks-merah', selisih < 0);
            }
            f.addEventListener('input', hitung);
            f.addEventListener('change', hitung);
            hitung();
        })();
    </script>
@endpush
