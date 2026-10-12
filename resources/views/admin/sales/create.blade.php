@extends('admin.layouts.app')

@section('judul', 'Catat penjualan')

@php
    // Baris awal: dari old() setelah validasi gagal, atau satu baris kosong.
    $lines = old('items', [['item_id' => '', 'input_quantity' => '', 'input_unit' => 'sack', 'price_per_kg' => '']]);
@endphp

@section('konten')
    <div class="judul-halaman">
        <h1>Catat penjualan</h1>
    </div>

    @if ($items->isEmpty())
        <x-admin::alert nada="warn" judul="Belum ada stok untuk dijual">Catat produksi dulu supaya ada beras / hasil samping yang bisa dijual.</x-admin::alert>
    @endif

    <form method="POST" action="{{ route('admin.sales.store') }}" id="formPenjualan" style="max-width:860px">
        @csrf

        <x-admin::panel judul="1. Pembeli & tanggal">
            <div class="row">
                <x-admin::field class="col-sm-7" label="Pembeli" name="buyer_id" hint="Belum terdaftar? Isi nama pembeli baru di bawah.">
                    <select id="buyer_id" name="buyer_id" @class(['form-select', 'is-invalid' => $errors->has('buyer_id')])>
                        <option value="">— pilih pembeli —</option>
                        @foreach ($buyers as $buyer)
                            <option value="{{ $buyer->id }}" @selected((string) old('buyer_id') === (string) $buyer->id)>
                                {{ $buyer->name }}{{ $buyer->city ? ' ('.$buyer->city.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </x-admin::field>
                <x-admin::field class="col-sm-5" label="Tanggal" name="sale_date" type="date" :value="today()->toDateString()" :max="today()->toDateString()" wajib />
            </div>
            <div class="row">
                <x-admin::field class="col-sm-7" label="Nama pembeli baru" name="new_buyer_name" maxlength="255" />
                <x-admin::field class="col-sm-5" label="Kota pembeli baru" name="new_buyer_city" maxlength="255" />
            </div>
        </x-admin::panel>

        <x-admin::panel judul="2. Barang">
            <div id="daftarBarang">
                @foreach ($lines as $i => $line)
                    @include('admin.sales.partials.line', ['index' => $i, 'line' => $line])
                @endforeach
            </div>
            <template id="templatBaris">
                @include('admin.sales.partials.line', ['index' => '__INDEX__', 'line' => ['item_id' => '', 'input_quantity' => '', 'input_unit' => 'sack', 'price_per_kg' => '']])
            </template>
            <button type="button" class="btn btn-garis btn-sm" id="tambahBaris">+ Tambah barang</button>
        </x-admin::panel>

        <x-admin::panel judul="3. Total">
            <div class="row">
                <x-admin::field class="col-sm-4" label="Diskon (Rp)" name="discount" type="number" step="0.01" min="0" value="0" />
                <x-admin::field class="col-sm-4" label="Ongkir ditagihkan (Rp)" name="shipping_cost" type="number" step="0.01" min="0" value="0" />
                <div class="col-sm-4 mb-3">
                    <div class="form-label">Total</div>
                    <div class="fs-5 fw-bold angka">Rp <span id="pratinjauTotal">0</span></div>
                </div>
            </div>
        </x-admin::panel>

        <x-admin::panel judul="4. Pembayaran">
            <div class="row">
                <x-admin::field class="col-sm-4" label="Bayar sekarang (Rp)" name="pay_now" type="number" step="0.01" min="0" value="0" />
                <x-admin::field class="col-sm-4" label="Metode" name="payment_method">
                    <select id="payment_method" name="payment_method" class="form-select">
                        @foreach (App\Enums\PaymentMethod::cases() as $method)
                            <option value="{{ $method->value }}" @selected(old('payment_method', 'cash') === $method->value)>{{ $method->getLabel() }}</option>
                        @endforeach
                    </select>
                </x-admin::field>
                <x-admin::field class="col-sm-4" label="Jatuh tempo" name="due_date" type="date" :min="today()->toDateString()" hint="Wajib bila bayar kurang dari total." />
            </div>
            <x-admin::field label="Catatan" name="notes">
                <textarea id="notes" name="notes" rows="2" maxlength="1000" class="form-control">{{ old('notes') }}</textarea>
            </x-admin::field>
        </x-admin::panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary" @disabled($items->isEmpty())>Simpan penjualan</button>
            <a href="{{ route('admin.sales.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        // Pratinjau saja; total, HPP & stok dihitung ulang di SaleService.
        (() => {
            const f = document.getElementById('formPenjualan');
            const daftar = document.getElementById('daftarBarang');
            const templat = document.getElementById('templatBaris');
            const angka = (v) => parseFloat(v) || 0;
            const format = (v) => v.toLocaleString('id-ID', { maximumFractionDigits: 2 });
            let urutan = daftar.children.length;
            let bayarDiubah = f.pay_now.value !== '0';

            function hitung() {
                let subtotal = 0;
                const dipilih = [...daftar.querySelectorAll('select[data-barang]')].map(s => s.value);
                daftar.querySelectorAll('[data-baris]').forEach(baris => {
                    const pilih = baris.querySelector('select[data-barang]');
                    const opsi = pilih.selectedOptions[0];
                    const satuan = baris.querySelector('[data-satuan]').value;
                    const kg = angka(baris.querySelector('[data-jumlah]').value) * (satuan === 'sack' ? angka(opsi?.dataset.karung) : 1);
                    const total = Math.round(kg * angka(baris.querySelector('input[data-harga]').value) * 100) / 100;
                    subtotal += total;
                    baris.querySelector('[data-kg]').textContent = format(kg);
                    baris.querySelector('[data-total]').textContent = format(total);
                    // Barang yang sudah dipilih di baris lain tidak bisa dipilih lagi.
                    [...pilih.options].forEach(o => { o.disabled = o.value !== '' && o.value !== pilih.value && dipilih.includes(o.value); });
                });
                const total = Math.round((subtotal - angka(f.discount.value) + angka(f.shipping_cost.value)) * 100) / 100;
                document.getElementById('pratinjauTotal').textContent = format(total);
                if (!bayarDiubah) f.pay_now.value = Math.max(total, 0);
            }

            document.getElementById('tambahBaris').addEventListener('click', () => {
                daftar.insertAdjacentHTML('beforeend', templat.innerHTML.replaceAll('__INDEX__', urutan++));
                hitung();
            });
            daftar.addEventListener('click', (e) => {
                if (e.target.matches('[data-hapus]') && daftar.children.length > 1) {
                    e.target.closest('[data-baris]').remove();
                    hitung();
                }
            });
            daftar.addEventListener('change', (e) => {
                if (e.target.matches('select[data-barang]')) {
                    e.target.closest('[data-baris]').querySelector('input[data-harga]').value = e.target.selectedOptions[0]?.dataset.harga || '';
                }
            });
            f.pay_now.addEventListener('input', () => { bayarDiubah = true; });
            f.addEventListener('input', hitung);
            f.addEventListener('change', hitung);
            hitung();
        })();
    </script>
@endpush
