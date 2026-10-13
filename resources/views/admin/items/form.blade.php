@extends('admin.layouts.app')

@section('judul', $item->exists ? 'Ubah barang' : 'Tambah barang')

@section('konten')
    <div class="judul-halaman">
        <h1>{{ $item->exists ? 'Ubah barang' : 'Tambah barang' }}</h1>
    </div>

    <form method="POST" style="max-width:560px"
          action="{{ $item->exists ? route('admin.items.update', $item) : route('admin.items.store') }}">
        @csrf
        @if ($item->exists) @method('PUT') @endif

        <x-admin::panel>
            <div class="row">
                <x-admin::field class="col-sm-4" label="Kode" name="code" :value="$item->code" maxlength="10" wajib />
                <x-admin::field class="col-sm-8" label="Nama barang" name="name" :value="$item->name" maxlength="255" wajib />
            </div>
            <x-admin::field label="Tipe" name="type" wajib>
                <select id="type" name="type" @class(['form-select', 'is-invalid' => $errors->has('type')])>
                    @foreach (App\Enums\ItemType::cases() as $type)
                        <option value="{{ $type->value }}" @selected(old('type', $item->type?->value) === $type->value)>{{ $type->getLabel() }}</option>
                    @endforeach
                </select>
            </x-admin::field>
            <x-admin::field label="Produk di katalog website" name="product_id" hint="Untuk menghubungkan barang ke halaman produk.">
                <select id="product_id" name="product_id" @class(['form-select', 'is-invalid' => $errors->has('product_id')])>
                    <option value="">— tidak ada —</option>
                    @foreach ($products as $id => $name)
                        <option value="{{ $id }}" @selected((string) old('product_id', $item->product_id) === (string) $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </x-admin::field>
            <div class="row">
                <x-admin::field class="col-sm-6" label="Berat per karung (kg)" name="weight_per_sack_kg" type="number" step="0.01" min="0.01"
                                :value="$item->weight_per_sack_kg" wajib />
                <x-admin::field class="col-sm-6" label="Batas minimum stok (kg)" name="min_stock_kg" type="number" step="0.01" min="0"
                                :value="$item->min_stock_kg" hint="Muncul peringatan di dashboard bila stok ≤ angka ini." />
            </div>
            <x-admin::field label="Harga pasar terbaru / kg (Rp)" name="default_price_per_kg" type="number" step="0.01" min="0"
                            :value="$item->default_price_per_kg" hint="Otomatis diperbarui dari transaksi terakhir. Dipakai untuk isi awal form." />
            <x-admin::active-check :checked="$item->is_active" hint="Barang nonaktif tidak muncul di pilihan transaksi." />
        </x-admin::panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ $item->exists ? route('admin.items.show', $item) : route('admin.items.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection
