@extends('admin.layouts.app')

@section('judul', 'Catat pembelian gabah')

@php $defaultItem = $items->firstWhere('code', 'GBH') ?? $items->first(); @endphp

@section('konten')
    <div class="judul-halaman">
        <h1>Catat pembelian gabah</h1>
    </div>

    <form method="POST" action="{{ route('admin.purchases.store') }}" id="formPembelian" style="max-width:720px">
        @csrf

        <x-admin::panel judul="1. Petani & tanggal">
            <div class="row">
                <x-admin::field class="col-sm-7" label="Petani / pemasok" name="supplier_id" hint="Belum terdaftar? Isi nama petani baru di bawah.">
                    <select id="supplier_id" name="supplier_id" @class(['form-select', 'is-invalid' => $errors->has('supplier_id')])>
                        <option value="">— pilih petani —</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected((string) old('supplier_id') === (string) $supplier->id)>
                                {{ $supplier->name }}{{ $supplier->village ? ' ('.$supplier->village.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </x-admin::field>
                <x-admin::field class="col-sm-5" label="Tanggal" name="purchase_date" type="date" :value="today()->toDateString()" :max="today()->toDateString()" wajib />
            </div>
            <div class="row">
                <x-admin::field class="col-sm-7" label="Nama petani baru" name="new_supplier_name" maxlength="255" />
                <x-admin::field class="col-sm-5" label="Desa petani baru" name="new_supplier_village" maxlength="255" />
            </div>
        </x-admin::panel>

        <x-admin::panel judul="2. Jumlah gabah">
            <div class="row">
                <x-admin::field class="col-sm-6" label="Barang" name="item_id" wajib>
                    <select id="item_id" name="item_id" @class(['form-select', 'is-invalid' => $errors->has('item_id')])>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" data-karung="{{ $item->weight_per_sack_kg }}" data-harga="{{ $item->default_price_per_kg }}"
                                    @selected((string) old('item_id', $defaultItem?->id) === (string) $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </x-admin::field>
                <x-admin::field class="col-sm-6" label="Kadar air (%)" name="moisture_percent" type="number" step="0.01" min="0" max="100" />
            </div>
            <div class="row">
                <x-admin::field class="col-6 col-sm-4" label="Jumlah" name="input_quantity" type="number" step="0.01" min="0.01" wajib />
                <x-admin::field class="col-6 col-sm-4" label="Satuan" name="input_unit" wajib>
                    <select id="input_unit" name="input_unit" class="form-select">
                        @foreach (App\Enums\QuantityUnit::cases() as $unit)
                            <option value="{{ $unit->value }}" @selected(old('input_unit', 'kg') === $unit->value)>{{ $unit->getLabel() }}</option>
                        @endforeach
                    </select>
                </x-admin::field>
                <div class="col-sm-4 mb-3">
                    <div class="form-label">Total berat</div>
                    <div class="fs-5 fw-bold angka"><span id="pratinjauKg">0</span> kg</div>
                </div>
            </div>
        </x-admin::panel>

        <x-admin::panel judul="3. Harga">
            <div class="row">
                <x-admin::field class="col-sm-4" label="Harga / kg (Rp)" name="price_per_kg" type="number" step="0.01" min="0.01" :value="$defaultItem?->default_price_per_kg" wajib />
                <x-admin::field class="col-sm-4" label="Ongkos lain (Rp)" name="other_cost" type="number" step="0.01" min="0" value="0" hint="Angkut / bongkar." />
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
            <button type="submit" class="btn btn-primary">Simpan pembelian</button>
            <a href="{{ route('admin.purchases.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        // Pratinjau saja; angka final dihitung ulang di PurchaseService.
        (() => {
            const f = document.getElementById('formPembelian');
            const angka = (v) => parseFloat(v) || 0;
            const format = (v) => v.toLocaleString('id-ID', { maximumFractionDigits: 2 });
            let bayarDiubah = f.pay_now.value !== '0';

            function hitung() {
                const opsi = f.item_id.selectedOptions[0];
                const kg = angka(f.input_quantity.value) * (f.input_unit.value === 'sack' ? angka(opsi?.dataset.karung) : 1);
                const total = Math.round((kg * angka(f.price_per_kg.value) + angka(f.other_cost.value)) * 100) / 100;
                document.getElementById('pratinjauKg').textContent = format(kg);
                document.getElementById('pratinjauTotal').textContent = format(total);
                if (!bayarDiubah) f.pay_now.value = total; // default: bayar lunas
            }

            f.item_id.addEventListener('change', () => { f.price_per_kg.value = f.item_id.selectedOptions[0]?.dataset.harga || ''; hitung(); });
            f.pay_now.addEventListener('input', () => { bayarDiubah = true; });
            f.addEventListener('input', hitung);
            f.addEventListener('change', hitung);
            hitung();
        })();
    </script>
@endpush
